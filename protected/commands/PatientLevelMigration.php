<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class PatientLevelMigration extends CConsoleCommand
{
    /**
     * Module name where the data is moving to
     * @var
     */
    protected $event_type;
    protected $api;

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
            $this->api = Yii::app()->moduleAPI->get($this->event_type);
        }
        return $this->api;
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
        $event = new Event();
        if ($episode->isNewRecord) {
            $episode->save();
        }
        $event->episode_id = $episode->id;
        $event->event_type_id = $this->getApi()->getEventType()->id;
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