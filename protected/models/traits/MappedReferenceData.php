<?php

/**
 * Trait MappedReferenceData
 */
trait MappedReferenceData
{
    /**
     * Returns a mapping table to map setting levels to common column names. This can be overridden if any of these are different.
     *
     * @var array
     */
    protected function levelColumns()
    {
        return array(
            ReferenceData::LEVEL_INSTALLATION => 'installation',
            ReferenceData::LEVEL_INSTITUTION => 'institution_id',
            ReferenceData::LEVEL_SITE => 'site_id',
            ReferenceData::LEVEL_SPECIALTY => 'specialty_id',
            ReferenceData::LEVEL_SUBSPECIALTY => 'subspecialty_id',
            ReferenceData::LEVEL_FIRM => 'firm_id',
            ReferenceData::LEVEL_USER => 'user_id',
        );
    }

    /**
     * Gets all supported levels.
     * @return int a Bitmask value representing the supported mapping levels.
     */
    abstract protected function getSupportedLevels(): int;

    /**
     * Gets the name of the ID column representing the reference data in the mapping table.
     * In most cases, this is just the table name with '_id' appended to the end.
     * @param int $level The level used for mapping.
     * @return string The name of the reference data ID column in the mapping table.
     */
    protected function mappingColumn(int $level): string
    {
        return $this->tableName() . '_id';
    }

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
                case ReferenceData::LEVEL_INSTITUTION:
                    return 'institution';
                default:
                    return 'installation';
            }
        }
        throw new InvalidArgumentException('Class does not support specified level.');
    }

    /**
     * Gets all model instances at the level specified
     * @deprecated v6.7.x Use findAllAtLevels with a single-level bitmask instead.
     * @param int $level
     * @param mixed $criteria
     * @param Institution|null $institution
     * @return array
     */
    public function findAllAtLevel(int $level, $criteria = null, ?Institution $institution = null): array
    {
        $levelCriteria = $this->buildCriteriaForFindAllAtLevel($level, $institution, false);

        if (isset($criteria)) {
            $levelCriteria->mergeWith($criteria);
        }

        return static::model()->findAll($levelCriteria);
    }

    /**
     * Gets all model instances at the levels specified.
     * @param int $level_mask
     * @param mixed $criteria
     * @param Institution|null $institution
     * @param bool $anyLevel
     * @return array
     */
    public function findAllAtLevels(int $level_mask, $criteria = null, ?Institution $institution = null, bool $anyLevel = true): array
    {
        $levelCriteria = $this->buildCriteriaForFindAllAtLevel($level_mask, $institution, $anyLevel);

        if (isset($criteria)) {
            $levelCriteria->mergeWith($criteria);
        }

        return static::model()->findAll($levelCriteria);
    }

    /**
     * Get criteria to fetch all model instances at the levels specified.
     * @param integer $level_mask
     * @param mixed $criteria
     * @param Institution|null $institution
     * @param boolean $anyLevel
     * @return CDbCriteria
     */
    public function getCriteriaForLevels(
        int $level_mask,
            $criteria = null,
        ?Institution $institution = null,
        bool $anyLevel = true
    ): CDbCriteria {
        $levelCriteria = $this->buildCriteriaForFindAllAtLevel($level_mask, $institution, $anyLevel);

        if (isset($criteria)) {
            $levelCriteria->mergeWith($criteria);
        }

        return $levelCriteria;
    }

    /**
     * This abstraction allows to extend the criteria for findAllAtLevel for models
     * that have additional functionality (cf Medication which only applies the mappings
     * on local instances)
     */
    protected function buildCriteriaForFindAllAtLevel(int $level_mask, ?Institution $institution = null, bool $anyLevel = true)
    {
        $criteria = new CDbCriteria();

        $level_ids = $this->getIdForLevels($level_mask, $institution);
        foreach ($level_ids as $level => $level_id) {
            if ($level === ReferenceData::LEVEL_INSTALLATION) {
                $sublevel_ids = $this->getIdForLevels($this->getSupportedLevels(), $institution);
                $subcondition = '';
                $index = 0;
                foreach ($this->enumerateSupportedLevels() as $sublevel) {
                    if ($sublevel === ReferenceData::LEVEL_INSTALLATION) {
                        // Skip installation level
                        continue;
                    }
                    list($bind, $mapping_level_column_name, $mapping_data_column_name, $mapping_model_table) = $this->getMappingData($sublevel);
                    $prefix = $index !== 0 ? ' AND ' : '';
                    $subcondition .= $prefix . "t.id NOT IN (SELECT $mapping_data_column_name FROM $mapping_model_table WHERE $mapping_level_column_name = $bind)";
                    $index++;
                    $criteria->params[$bind] = $sublevel_ids[$sublevel];
                }
                $criteria->addCondition($subcondition, $anyLevel ? 'OR' : 'AND');
            } else {
                list($bind, $mapping_level_column_name, $mapping_data_column_name, $mapping_model_table) = $this->getMappingData($level);
                $criteria->addCondition(
                    "t.id in (SELECT $mapping_data_column_name FROM $mapping_model_table WHERE $mapping_level_column_name = $bind)",
                    $anyLevel ? 'OR' : 'AND'
                );
                $criteria->params[$bind] = $level_id;
            }
        }

        return $criteria;
    }

    /**
     * Returns mapping information as a tuple based on the supplied level.
     *
     * @param int $level
     * @return array [bind param name, level column name, data column name, mapping model table name]
     */
    private function getMappingData(int $level): array
    {
        $bind = ':' . ReferenceData::LEVEL_REFS[$level];
        $mapping_level_column_name = $this->levelIdColumn($level);
        $mapping_model = $this->mappingModelName($level)::model();

        $mapping_data_column_name = $this->mappingColumn($level);
        $mapping_model_table = $mapping_model->tableName();
        return [$bind, $mapping_level_column_name, $mapping_data_column_name, $mapping_model_table];
    }

    /**
     * Get ID at the specified level
     *
     * @deprecated v6.7.x Use getIdForLevels with a single-level bitmask instead
     *
     * @param int $level
     * @param Institution|null $institution
     * @param Site|null $site
     * @param Specialty|null $specialty
     * @param Subspecialty|null $subspecialty
     * @param Firm|null $firm
     * @param User|null $user
     * @return int
     */
    public function getIdForLevel(
        int $level,
        ?Institution $institution = null,
        ?Site $site = null,
        ?Specialty $specialty = null,
        ?Subspecialty $subspecialty = null,
        ?Firm $firm = null,
        ?User $user = null
    ): int {
        $institution = $institution ?? Institution::model()->getCurrent();
        $firm = $firm ?? Yii::app()->session->getSelectedFirm();
        $subspecialty = $subspecialty ?? ($firm ? $firm->serviceSubspecialtyAssignment->subspecialty : null);
        $specialty = $specialty ?? ($subspecialty ? $subspecialty->specialty : null);
        $site = $site ?? Yii::app()->session->getSelectedSite();
        $user = $user ?? User::model()->findByPk(Yii::app()->user->id);

        if ($this->getSupportedLevels() & $level) {
            switch ($level) {
                case ReferenceData::LEVEL_USER:
                    if (!isset($user)) {
                        throw new Exception("No applicable user exists");
                    }
                    return $user->id;
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
                case ReferenceData::LEVEL_INSTITUTION:
                    if (!isset($institution)) {
                        throw new Exception("No applicable institution exists");
                    }
                    return $institution->id;
                default:
                    return null;
            }
        }
        throw new InvalidArgumentException('Class does not support specified level.');
    }

    /**
     * Gets IDs for all applicable levels from data context.
     *
     * @param integer $level_mask A bitmask of the levels to find IDs for.
     * @param Institution|null $institution Institution
     * @param Site|null $site Site
     * @param Specialty|null $specialty Specialty
     * @param Subspecialty|null $subspecialty Subspecialty
     * @param Firm|null $firm Firm
     * @param User|null $user User
     * @return array A mapping table of ID columns and values.
     */
    protected function getIdForLevels(
        int $level_mask,
        ?Institution $institution = null,
        ?Site $site = null,
        ?Specialty $specialty = null,
        ?Subspecialty $subspecialty = null,
        ?Firm $firm = null,
        ?User $user = null
    ): array {
        $level_ids = array();
        $resolver = new ReferenceLevelIdResolver($institution, $site, $specialty, $subspecialty, $firm, $user);

        // Get the selected levels that are applicable to the level mask for this object using a bitwise AND.
        $selected_levels = $this->getSupportedLevels() & $level_mask;

        // As this loops over all levels (regardless of the level mask), only add the IDs for the selected (and therefore supported) levels to the list.
        foreach (array_reverse($this->levelColumns(), true) as $level => $column) {
            if ($selected_levels & $level) {
                if ($level === ReferenceData::LEVEL_INSTALLATION) {
                    $level_ids[$level] = 1;
                } else {
                    $level_ids[$level] = $resolver->resolveId(ReferenceData::LEVEL_REFS[$level]);
                }
            }
        }

        return $level_ids;
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
            if ($level === ReferenceData::LEVEL_INSTALLATION) {
                // For installation level, this mapping only exists if there are no mappings at any lower level.
                foreach ($this->enumerateSupportedLevels() as $sublevel) {
                    if ($sublevel === ReferenceData::LEVEL_INSTALLATION) {
                        continue;
                    }
                    $suffix = $this->levelRelationProperty($sublevel);
                    foreach ($this->$suffix ?? [] as $model) {
                        if ((int)$model->id === $id) {
                            if (!$this->softDeleteMappings() || $model->active) {
                                return false;
                            }
                        }
                    }
                }
                return true;
            }
            $suffix = $this->levelRelationProperty($level);
            foreach ($this->$suffix ?? [] as $model) {
                if ((int)$model->id === $id) {
                    if (!$this->softDeleteMappings() || $model->active) {
                        return true;
                    }
                    return false;
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
            if ($level === ReferenceData::LEVEL_INSTALLATION) {
                return true;
            } else {
                $model = $this->mappingModelName($level);
                $model_func = $model . '::model';
                $reference_data_column = $this->mappingColumn($level);
                $level_column = $this->levelIdColumn($level);

                if (
                    (int)$model_func()->count(
                        "$reference_data_column = :reference_data_id AND $level_column = :level_id",
                        array(':reference_data_id' => $this->id, ':level_id' => $level_id)
                    ) > 0
                ) {
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
            if ($level === ReferenceData::LEVEL_INSTALLATION) {
                // Mapping is not created at installation level as the mapping is just the model itself.
                return true;
            } else {
                $model = $this->mappingModelName($level);
                $model_func = $model . '::model';
                $reference_data_column = $this->mappingColumn($level);
                $level_column = $this->levelIdColumn($level);

                $saved = true;
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    foreach ($level_ids as $level_id) {
                        if (
                            $model_func()->exists(
                                "$reference_data_column = :reference_data_id AND $level_column = :level_id",
                                array(':reference_data_id' => $this->id, ':level_id' => $level_id)
                            )
                        ) {
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
            if ($level === ReferenceData::LEVEL_INSTALLATION) {
                // Mapping is not deleted at installation level as the mapping is just the model itself.
                return true;
            } else {
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
            if ($level === ReferenceData::LEVEL_INSTALLATION) {
                throw new Exception("Remapping is not supported at installation level");
            }
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
            if ($level === ReferenceData::LEVEL_INSTALLATION) {
                // Mapping is not deleted at installation level as the mapping is just the model itself.
                return true;
            } else {
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
        }
        throw new InvalidArgumentException('Specified level is not supported by this class.');
    }

    /**
     * @return array Array of integers corresponding to ReferenceData class constants denoting supported mapping levels.
     */
    public function enumerateSupportedLevels(): array
    {
        $enumerated_supported_levels = array();

        foreach (ReferenceData::ALL_LEVELS as $level) {
            if ($this->getSupportedLevels() & $level) {
                $enumerated_supported_levels[] = $level;
            }
        }

        return $enumerated_supported_levels;
    }
}