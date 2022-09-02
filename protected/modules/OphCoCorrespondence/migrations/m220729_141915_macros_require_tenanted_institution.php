<?php

/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class m220729_141915_macros_require_tenanted_institution
 *
 * This migration has been based on the original admin UI that was defined for the LetterMacro relationship with Institution. As such, it throws
 * exceptions for collisions between mismatched derived (from Firm and Site) institution relationships.
 *
 * Furthermore, it does not take account of any LetterMacro instances that have multiple explicit Institutions related to them, as this should not
 * have been possible in the UI.
 */
class m220729_141915_macros_require_tenanted_institution extends OEMigration
{

    protected ?array $defaultMacroInstitutionFromSites = null;
    protected ?array $defaultMacroInstitutionFromFirms = null;

    public function safeUp()
    {
        if (!Yii::app()->params['institution_code']) {
            throw new Exception("m220729_141915_macros_require_tenanted_institution requires a default institution code in the app params.");
        }

        $primary_institution_id = $this->dbConnection
            ->createCommand("SELECT id FROM institution WHERE remote_id = :code")
            ->bindValues(array(':code' => Yii::app()->params['institution_code']))
            ->queryScalar();


        // get all macros where institution_id IS NULL and set to primary institution
        $macro_ids_without_institution = $this->getMacroIdsWithoutInstitution();
        if (!count($macro_ids_without_institution)) {
            return true;
        }

        $this->loadSites($macro_ids_without_institution);
        $this->loadFirms($macro_ids_without_institution);

        $still_to_map = $this->mapInstitutionForLetterMacroIds($macro_ids_without_institution);

        if (!count($still_to_map)) {
            return true;
        }

        // then duplicate for all the other tenanted institutions (Maybe add WHERE i.id != :primary_id)
        $tenanted_institution_ids = Yii::app()->db->createCommand()
            ->selectDistinct('i.id')
            ->from('institution i')
            ->join('institution_authentication ia', 'ia.institution_id = i.id')
            ->queryColumn();

        $this->mapInstitutionsForMacroIds($still_to_map, $tenanted_institution_ids);
    }

    public function safeDown()
    {
        echo "m220729_141915_macros_require_tenanted_institution does not support migration down.";
        return true;
    }

    protected function getMacroIdsWithoutInstitution(): array
    {
        return $this->dbConnection
        ->createCommand("SELECT id from ophcocorrespondence_letter_macro where id not in (SELECT letter_macro_id FROM ophcocorrespondence_letter_macro_institution)")->queryColumn();
    }

    protected function loadSites(array $letter_macro_ids = []): void
    {
        $ids = implode(",", $letter_macro_ids);
        $query = <<<EOSQL
            SELECT level_table.letter_macro_id as id, site.institution_id as institution_id FROM site
            JOIN ophcocorrespondence_letter_macro_site level_table
            ON site.id = level_table.site_id
            WHERE level_table.letter_macro_id in ($ids)
EOSQL;

        $results = $this->dbConnection
            ->createCommand($query)
            ->queryAll();

        $this->defaultMacroInstitutionFromSites = array_reduce(
            $results,
            function ($mapped, $row) {
                if (isset($mapped[$row['id']]) && $mapped[$row['id']] !== $row['institution_id']) {
                    throw new \LogicException("Letter macro with id {$row['id']} has conflicting instituon maps from sites.");
                }
                $mapped[$row['id']] = $row['institution_id'];
                return $mapped;
            },
            []
        );
    }

    protected function loadFirms(array $letter_macro_ids = []): void
    {
        $ids = implode(",", $letter_macro_ids);
        $query = <<<EOSQL
            SELECT level_table.letter_macro_id as id, firm.institution_id as institution_id FROM firm
            JOIN ophcocorrespondence_letter_macro_firm level_table
            ON firm.id = level_table.firm_id
            WHERE level_table.letter_macro_id in ($ids)
EOSQL;

        $results = $this->dbConnection
            ->createCommand($query)
            ->queryAll();

        $this->defaultMacroInstitutionFromFirms = array_reduce(
            $results,
            function ($mapped, $row) {
                if (isset($mapped[$row['id']]) && $mapped[$row['id']] !== $row['institution_id']) {
                    throw new \LogicException("Letter macro with id {$row['id']} has conflicting instituon maps from firms.");
                }
                $mapped[$row['id']] = $row['institution_id'];
                return $mapped;
            },
            []
        );
    }

