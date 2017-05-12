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
 * Class PatientLevelMigration
 *
 * Base class to support the migration of various patient level data items to the new
 * event level model being introduced as part of v2.0.0
 */
class PatientLevelMigration extends CConsoleCommand
{
    /**
     * Module name where the data is moving to
     * @var
     */
    protected $event_type_cls;
    protected $api;
    /**
     * @var EventType
     */
    protected $event_type;

    // Original table is renamed to this during the module database migration
    protected static $archived_entry_table = '';
    // column on patient record indicating no entries have been explicitly recorded
    protected static $archived_no_values_col = '';
    // column on the event level element to record explicit no entries date
    protected static $no_values_col = '';
    // fully qualified class name of the main element for storing the patient level data
    protected static $element_class = '';
    // fully qualified class name of the entry object for storing the patient level data entries
    protected static $entry_class = '';
    // attributes to migrate for each entry
    protected static $entry_attributes = array();

    /**
     * @return OEModule\OphCiExamination\components\OphCiExamination_API
     */
    protected function getApi()
    {
        if (!$this->api) {
            $this->api = Yii::app()->moduleAPI->get($this->event_type_cls);
        }
        return $this->api;
    }

    protected function getEventType()
    {
        if (!$this->event_type) {
            $this->event_type = $this->getApi()->getEventType();
        }
        return $this->event_type;
    }

    /**
     * Should be wrapped in a transaction so that records are not permanently saved
     *
     * @param Patient $patient
     * @return \Event
     */
    protected function getChangeEvent(\Patient $patient)
    {
        $episode = Episode::getChangeEpisode($patient);

        if ($episode->isNewRecord) {
            $episode->save();
        } elseif (count($episode->events)) {
            foreach ($episode->events as $ev) {
                if ($ev->event_type_id == $this->getEventType()->id) {
                    return $ev;
                }
            }
        }

        $event = new Event();
        $event->episode_id = $episode->id;
        $event->event_type_id = $this->getEventType()->id;
        $event->event_date = date('Y-m-d 00:00:00');
        $event->save();

        return $event;
    }

    /**
     * @param array $args
     */
    public function run($args)
    {
        $patient_id = null;
        $patient_rows = array();
        $patient_count = 0;
        $processed_count = 0;

        $db = Yii::app()->db;
        $query = $db->createCommand('select patient_id, '
            . implode(',', static::$entry_attributes)
            . ' from '
            . static::$archived_entry_table
            . ' order by patient_id asc, created_date asc, id asc');

        foreach ($query->queryAll() as $row) {
            if ($row['patient_id'] !== $patient_id) {
                if (count($patient_rows)) {
                    if ($this->processPatient($patient_id, null, $patient_rows)) {
                        $processed_count++;
                    } else {
                        echo $patient_id . " did not process entries\n";
                    }
                    $patient_count++;
                    echo ".";
                }
                $patient_id = $row['patient_id'];
                $patient_rows = array();
            }
            $patient_rows[] = $row;
        }
        // repeat to process the final patient that has been matched on, but not completed
        if ($this->processPatient($patient_id, null, $patient_rows)) {
            $processed_count++;
        } else {
            echo $patient_id . " did not process as last patient\n";
        }
        $patient_count++;

        if (static::$archived_no_values_col) {
            $query = $db->createCommand('select ' . static::$archived_no_values_col
                . ', id from patient where '
                . static::$archived_no_values_col
                . ' is not null and '
                . static::$archived_no_values_col . ' != ""');

            foreach ($query->queryAll() as $row) {
                if ($this->processPatient($row['id'], $row[static::$archived_no_values_col])) {
                    $processed_count++;
                } else {
                    echo $row['id'] . " did not process no entries value\n";
                }
                $patient_count++;
            }
        }


        echo "\nProcessed " . $processed_count . "/" . $patient_count . " patients.\n";
    }

    /**
     * Process an individual set of records for a patient.
     *
     * @param $patient_id
     * @param $no_entries_date
     * @param $rows
     * @return bool
     * @throws Exception
     */
    public function processPatient($patient_id, $no_entries_date = null, $rows = array())
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
            $event = $this->getChangeEvent($patient);

            $element = new static::$element_class();
            $element->event_id = $event->id;
            if ($no_entries_date) {
                $element->{static::$no_values_col} = $no_entries_date;
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