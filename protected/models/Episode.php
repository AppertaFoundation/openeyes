<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "episode".
 *
 * The followings are the available columns in table 'episode':
 *
 * @property int $id
 * @property int $patient_id
 * @property int $firm_id
 * @property string $start_date
 * @property string $end_date
 * @property boolean $support_services
 * @property boolean $change_tracker
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property Firm $firm
 * @property Event[] $events
 * @property EpisodeStatus $status
 */
class Episode extends BaseActiveRecordVersioned
{

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Episode the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'episode';
    }

    /**
     * Sets default scope for events such that we never pull back any rows that have deleted set to 1.
     *
     * @return array of mandatory conditions
     */
    public function defaultScope()
    {
        if ($this->getDefaultScopeDisabled()) {
            return [];
        }

        $table_alias = $this->getTableAlias(false, false);

        return array(
            'condition' => $table_alias.'.deleted = 0',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('patient_id', 'required'),
            array('patient_id, firm_id', 'length', 'max' => 10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, patient_id, firm_id, start_date, end_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'events' => array(self::HAS_MANY, 'Event', 'episode_id', 'order' => ' events.event_date asc, events.created_date asc'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'status' => array(self::BELONGS_TO, 'EpisodeStatus', 'episode_status_id'),
            'diagnosis' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'referralAssignment' => array(self::HAS_ONE, 'ReferralEpisodeAssignment', 'episode_id'),
        );
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'patient_id' => 'Patient',
            'firm_id' => 'Firm',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'episode_status_id' => 'Current Status',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('patient_id', $this->patient_id, true);
        $criteria->compare('firm_id', $this->firm_id, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns true if an event of the given type exists within this episode.
     *
     * @param int   $eventTypeId
     * @param Event $currentEvent
     *
     * @return bool
     */
    public function hasEventOfType($eventTypeId, $currentEvent = null)
    {
        foreach ($this->events as $event) {
            if ($event->event_type_id == $eventTypeId) {
                if (!$currentEvent || $currentEvent->id != $event->id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the episode for a patient and subspecialty if there is one.
     *
     * @param int $subspecialtyId id of the subspecialty
     * @param int $patientId      id of the patient
     *
     * @return object $episode if found, null otherwise
     *
     * @deprecated - since 1.4 use getCurrentEpisodeByFirm instead
     */
    public function getBySubspecialtyAndPatient($subspecialtyId, $patientId, $onlyReturnOpen = true)
    {
        $criteria = new CDbCriteria();
        $criteria->join = 'LEFT JOIN firm ON t.firm_id = firm.id
			LEFT JOIN service_subspecialty_assignment servicesubspecialtyAssignment ON
				servicesubspecialtyAssignment.id = firm.service_subspecialty_assignment_id
			LEFT JOIN patient ON t.patient_id = patient.id';
        $criteria->addCondition('servicesubspecialtyAssignment.subspecialty_id = :subspecialty_id');
        $criteria->addCondition('patient.id = :patient_id');
        if ($onlyReturnOpen) {
            $criteria->addCondition('t.end_date IS NULL');
        }
        $criteria->params = array(
            ':subspecialty_id' => $subspecialtyId,
            ':patient_id' => $patientId,
        );

        return self::model()->find($criteria);
    }

    public function getPrincipalDiagnosisDisorderTerm()
    {
        if ($disorder = $this->getPrincipalDisorder()) {
            return $disorder->term;
        } else {
            return 'none';
        }
    }

    /**
     * get the current episode for the given firm, based on the firm subspecialty - if firm has no
     * subspecialty, will return a support services episode.
     *
     * wrapper for getCurrentEpisodeBySubspecialtyId($patient_id, $subspecialty_id, $include_closed)
     */
    public static function getCurrentEpisodeByFirm($patient_id, $firm, $include_closed = false)
    {
        if (!$firm) {
            throw new Exception('Invalid firm');
        }

        return self::model()->getCurrentEpisodeBySubspecialtyId($patient_id, $firm->getSubspecialtyID(), $include_closed);
    }

    /**
     * Get the current episode for the patient id and subspecialty_id (if this is null, looking for support services episode).
     *
     * return Episode
     */
    public static function getCurrentEpisodeBySubspecialtyId($patient_id, $subspecialty_id, $include_closed = false)
    {
        $where = $include_closed ? '' : ' AND e.end_date IS NULL';

        // Check for an open episode for this patient and firm's service with a referral
        if (!is_null($subspecialty_id)) {
            $episode = Yii::app()->db->createCommand()
                ->select('e.id AS eid')
                ->from('episode e')
                ->join('firm f', 'e.firm_id = f.id')
                ->join('service_subspecialty_assignment s_s_a', 'f.service_subspecialty_assignment_id = s_s_a.id')
                ->where('e.deleted = false'.$where.' AND e.patient_id = :patient_id AND s_s_a.subspecialty_id = :subspecialty_id', array(
                    ':patient_id' => $patient_id,
                    ':subspecialty_id' => $subspecialty_id,
                ))
                ->queryRow();
        } else {
            $episode = Yii::app()->db->createCommand()
                ->select('e.id AS eid')
                ->from('episode e')
                ->where('e.deleted = false AND e.legacy = false AND e.support_services = TRUE '.$where.' AND e.patient_id = :patient_id', array(
                    ':patient_id' => $patient_id,
                ))
                ->queryRow();
        }

        if (!$episode || !$episode['eid']) {
            // No episode found
            return;
        }
        // return the episode object
        return self::model()->findByPk($episode['eid']);
    }

    /**
     * @param Patient $patient
     * @param boolean $create - defaults to true
     * @return Episode|null
     */
    public static function getChangeEpisode(Patient $patient, $create = true)
    {
        $episode = self::model()->with('events')->findByAttributes(array(
            'patient_id' => $patient->id,
            'change_tracker' => true
        ));
        if (!$episode && $create) {
            // note that requesting code is responsible for checking/saving new episode
            // if necessary. This is to allow the episode to be saved in a transaction
            // which completes successfully.
            $episode = new Episode();
            $episode->patient_id = $patient->id;
            $episode->change_tracker = true;
        }
        return $episode;
    }

    /**
     * Get the most recent event by the given type in this episode.
     *
     * @param $event_type_id
     *
     * @return Event
     */
    public function getMostRecentEventByType($event_type_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('episode_id', $this->id);
        $criteria->compare('event_type_id', $event_type_id);
        $criteria->order = 'event_date desc, created_date desc';
        $criteria->limit = 1;

        return Event::model()->find($criteria);
    }

    /**
     * Get all the events of the given type for this episode.
     *
     * @param $event_type_id
     *
     * @return Event[]
     */
    public function getAllEventsByType($event_type_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('episode_id', $this->id);
        $criteria->compare('event_type_id', $event_type_id);
        $criteria->order = 'event_date asc';

        return Event::model()->findAll($criteria);
    }

    /**
     * get the latest event for this episode.
     *
     * @return Event
     */
    public function getLatestEvent()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('episode_id = :eid');
        $criteria->params = array(':eid' => $this->id);
        $criteria->order = 't.event_date DESC, t.created_date DESC';
        $criteria->limit = 1;

        return Event::model()->with('episode')->find($criteria);
    }

    /**
     * Get all elements of the given type from this Episode.
     *
     * @param $element_type
     * @param int $exclude_event_id
     *
     * @return BaseEventTypeElement[]
     */
    public function getElementsOfType($element_type, $exclude_event_id = null)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'event.episode_id = :episode_id';
        $criteria->params = array(':episode_id' => $this->id);
        if ($exclude_event_id) {
            $criteria->condition .= ' AND event.id != :exclude_event_id';
            $criteria->params[':exclude_event_id'] = $exclude_event_id;
        }
        $criteria->order = 't.id DESC';
        $criteria->join = 'JOIN event ON event.id = t.event_id and event.deleted = 0';
        $kls = $element_type->class_name;

        return $kls::model()->findAll($criteria);
    }

    /**
     * get the subspecialty for this episode.
     *
     * @return Subspecialty
     */
    public function getSubspecialty()
    {
        if ($firm = $this->firm) {
            return $firm->getSubspecialty();
        }

        // no subspecialty for episodes without firms
        return;
    }

    /**
     * get the subspecialty text for the episode.
     *
     * @return string
     */
    public function getSubspecialtyText()
    {
        if ($subspecialty = $this->getSubspecialty()) {
            return $subspecialty->name;
        } else {
            if ($this->support_services) {
                return 'Support Services';
            } elseif ($this->legacy) {
                return 'Legacy';
            }
        }
    }

    /**
     * get the subspecialty id for this episode.
     *
     * @return int|null
     */
    public function getSubspecialtyID()
    {
        if ($ss = $this->getSubspecialty()) {
            return $ss->id;
        }

        return;
    }

    public function save($runValidation = true, $attributes = null, $allow_overriding = false, $save_version = false)
    {
        $previous = self::model()->findByPk($this->id);

        if (parent::save($runValidation, $attributes, $allow_overriding, $save_version)) {
            if ($previous && $previous->episode_status_id != $this->episode_status_id) {
                $this->audit('episode', 'change-status', $this->episode_status_id);
            }

            return true;
        }

        return false;
    }

    public function getHidden()
    {
        if (isset(Yii::app()->getController()->event) && Yii::app()->getController()->event->episode_id == $this->id) {
            return false;
        }

        if (isset(Yii::app()->session['episode_hide_status'][$this->id])) {
            return !Yii::app()->session['episode_hide_status'][$this->id];
        }

        if (isset(Yii::app()->getController()->episode)) {
            return Yii::app()->getController()->episode->id != $this->id || $this->end_date != null;
        }

        return true;
    }

    public function getOpen()
    {
        return $this->end_date == null;
    }

    public function behaviors()
    {
        return array(
            'OeDateFormat' => array(
                'class' => 'application.behaviors.OeDateFormat',
                'date_columns' => [],
                'fuzzy_date_field' => 'disorder_date',
            ),
        );
    }

    protected function afterSave()
    {
        foreach (SecondaryDiagnosis::model()->findAll('patient_id=? and disorder_id=?', array($this->patient_id, $this->disorder_id)) as $sd) {
            if ($this->eye_id == $sd->eye_id || ($this->eye_id == 3 && in_array($sd->eye_id, array(1, 2)))) {
                $sd->delete();
            } elseif (in_array($this->eye_id, array(1, 2)) && $sd->eye_id == 3) {
                $sd->eye_id = ($this->eye_id == 1 ? 2 : 1);
                $sd->save();
            }
        }
    }

    public function setPrincipalDiagnosis($disorder_id, $eye_id, $disorder_date = false)
    {
        if (!$disorder_date) {
            $disorder_date = date('Y-m-d');
        }
        $this->disorder_id = $disorder_id;
        $this->eye_id = $eye_id;
        $this->disorder_date = $disorder_date;
        if (!$this->save()) {
            throw new Exception('Unable to set episode principal diagnosis/eye: '.print_r($this->getErrors(), true));
        }

        $this->audit('episode', 'set-principal-diagnosis');
    }

    public function getDisplayDate()
    {
        if (!$this->disorder_date) {
            return null;
        }
        return \Helper::formatFuzzyDate($this->disorder_date);
    }

    public function audit($target, $action, $data = null, $log = false, $properties = array())
    {
        $properties['episode_id'] = $this->id;
        $properties['patient_id'] = $this->patient_id;
        parent::audit($target, $action, $data, $log, $properties);
    }

    /**
     * @TODO change to configuration
     * @return string
     */
    public static function getEpisodeLabel()
    {
                return 'Specialty';
    }

    /**
     * @TODO Change to configuration
     * @return string
     */
    public static function getEpisodeLabelPlural()
    {
        return 'Specialties';
    }
}
