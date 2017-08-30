<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Class AllergyMigrateCommand
 */
class MedicationMigrateCommand extends PatientLevelMigration
{
    protected $event_type_cls = 'OphCiExamination';
    // Original table is renamed to this during the module database migration
    protected static $archived_entry_table = 'medication';

    protected static $element_class = 'OEModule\OphCiExamination\models\HistoryMedications';
    protected static $entry_class = 'OEModule\OphCiExamination\models\HistoryMedicationsEntry';
    protected static $entry_attributes = array(
        'drug_id',
        'medication_drug_id',
        'dose',
        'route_id',
        'option_id',
        'frequency_id',
        'start_date',
        'end_date',
        'stop_reason_id',
        'prescription_item_id'
    );

    public function getHelp()
    {
        return "Migrates the original Medication records to an examination event in a change tracker episode\n";
    }

    protected function processPatientRows($patient, $rows)
    {
        $entries = parent::processPatientRows($patient, $rows);
        print "original:" . count($entries) . "\n";
        return array_merge($entries, $this->getEntriesForUntrackedPrescriptionItems($patient, $entries));
    }

    /**
     * Essentially duplicated functionality from the HistoryMedications widget
     * to create entries for a patient that were not duped into the medications
     * table (in the <2.0 implementation, medications were only created for
     * prescriptions when they were stopped)
     *
     * @param Patient $patient
     * @param array $entries
     * @return array
     */
    private function getEntriesForUntrackedPrescriptionItems($patient, $entries)
    {
        $untracked = array();
        if ($api = Yii::app()->moduleAPI->get('OphDrPrescription')) {
            $tracked_prescr_item_ids = array_map(
                function ($entry) {
                    return $entry->prescription_item_id;
                },
                array_filter(
                    $entries,
                    function($entry) {return $entry->prescription_item_id !== null;}
                )
            );

            if ($untracked_prescription_items = $api->getPrescriptionItemsForPatient(
                $patient, $tracked_prescr_item_ids)
            ) {
                foreach ($untracked_prescription_items as $item) {
                    $entry = new static::$entry_class();
                    $entry->loadFromPrescriptionItem($item);
                    $untracked[] = $entry;
                }
            }
        }
        print "untracked:" . count($untracked) . "\n";
        return $untracked;
    }
}