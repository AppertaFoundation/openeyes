<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class AllergyMigrateCommand
 */
class MedicationMigrateCommand extends PatientLevelMigration
{
    protected $event_type_cls = 'OphCiExamination';
    // Original table is renamed to this during the module database migration
    protected static $archived_entry_table = 'archive_medication';

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

    /**
     * @var OphDrPrescription_API
     */
    protected $prescription_api;

    public function getHelp()
    {
        return "Migrates the original Medication records to an examination event in a change tracker episode\n";
    }

    public function init()
    {
        $this->prescription_api = Yii::app()->moduleAPI->get('OphDrPrescription');
    }

    protected function processPatientRows($patient, $rows)
    {
        // strip out any entries that don't actually have a medication or drug recorded
        $rows = array_filter($rows, function($row) {
            return $row['drug_id'] || $row['medication_drug_id'];
        });
        $cleaned_rows = array();
        foreach ($rows as $row) {
            if (!$row['drug_id'] && !$row['medication_drug_id']) {
                continue;
            }
            if (substr($row['start_date'], 0, 4) === '0000') {
                $row['start_date'] = '0000-00-00';
            }
            if (substr($row['end_date'], 0, 4) === '0000') {
                $row['end_date'] = '0000-00-00';
            }
            $cleaned_rows[] = $row;
        }
        $entries = parent::processPatientRows($patient, $cleaned_rows);
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
        if ($this->prescription_api) {
            $tracked_prescr_item_ids = array_map(
                function ($entry) {
                    return $entry->prescription_item_id;
                },
                array_filter(
                    $entries,
                    function($entry) {return $entry->prescription_item_id !== null;
                    }
                )
            );

            if ($untracked_prescription_items = $this->prescription_api->getPrescriptionItemsForPatient(
                $patient, $tracked_prescr_item_ids)
            ) {
                foreach ($untracked_prescription_items as $item) {
                    $entry = new static::$entry_class();
                    $entry->loadFromPrescriptionItem($item);
                    $untracked[] = $entry;
                }
            }
        }
        return $untracked;
    }

    protected function generateAdditionalRecords(&$processed_count, &$patient_count)
    {
        $db = Yii::app()->db;
        $query = $db->createCommand()
            ->select('episode.patient_id')
            ->from('et_ophdrprescription_details as t')
            ->join('event', 't.event_id = event.id')
            ->join('episode', 'event.episode_id = episode.id')
            ->where('episode.deleted != true')
            ->andWhere('event.deleted != true')
            ->andWhere('t.draft = false')
            ->andWhere('episode.patient_id NOT IN (select distinct patient_id from archive_medication)');
        if ($this->patient_id) {
            $query->andWhere('episode.patient_id = :patient_id', array(
                ':patient_id' => $this->patient_id
            ));
        }
        $query->setDistinct(true);
        foreach ($query->queryAll() as $row) {
            $patient_count++;
            $patient = Patient::model()->findByPk($row['patient_id']);
            if ($this->getApi()->getLatestElement(static::$element_class, $patient)) {
                print $patient->id . "already processed\n";
                continue;
            }
            $entries = $this->getEntriesForUntrackedPrescriptionItems($patient, array());
            if ($this->saveRecords($patient, null, $entries)) {
                $processed_count++;
            } else {
                print $patient->id . "already processed\n";
            }
        }

    }
}