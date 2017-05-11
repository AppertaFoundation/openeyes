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

class FamilyHistoryMigrateCommand extends CConsoleCommand
{
    protected static $element_class = 'OEModule\OphCiExamination\models\FamilyHistory';
    protected static $entry_class = 'OEModule\OphCiExamination\models\FamilyHistory_Entry';
    protected static $entry_attributes = array(
        'relative_id',
        'other_relative',
        'side_id',
        'condition_id',
        'other_condition',
        'comments'
    );

    public function getHelp()
    {
        return "Migrates the original Family History record to an examination event in change tracker episode\n";
    }

    public function run($args)
    {
        $db = Yii::app()->db;
        $query = $db->createCommand('select patient_id, ' . implode(',', static::$entry_attributes) . ' from archive_family_history order by patient_id asc, created_date asc, id asc');
        $patient_id = null;
        $patient_rows = array();
        $patient_count = 0;
        $processed_count = 0;
        foreach ($query->queryAll() as $row) {
            if ($row['patient_id'] !== $patient_id) {
                if (count($patient_rows)) {
                    if ($this->processPatient($patient_id, null, $patient_rows)) {
                        $processed_count++;
                    }
                    $patient_count++;
                    echo ".";
                    break;
                }
                $patient_id = $row['patient_id'];
                $patient_rows = array();
            }
            $patient_rows[] = $row;
        }
        // repeat for last patient
        if ($this->processPatient($patient_id, null, $patient_rows)) {
            $processed_count++;
        }
        $patient_count++;

        $query = $db->createCommand('select archive_no_family_history_date, id from patient where archive_no_family_history_date is not null and archive_no_family_history_date != ""');
        foreach ($query->queryAll() as $row) {
            if ($this->processPatient($row['id'], $row['archive_no_family_history_date'])) {
                $processed_count++;
            }
            $patient_count++;
        }

        echo "\nProcessed " . $processed_count . "/" . $patient_count . " patients.\n";
    }

    private $api;

    /**
     * @return OEModule\OphCiExamination\components\OphCiExamination_API
     */
    protected function getApi()
    {
        if (!$this->api) {
            $this->api = Yii::app()->moduleAPI->get('OphCiExamination');
        }
        return $this->api;
    }

    /**
     * Process an individual set of records for a patient.
     *
     * @param $patient_id
     * @param $no_history_date
     * @param $rows
     * @return bool
     * @throws Exception
     */
    public function processPatient($patient_id, $no_history_date = null, $rows = array())
    {
        $patient = Patient::model()->findByPk($patient_id);

        if ($this->getApi()->getLatestElement(static::$element_class, $patient)) {
            return false;
        }
        $entries = array();
        foreach ($rows as $row) {
            $entry = new static::$entry_class();
            $entry->attributes = $row;
            $entries[] = $entry;
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $episode = Episode::getChangeEpisode($patient);
            $event = new Event();
            if ($episode->isNewRecord) {
                $episode->save();
            }
            $event->episode_id = $episode->id;
            $event->event_type_id = $this->getApi()->getEventType()->id;
            $event->save();

            $element = new static::$element_class();
            $element->event_id = $event->id;
            if ($no_history_date) {
                $element->no_family_history_date = $no_history_date;
            } else {
                $element->entries = $entries;
            }
            $element->save();

            $transaction->commit();
        }
        catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        return true;
    }

}