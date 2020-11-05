<?php /**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class OldMedicationAndDrugDeletionCommand extends CConsoleCommand
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Delete medication and drugs with redundant tables which are not needed anymore after DM+D data import';
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return <<<EOH
        
'Delete medication and drugs with redundant tables after import

USAGE
  php yiic oldmedicationanddrugdeletion
         
EOH;
    }

    public function actionIndex()
    {
        $t = microtime(true);
        echo "\n[". (date("Y-m-d H:i:s")) ."] Old medication and drug deletion (oldmedicationanddrugdeletion) ... ";
        $this->deleteUnusedMedicationDrugs();
        $this->deleteOldDrugAndMedicationTables();
        $this->dropTempColumns();
        echo "OK - took: " . (microtime(true) -$t) . "s\n";
    }

    private function deleteUnusedMedicationDrugs()
    {
        $medication_drugs = Medication::model()->findAll(
            'source_subtype = :source_subtype',
            [':source_subtype' => 'medication_drug']
        );

        foreach ($medication_drugs as $medication_drug) {
            $event_medication_use = EventMedicationUse::model()->findAll(
                'medication_id = :medication_id',
                [":medication_id" => $medication_drug->id]
            );
            if (count($event_medication_use) === 0) {
                $this->deleteMedication($medication_drug);
            }
        }
    }

    private function deleteOldDrugAndMedicationTables()
    {
        ## Find & archive old tables (+ version tables)
        $drugTablePrefix = 'drug_';
        $tables_to_rename = ['drug', 'ophciexamination_history_medications_entry',
            'ophdrprescription_item',
            'site_subspecialty_drug',
            'medication_drug',
        ];
        $tables_versions = array();

        foreach ($tables_to_rename as $table) {
            array_push($tables_versions, $table . '_version');
        }

        $tables_to_rename = array_merge($tables_to_rename, $tables_versions);

        foreach (Yii::app()->db->getSchema()->getTableNames() as $table_to_rename) {
            if (((substr($table_to_rename, 0, strlen($drugTablePrefix)) === $drugTablePrefix) || in_array($table_to_rename, $tables_to_rename))) {
                Yii::app()->db->createCommand("RENAME TABLE " . $table_to_rename . " TO archive_" . $table_to_rename)->execute();
            }
        }
    }

    private function deleteMedication($medication_drug)
    {
        MedicationAttributeAssignment::model()->find('medication_id = :medication_id', [':medication_id' => $medication_drug->id]);
        MedicationSearchIndex::model()->find('medication_id = :medication_id', [':medication_id' => $medication_drug->id]);
        MedicationSetAutoRuleMedication::model()->find('medication_id = :medication_id', [':medication_id' => $medication_drug->id]);
        MedicationSetItem::model()->find('medication_id = :medication_id', [':medication_id' => $medication_drug->id]);
        $medication_drug->delete();
    }

    private function dropTempColumns()
    {
        $db = Yii::app()->db;
        $db->createCommand("ALTER TABLE event_medication_use DROP COLUMN IF EXISTS temp_prescription_item_id")->execute();
        $db->createCommand("ALTER TABLE event_medication_use_version DROP COLUMN IF EXISTS temp_prescription_item_id")->execute();
    }
}
