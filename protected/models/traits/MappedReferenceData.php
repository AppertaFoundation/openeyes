<?php

/**
 * Trait MappedReferenceData
 */
trait MappedReferenceData
{
    /**
     * Gets all supported levels.
     * @return int a Bitwise value representing the supported mapping levels.
     */
    abstract protected function getSupportedLevels(): int;

    /**
     * Gets the name of the ID column representing the reference data in the mapping table.
     * @param int $level The level used for mapping.
     * @return string The name of the reference data ID column in the mapping table.
     */
    abstract protected function mappingColumn(int $level): string;

    /**
     * Override this function to enable soft deletion.
     * @return bool|false
     */
    public function softDeleteMappings(): bool
    {
        // This has to be an overridable function as you cannot override property values within a trait.
        return false;
    }

    /**
     * Gets the name of the level relation property.
     * Only override this if the relation name does not follow the pattern "{level}s"
     * @param $level
     * @return string
     */
    protected function levelRelationProperty(int $level): string
    {
        return $this->getModelSuffixForLevel($level) . 's';
    }

    /**
     * Gets the name of the ID column representing the level.
     * Override this function if the column name in the mapping table doesn't follow the pattern "{level}_id".
     * @param int $level
     * @return string
     */
    protected function levelIdColumn(int $level): string
    {
        return $this->getModelSuffixForLevel($level) . '_id';
    }

    /**
     * Gets the name of the model for the mapping class.
     * Override this function if the mapping model class name does not follow the pattern {referencedataclass}_{level}
     * @param $level int The level
     * @return string
     */
    protected function mappingModelName(int $level): string
    {
        return __CLASS__ . '_' . ucfirst($this->getModelSuffixForLevel($level));
    }

    /**
     * Gets the mapping model suffix for the specified level
     * @param int $level
     * @return string
     */
    public function getModelSuffixForLevel(int $level): string
    {
        if ($this->getSupportedLevels() & $level) {
            switch ($level) {
                case ReferenceData::LEVEL_USER:
                    return 'user';
                case ReferenceData::LEVEL_FIRM:
                    return 'firm';
                case ReferenceData::LEVEL_SUBSPECIALTY:
                    return 'subspecialty';
                case ReferenceData::LEVEL_SPECIALTY:
                    return 'specialty';
                case ReferenceData::LEVEL_SITE:
                    return 'site';
                default:
                    return 'institution';
            }
        }
        throw new InvalidArgumentException('Class does not support specified level.');
    }

    /**
     * Gets all model instances at the level specified
     * @param int $level
     * @return array
     */
    public function findAllAtLevel(int $level, $criteria = null): array
    {
        $levelCriteria = $this->buildCriteriaForFindAllAtLevel($level);

        if (isset($criteria)) {
            $levelCriteria->mergeWith($criteria);
        }

        return static::model()->findAll($levelCriteria);
    }

    /**
     * This abstraction allows to extend the criteria for findAllAtLevel for models
     * that have additional functionality (cf Medication which only applies the mappings
     * on local instances)
     */
    protected function buildCriteriaForFindAllAtLevel(int $level)
    {
        $criteria = new CDbCriteria();

        $mapping_level_column_name = $this->levelIdColumn($level);
        $level_id = $this->getIdForLevel($level);
        $mapping_model = $this->mappingModelName($level)::model();

        if ((int) $mapping_model->countByAttributes([$mapping_level_column_name => $level_id]) === 0) {
            // no instances mapped at this level, so no filtering to be done
            return $criteria;
        }

        $mapping_data_column_name = $this->mappingColumn($level);
        $mapping_model_table = $mapping_model->tableName();
        $criteria->addCondition("t.id in (SELECT {$mapping_data_column_name} FROM {$mapping_model_table} WHERE $mapping_level_column_name = :_ref_level_id)");
        $criteria->params[':_ref_level_id'] = $level_id;

        return $criteria;
    }

    public function getIdForLevel(int $level): int
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty = $firm ? $firm->serviceSubspecialtyAssignment->subspecialty : null;
        $specialty = $subspecialty ? $subspecialty->specialty : null;
        $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

