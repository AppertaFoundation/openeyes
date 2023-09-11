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
    public function levelColumns()
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
     * @param Site|null $site
     * @param Specialty|null $specialty
     * @param Subspecialty|null $subspecialty
     * @param Firm|null $firm
     * @param User|null $user
     * @return BaseActiveRecord[] A list of records matching the bitmask and extra criteria (if any).
     */
    public function findAllAtLevels(
        int $level_mask,
        $criteria = null,
        ?Institution $institution = null,
        ?Site $site = null,
        ?Specialty $specialty = null,
        ?Subspecialty $subspecialty = null,
        ?Firm $firm = null,
        ?User $user = null,
        bool $matchAnyLevels = true
    ): array {
        $levelCriteria = $this->getCriteriaForLevels(
            $level_mask,
            $criteria,
            $institution,
            $site,
            $specialty,
            $subspecialty,
            $firm,
            $user,
            $matchAnyLevels
        );

        return static::model()->findAll($levelCriteria);
    }

    /**
     * Returns a CDbCriteria instance that can be used with the Admin object to locate instances of this object at all specified levels that are supported.
     *
     * @param integer $level_mask A bitmask of the levels to find results for.
     * @param mixed $criteria Any extra criteria to add to the query.
     * @param Institution|null $institution The institution to attach to the criteria. Leave blank for the current institution.
     * @param Site|null $site
     * @param Specialty|null $specialty
     * @param Subspecialty|null $subspecialty
     * @param Firm|null $firm
     * @param User|null $user
     * @return CDbCriteria
     */
    public function getCriteriaForLevels(
        int $level_mask,
        $criteria = null,
        ?Institution $institution = null,
        ?Site $site = null,
        ?Specialty $specialty = null,
        ?Subspecialty $subspecialty = null,
        ?Firm $firm = null,
        ?User $user = null,
        bool $matchAnyLevels = true
    ): CDbCriteria {
        if ($institution === null) {
            $institution = Institution::model()->getCurrent();
        }

        $levelCriteria = $this->buildCriteriaForFindAllAtLevels(
            $level_mask,
            $institution,
            $site,
            $specialty,
            $subspecialty,
            $firm,
            $user,
            $matchAnyLevels
        );

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
    protected function buildCriteriaForFindAllAtLevels(
        int $level_mask,
        ?Institution $institution,
        ?Site $site,
        ?Specialty $specialty,
        ?Subspecialty $subspecialty,
        ?Firm $firm,
        ?User $user,
        bool $matchAnyLevels
    ): CDbCriteria {
        $criteria = new CDbCriteria();
        $conditions = array();
        $joinOperator = $matchAnyLevels ? 'OR' : 'AND';

        $level_ids = $this->getIdForLevels($level_mask, $institution, $site, $specialty, $subspecialty, $firm, $user);

        if (count($level_ids) === 0) {
            // No IDs specified
            foreach ($this->getLevelColumnsOnModel() as $column) {
                $conditions[$column] = null;
            }
            if (!empty($conditions)) {
                $criteria->addColumnCondition($conditions, 'AND', $joinOperator);
            }
            return $criteria;
        }

        foreach ($level_ids as $column => $level_constraint) {
            $criteria = $this->addLevelQueryToCriteria($column, $level_constraint, $criteria, array_keys($level_ids), $matchAnyLevels);
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
     * @param integer|null $id ID
     * @param CDbCriteria|null $criteria Existing criteria to append to.
     * @return CDbCriteria
     */
    private function addLevelQueryToCriteria(
        string $column,
        ?int $id,
        ?CDbCriteria $criteria,
        array $selected_levels,
        bool $matchAnyLevels
    ): CDbCriteria {
        if (!$criteria) {
            $criteria = new CDbCriteria();
        }
        $supported_columns = $this->getLevelColumnsOnModel();
        $joinOperator = $matchAnyLevels ? 'OR' : 'AND';
        if ($column === $supported_columns[ReferenceData::LEVEL_INSTALLATION]) {
            if ($matchAnyLevels) {
                // Match on record where every reference data relation column is blank.
                $subconditions = array();
                foreach ($supported_columns as $subcolumn) {
                    // Skip installation level as this is not a column.
                    if ($subcolumn === $supported_columns[ReferenceData::LEVEL_INSTALLATION]) {
                        continue;
                    }
                    $subconditions[$subcolumn] = null;
                }
                $criteria->addColumnCondition($subconditions, 'AND', $joinOperator);
            } else {
                // Match on records where every reference data relation column except those selected in the level mask are empty.
                $subconditions = array();
                foreach ($supported_columns as $subcolumn) {
                    // Exclude the columns relating to the selected levels and exclude the installation level as this is not a column.
                    if (
                        $subcolumn === $supported_columns[ReferenceData::LEVEL_INSTALLATION]
                        || in_array($subcolumn, $selected_levels, true)
                    ) {
                        continue;
                    }
                    $subconditions[$subcolumn] = null;
                }
                if (!empty($subconditions)) {
                    $criteria->addColumnCondition($subconditions, 'AND', $joinOperator);
                }
            }
        } else {
            if ($id) {
                $criteria->compare($column, $id, false, $joinOperator);
            } else {
                $criteria->addColumnCondition([$column => null], 'AND', $joinOperator);
            }
        }

        return $criteria;
    }
}
