<?php

/**
 * Class OwnedByReferenceData
 * Represents a reference data point that is 'owned' by certain levels (this also respects cardinality between different levels except installation level).
 * If not owned by any level, then it is considered an installation level reference data point (if this is supported).
 */
trait OwnedByReferenceData
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
     * Finds all instances of this object at all specified levels that are supported.
     *
     * @param int $level_mask A bitmask of the levels to find results for
     * @param mixed $criteria Any extra criteria to add to the query.
     * @param Institution|null $institution The institution to use in the criteria. Leave blank to use the current institution.
     * @return BaseActiveRecord[] A list of records matching the bitmask and extra criteria (if any).
     */
    public function findAllAtLevels(int $level_mask, $criteria = null, ?Institution $institution = null): array
    {
        if ($institution === null) {
            $institution = Institution::model()->getCurrent();
        }

        $levelCriteria = $this->buildCriteriaForFindAllAtLevels($level_mask, $institution);

        if (isset($criteria)) {
            $levelCriteria->mergeWith($criteria);
        }

        return static::model()->findAll($levelCriteria);
    }

    /**
     * Returns a CDbCriteria instance that can be used with the Admin object to locate instances of this object at all specified levels that are supported.
     *
     * @param integer $level_mask A bitmask of the levels to find results for.
     * @param mixed $criteria Any extra criteria to add to the query.
     * @param Institution $institution The institution to attach to the criteria. Leave blank for the current institution.
     * @return CDbCriteria
     */
    public function getCriteriaForLevels(int $level_mask, $criteria = null, ?Institution $institution = null): CDbCriteria
    {
        if ($institution === null) {
            $institution = Institution::model()->getCurrent();
        }

        $levelCriteria = $this->buildCriteriaForFindAllAtLevels($level_mask, $institution);

        if (isset($criteria)) {
            $levelCriteria->mergeWith($criteria);
        }

        return $levelCriteria;
    }

    /**
     * Gets a bitmask of supported setting levels.
     *
     * @return int
     */
    protected function getSupportedLevelMask(): int
    {
        return ReferenceData::LEVEL_INSTALLATION;
    }

    /**
     * Builds criteria for multi-levelled findAll operations.
     *
     * @param integer $level_mask A bitmask of the levels to find results for
     * @param Institution $institution The institution to compare against.
     * @return CDbCriteria A CDbCriteria object that can be used to find all records matching the bitmask and extra criteria (if any).
     */
    protected function buildCriteriaForFindAllAtLevels(int $level_mask, ?Institution $institution = null)
    {
        $criteria = new CDbCriteria();
        $conditions = array();

        $level_ids = $this->getIdForLevels($level_mask, $institution);

        if (count($level_ids) === 0) {
            // No IDs specified
            foreach ($this->getLevelColumnsOnModel() as $column) {
                $conditions[] = "$column IS NULL";
            }
            if (!empty($conditions)) {
                $criteria->addCondition(implode(' AND ', $conditions));
            }
            return $criteria;
        }
        foreach ($level_ids as $column => $level_constraint) {
            $criteria = $this->addLevelQueryToCriteria($column, $level_constraint, $criteria);
        }

        return $criteria;
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

        // Get the selected levels that are applicable to the level mask for this object using a bitwise AND.
        $selected_levels = $this->getSupportedLevelMask() & $level_mask;

        $resolver = new ReferenceLevelIdResolver($institution, $site, $specialty, $subspecialty, $firm, $user);

        // As this loops over all levels (regardless of the level mask), only add the IDs for the selected (and therefore supported) levels to the list.
        foreach (array_reverse($this->levelColumns(), true) as $level => $column) {
            if ($selected_levels & $level) {
                if ($level === ReferenceData::LEVEL_INSTALLATION) {
                    $level_ids[$column] = 1;
                } else {
                    $level_ids[$column] = $resolver->resolveId(ReferenceData::LEVEL_REFS[$level]);
                }
            }
        }

        return $level_ids;
    }

    /**
     * Gets the level-based foreign-key columns that exist on the model.
     *
     * @return array
     */
    private function getLevelColumnsOnModel(): array
    {
        $supported_columns = array();
        foreach (array_reverse($this->levelColumns(), true) as $level => $column_name) {
            if ($level === ReferenceData::LEVEL_INSTALLATION) {
                $supported_columns[$level] = $column_name;
            }
            if ($this->getTableSchema()->getColumn($column_name)) {
                $supported_columns[$level] = $column_name;
            }
        }
        return $supported_columns;
    }

    /**
     * Adds level-based criteria to an existing criteria object
     *
     * @param string $column Column name
     * @param integer $id ID
     * @param CDbCriteria|null $criteria Existing criteria to append to.
     * @return CDbCriteria
     */
    private function addLevelQueryToCriteria(string $column, int $id, ?CDbCriteria $criteria): CDbCriteria
    {
        if (!$criteria) {
            $criteria = new CDbCriteria();
        }
        $supported_columns = $this->getLevelColumnsOnModel();
        if ($column === $supported_columns[ReferenceData::LEVEL_INSTALLATION]) {
            $subconditions = array();
            foreach ($supported_columns as $subcolumn) {
                if ($subcolumn === $supported_columns[ReferenceData::LEVEL_INSTALLATION]) {
                    continue;
                }
                $subconditions[] = "$subcolumn IS NULL";
            }
            $condition = '(' . implode(' AND ', $subconditions) . ')';
        } else {
            $subconditions = array();
            // Add IS NULL conditions for all lower level ID columns.
            foreach ($supported_columns as $subcolumn) {
                // As the supported levels list is in order from lowest to highest level,
                // we can break the loop once we reach the current column in the parent loop.
                if ($subcolumn === $column) {
                    break;
                }
                $subconditions[] = $subcolumn . ' IS NULL';
            }
            if (!empty($subconditions)) {
                $condition = "$column = :$column AND " . implode(' AND ', $subconditions) . '';
            } else {
                $condition = "$column = :$column";
            }
            $criteria->params[":$column"] = $id;
        }
        $criteria->addCondition($condition, 'OR');
        return $criteria;
    }
}