        if ($this->getSupportedLevels() & $level) {
            switch ($level) {
                case ReferenceData::LEVEL_USER:
                    return Yii::app()->session['user']->id;
                case ReferenceData::LEVEL_FIRM:
                    if (!isset($firm)) {
                        throw new Exception("No applicable firm exists");
                    }
                    return $firm->id;
                case ReferenceData::LEVEL_SUBSPECIALTY:
                    if (!isset($subspecialty)) {
                        throw new Exception("No applicable subspecialty exists");
                    }
                    return $subspecialty->id;
                case ReferenceData::LEVEL_SPECIALTY:
                    if (!isset($specialty)) {
                        throw new Exception("No applicable specialty exists");
                    }
                    return $specialty->id;
                case ReferenceData::LEVEL_SITE:
                    if (!isset($site)) {
                        throw new Exception("No applicable site exists");
                    }
                    return $site->id;
                default:
                    return Institution::model()->getCurrent()->id;
            }
        }
        throw new InvalidArgumentException('Class does not support specified level.');
    }

    /**
     * Determines if the reference data has a mapping to the specified Level ID at the specified level.
     * @param int $level The level to check for an existing mapping.
     * @param int $id Level ID
     * @return bool True if a mapping exists; otherwise false.
     * @uses MappedReferenceData::levelRelationProperty()
     */
    public function hasMapping(int $level, int $id): bool
    {
        if ($this->getSupportedLevels() & $level) {
            $suffix = $this->levelRelationProperty($level);
            foreach ($this->$suffix ?? [] as $model) {
                if ((int)$model->id === $id) {
                    if (($this->softDeleteMappings() && $model->active)
                    || !$this->softDeleteMappings()) {
                        return true;
                    }
                }
            }
            return false;
        }
        throw new InvalidArgumentException('Specified level is not supported by this class.');
    }

    /**
     * Create a mapping for a single level ID
     * @param int $level The level at which to create the mapping.
     * @param int|string $level_id Level ID
     * @return bool True if the mapping was saved successfully; otherwise false
     * @uses MappedReferenceData::mappingModelName()
     * @uses MappedReferenceData::mappingColumn()
     * @uses MappedReferenceData::levelIdColumn()
     */
    public function createMapping(int $level, $level_id): bool
    {
        // Do not duplicate mapping if it already exists.
        if ($this->getSupportedLevels() & $level) {
            $model = $this->mappingModelName($level);
            $model_func = $model . '::model';
            $reference_data_column = $this->mappingColumn($level);
            $level_column = $this->levelIdColumn($level);

            if ((int)$model_func()->count(
                "$reference_data_column = :reference_data_id AND $level_column = :level_id",
                array(':reference_data_id' => $this->id, ':level_id' => $level_id)
            ) > 0) {
                throw new RuntimeException('Mapping already exists for the specified level ID.');
            }

            $instance = new $model();
            $instance->$reference_data_column = $this->id;
            $instance->$level_column = $level_id;

            if (!$instance->save()) {
                $this->addErrors($instance->getErrors());
                return false;
            }
            return true;
        }
        throw new InvalidArgumentException('Specified level is not supported by this class.');
    }

    /**
     * @param int $level The level at which to create the mappings.
     * @param int[]|string[] $level_ids An array of Level IDs
     * @return bool True if all mappings saved successfully; otherwise false
     * @uses MappedReferenceData::mappingModelName()
     * @uses MappedReferenceData::mappingColumn()
     * @uses MappedReferenceData::levelIdColumn()
     */
    public function createMappings(int $level, array $level_ids): bool
    {
        // Do not duplicate mapping if it already exists.
        if ($this->getSupportedLevels() & $level) {
            $model = $this->mappingModelName($level);
            $model_func = $model . '::model';
            $reference_data_column = $this->mappingColumn($level);
            $level_column = $this->levelIdColumn($level);

            $saved = true;
            $transaction = Yii::app()->db->beginTransaction();
            try {
                foreach ($level_ids as $level_id) {
                    if ($model_func()->exists(
                        "$reference_data_column = :reference_data_id AND $level_column = :level_id",
                        array(':reference_data_id' => $this->id, ':level_id' => $level_id)
                    )) {
                        continue;
                    }
                    $instance = new $model();
                    $instance->$reference_data_column = $this->id;
                    $instance->$level_column = $level_id;

                    if (!$instance->save()) {
                        $this->addErrors($instance->getErrors());
                        $saved = false;
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                $saved = false;
            }

            if ($saved) {
                $transaction->commit();
            } else {
                $transaction->rollback();
            }
            return $saved;
        }
        throw new InvalidArgumentException('Specified level is not supported by this class.');
    }

    /**
     * @param int $level The level at which to delete the mapping.
     * @param int $level_id Level ID
     * @return bool True if the mapping was successfully deleted; otherwise false.
     * @uses MappedReferenceData::mappingModelName()
     * @uses MappedReferenceData::mappingColumn()
     * @uses MappedReferenceData::levelIdColumn()
     */
    public function deleteMapping(int $level, $level_id): bool
    {
        if ($this->getSupportedLevels() & $level) {
            // If mapping doesn't exist, create an inactive mapping.
            $model = $this->mappingModelName($level);
            $model_instance_func = $model . '::model';
            $reference_data_column = $this->mappingColumn($level);
            $level_column = $this->levelIdColumn($level);
            $instance = $model_instance_func()->find(
                "$reference_data_column = :reference_data_id AND $level_column = :level_id",
                array(":reference_data_id" => $this->id, ':level_id' => $level_id)
            );

            if (!$instance) {
                if ($this->softDeleteMappings()) {
                    $instance = new $model();
                    $instance->$reference_data_column = $this->id;
                    $instance->$level_column = $level_id;
                    $instance->active = false;
                    if (!$instance->save()) {
                        $this->addErrors($instance->getErrors());
                        return false;
                    }
                }
                // if no instance found, then there is no need to delete
                return true;
            }

            if ($this->softDeleteMappings()) {
                $instance->active = false;
                if (!$instance->save()) {
                    $this->addErrors($instance->getErrors());
                    return false;
                }
                return true;
            }
            if (!$instance->delete()) {
                $this->addErrors($instance->getErrors());
                return false;
            }
            return true;
        }
        throw new InvalidArgumentException('Specified level is not supported by this class.');
    }

    /**
     * Remap the reference data mappings at the specified level of this reference data point
     * to a different reference data point.
     * @param int $level The level at which to remap existing mappings.
     * @param int $new_reference_data_id The ID of the new reference data model.
     * @return bool True if all mappings were successfully remapped; otherwise false.
     */
    public function remapMappings(int $level, int $new_reference_data_id): bool
    {
        if ($this->getSupportedLevels() & $level) {
            $model_instance_func = $this->mappingModelName($level) . '::model';
            $reference_data_column = $this->mappingColumn($level);
            $instances = $model_instance_func()->findAll(
                "$reference_data_column = :reference_data_id",
                array(":reference_data_id" => $this->id)
            );
            $saved = true;
            $transaction = Yii::app()->db->beginTransaction();
            try {
                foreach ($instances as $instance) {
                    $instance->$reference_data_column = $new_reference_data_id;
                    if (!$instance->save()) {
                        $this->addErrors($instance->getErrors());
                        $saved = false;
                    }
                }
            } catch (Exception $e) {
                $saved = false;
            }

            if ($saved) {
                $transaction->commit();
            } else {
                $transaction->rollback();
            }

            return $saved;
        }
        throw new InvalidArgumentException('Specified level is not supported by this class.');
    }

    /**
     * @param int $level The level at which to remove all mappings.
     * @return bool True if all mappings were successfully deleted; otherwise false.
     * @uses MappedReferenceData::mappingModelName()
     * @uses MappedReferenceData::mappingColumn()
     * @uses MappedReferenceData::levelIdColumn()
     */
    public function deleteMappings(int $level): bool
    {
        if ($this->getSupportedLevels() & $level) {
            $model_instance_func = $this->mappingModelName($level) . '::model';
            $reference_data_column = $this->mappingColumn($level);
            $instances = $model_instance_func()->findAll(
                "$reference_data_column = :reference_data_id",
                 array(":reference_data_id" => $this->id)
            );

            if (empty($instances)) {
                // if no instance found, then there is no need to delete
                return true;
            }

            if ($this->softDeleteMappings()) {
                // Soft-delete all mappings.
                $transaction = Yii::app()->db->beginTransaction();
                $saved = true;
                try {
                    foreach ($instances as $instance) {
                        $instance->active = false;
                        if (!$instance->save()) {
                            $this->addErrors($instance->getErrors());
                            $saved = false;
                        }
                    }
                } catch (Exception $e) {
                    $saved = false;
                }
                if ($saved) {
                    $transaction->commit();
                } else {
                    $transaction->rollback();
                }
                return $saved;
            }
            // Bulk-delete all associated mappings.
            return $model_instance_func()->deleteAll(
                "$reference_data_column = :reference_data_id",
                array(":reference_data_id" => $this->id)
            );
        }
        throw new InvalidArgumentException('Specified level is not supported by this class.');
    }

    /**
     * @return array Array of integers corresponding to ReferenceData class constants denoting supported mapping levels.
     */
    public function enumerateSupportedLevels(): array
    {
        $supported_levels = $this->getSupportedLevels();

        $enumerated_supported_levels = array();

        foreach (ReferenceData::ALL_LEVELS as $level) {
            if ($this->getSupportedLevels() & $level) {
                $enumerated_supported_levels[] = $level;
            }
        }

        return $enumerated_supported_levels;
    }
}
