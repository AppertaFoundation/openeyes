<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */

use OEModule\OphCiExamination\models\SocialHistoryDrivingStatus;

class SocialHistoryMigrateCommand extends PatientLevelMigration
{
    protected $event_type_cls = 'OphCiExamination';
    // Original table is renamed to this during the module database migration
    protected static $archived_entry_table = 'archive_socialhistory';
    protected static $element_class = 'OEModule\OphCiExamination\models\SocialHistory';
    protected static $entry_attributes = array(
        'occupation_id',
        'smoking_status_id',
        'accommodation_id',
        'comments',
        'type_of_job',
        'carer_id',
        'alcohol_intake',
        'substance_misuse_id'
    );
    public function getHelp()
    {
        return "Migrates the original Family History record to an examination event in change tracker episode\n";
    }

    protected $driving_status_lkup = array();

    /**
     * Cached lookup for driving status
     *
     * @param $id
     * @return mixed
     */
    public function getDrivingStatus($id)
    {
        if (!array_key_exists($id, $this->driving_status_lkup)) {
            $this->driving_status_lkup[$id] = SocialHistoryDrivingStatus::model()->findByPk($id);
        }
        return $this->driving_status_lkup[$id];
    }

    /**
     * @param $id
     * @return array
     */
    public function getDrivingStatuses($patient_id)
    {
        $db = Yii::app()->db;
        $query = $db->createCommand('select driving_status_id '
            . 'from archive_socialhistory_driving_status_assignment ass '
            . 'join archive_socialhistory ent on ass.socialhistory_id = ent.id where ent.patient_id = ' . $patient_id);
        $res = [];
        foreach ($query->queryAll() as $row) {
            $res[] = $this->getDrivingStatus($row['driving_status_id']);
        }
        return $res;
    }
    /**
     * @param $patient_id
     * @param null $no_entries_date
     * @param array $rows
     * @return bool
     * @throws Exception
     */
    public function processPatient($patient_id, $no_entries_date = null, $rows = array())
    {
        $patient = Patient::model()->findByPk($patient_id);

        if ($this->getApi()->getLatestElement(static::$element_class, $patient)) {
            return false;
        }

        // get the driving status assignments
        $rows[0]['driving_statuses'] = $this->getDrivingStatuses($rows[0]['patient_id']);

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $event = $this->getChangeEvent($patient);

            $element = new static::$element_class();
            $element->attributes = $rows[0];
            $element->event_id = $event->id;
            $element->save();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        return true;
    }
}