    /**
     * uses the mapped instituion values from site and firm to explicitly set the institution on the given letter macro ids.
     * will return a list of ids that could not be mapped because no site or firm has been mapped for it.
     */
    protected function mapInstitutionForLetterMacroIds(array $letter_macro_ids = []): array
    {
        $insert_list = [];
        $no_institution_mapped_ids = [];
        foreach ($letter_macro_ids as $letter_macro_id) {
            $institution_id_from_site = $this->defaultMacroInstitutionFromSites[$letter_macro_id] ?? null;
            $institution_id_from_firm = $this->defaultMacroInstitutionFromFirms[$letter_macro_id] ?? null;
            if ($institution_id_from_firm && $institution_id_from_site && $institution_id_from_firm !== $institution_id_from_site) {
                throw new \LogicException(("Letter macro with id $letter_macro_id has conflicting institution maps from firm(s) and site(s)."));
            }

            $institution_id = $institution_id_from_firm ?? $institution_id_from_site;

            if ($institution_id) {
                $insert_list[] = ['letter_macro_id' => $letter_macro_id, 'institution_id' => $institution_id];
            } else {
                $no_institution_mapped_ids[] = $letter_macro_id;
            }
        }

        if (count($insert_list)) {
            $this->insertMultiple('ophcocorrespondence_letter_macro_institution', $insert_list);
        }

        return $no_institution_mapped_ids;
    }

    protected function mapInstitutionsForMacroIds(array $letter_macro_ids = [], array $institution_ids = []): void
    {
        if (!count($letter_macro_ids) || !count($institution_ids)) {
            return;
        }

        $this->assignInstitutionIdToMacroIds($letter_macro_ids, array_shift($institution_ids));

        if (!count($institution_ids)) {
            return;
        }

        $this->duplicateMacrosForInstitutionIds($letter_macro_ids, $institution_ids);
    }

    protected function assignInstitutionIdToMacroIds(array $letter_macro_ids, $institution_id): void
    {
        $insert_list = array_reduce(
            $letter_macro_ids,
            function ($mapped, $letter_macro_id) use ($institution_id) {
                $mapped[] = ['letter_macro_id' => $letter_macro_id, 'institution_id' => $institution_id];
                return $mapped;
            },
            []
        );

        $this->insertMultiple('ophcocorrespondence_letter_macro_institution', $insert_list);
    }

    protected function duplicateMacrosForInstitutionIds(array $letter_macro_ids, array $institution_ids): void
    {
        foreach ($institution_ids as $institution_id) {
            $duplicate_macro_ids = [];
            foreach ($letter_macro_ids as $letter_macro_id) {
                $duplicate_macro_id = $this->duplicateLetterMacro($letter_macro_id);
                $this->duplicateLetterMacroSubspecialty($letter_macro_id, $duplicate_macro_id);

                $duplicate_macro_ids[] = $duplicate_macro_id;
            }

            $this->assignInstitutionIdToMacroIds($duplicate_macro_ids, $institution_id);
        }
    }

    private function duplicateLetterMacro($letter_macro_id): int
    {
        $columns_to_duplicate = "name, use_nickname, body, cc_patient, display_order, cc_doctor, cc_drss, recipient_id, short_code, letter_type_id, cc_optometrist";
        $sql = <<<EOSQL
        INSERT INTO ophcocorrespondence_letter_macro
        ($columns_to_duplicate)
        SELECT $columns_to_duplicate FROM ophcocorrespondence_letter_macro where id = $letter_macro_id
EOSQL;
        $this->dbConnection->createCommand($sql)->execute();

        return $this->dbConnection->lastInsertID;
    }

    private function duplicateLetterMacroSubspecialty(int $old_letter_macro_id, int $new_letter_macro_id): void
    {
        $insert_list = [];

        $results = $this->dbConnection
            ->createCommand("SELECT subspecialty_id FROM ophcocorrespondence_letter_macro_subspecialty WHERE id = $old_letter_macro_id")
            ->queryColumn();

        foreach ($results as $row) {
            $insert_list[] = ['letter_macro_id' => $new_letter_macro_id, 'subspecialty_id' => $row['subspecialty_id']];
        }

        if (count($insert_list)) {
            $this->insertMultiple('ophcocorrespondence_letter_macro_subspecialty', $insert_list);
        }
    }
}
