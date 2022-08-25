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

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "et_ophtroperationbooking_scheduleope".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $schedule_options_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property Element_OphTrOperationbooking_ScheduleOperation_ScheduleOptions $schedule_options
 * @property OphTrOperationbooking_ScheduleOperation_PatientUnavailable[] $patient_unavailables
 */
class Element_OphTrOperationbooking_ScheduleOperation extends BaseEventTypeElement
{
    use HasFactory;
    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophtroperationbooking_scheduleope';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, schedule_options_id, ', 'safe'),
            array('schedule_options_id, ', 'required'),
            array('patient_unavailables', 'validateNoDateRangeOverlap'),
            array('patient_unavailables', 'validateNoBookingCollision'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, schedule_options_id, ', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'schedule_options' => array(self::BELONGS_TO, 'OphTrOperationbooking_ScheduleOperation_Options', 'schedule_options_id'),
            'patient_unavailables' => array(self::HAS_MANY, 'OphTrOperationbooking_ScheduleOperation_PatientUnavailable', 'element_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'schedule_options_id' => 'Schedule options',
            'patient_unavailables' => 'Patient Unavailable Periods',
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
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('schedule_options_id', $this->schedule_options_id);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    /**
     * Get the operation booking for the event.
     *
     * @return OphTrOperationbooking_Operation_Booking
     */
    public function getCurrentBooking()
    {
        if ($this->event_id && ($op = Element_OphTrOperationbooking_Operation::model()->with('booking')->find('event_id = ?', array($this->event_id)))) {
            return $op->booking;
        }
    }

    /**
     * validate a date is earlier or equal to another.
     *
     * @param $attribute - the element attribute that must be an earlier date
     * @param $params - 'later_date' is the attribute to compare it with
     */
    public function validateNoDateRangeOverlap($attribute, $params)
    {
        $pstart = 'start_date';
        $pend = 'end_date';
        foreach ($this->$attribute as $i => $dr) {
            for ($j = $i + 1; $j < count($this->$attribute); ++$j) {
                if ($dr->$pstart < $this->{$attribute}[$j]->$pend && $dr->$pend > $this->{$attribute}[$j]->$pstart) {
                    $this->addError($attribute, 'Data ranges cannot overlap');
                }
            }
        }
    }

    /**
     * Ensure that if there is a current booking on this event, the patient unavailables dates don't collide with the booking.
     *
     * @param $attribute
     * @param $params
     */
    public function validateNoBookingCollision($attribute, $params)
    {
        if ($booking = $this->getCurrentBooking()) {
            if (!$this->isPatientAvailable($booking->session_date)) {
                $this->addError($attribute, 'Cannot set the patient to be unavailable on the day of the current booking.');
            }
        }
    }

    /**
     * Make sure patient_unavailables are validated.
     */
    public function afterValidate()
    {
        foreach ($this->patient_unavailables as $i => $unavailable) {
            if (!$unavailable->validate()) {
                foreach ($unavailable->getErrors() as $fld => $err) {
                    if ($fld) {
                        $this->addError('patient_unavailables_'. $i .'_'.$fld, $this->getAttributeLabel('patient_unavailables').
                            ' ('.($i + 1).'): '.implode(', ', $err));
                    } else {
                        $this->addError('patient_unavailables', $this->getAttributeLabel('patient_unavailables').
                            ' ('.($i + 1).'): '.implode(', ', $err));
                    }
                }
            }
        }
    }

    /**
     * Set the patient unavailable objects for this element.
     *
     * @param $unavailables
     *
     * @throws Exception
     */
    public function updatePatientUnavailables($unavailables)
    {
        $curr_by_id = array();
        $save = array();
        $criteria = new CDbCriteria();
        $criteria->addCondition('element_id = :eid');
        $criteria->params[':eid'] = $this->id;
        foreach (OphTrOperationbooking_ScheduleOperation_PatientUnavailable::model()->findAll($criteria) as $c_pun) {
            $curr_by_id[$c_pun->id] = $c_pun;
        }

        foreach ($unavailables as $unavailable) {
            if (@$unavailable['id']) {
                // it's an existing one
                $obj = $curr_by_id[$unavailable['id']];
                unset($curr_by_id[$unavailable['id']]);
            } else {
                $obj = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
                $obj->element_id = $this->id;
            }
            $dosave = false;
            foreach (array('start_date', 'end_date', 'reason_id') as $attr) {
                if ($obj->$attr != $unavailable[$attr]) {
                    $dosave = true;
                    $obj->$attr = $unavailable[$attr];
                }
            }
            if ($dosave) {
                $save[] = $obj;
            }
        }
        foreach ($save as $s) {
            if (!$s->save()) {
                throw new Exception('Unable to save Patient Unavailable '.print_r($s->getErrors(), true));
            };
        }

        foreach ($curr_by_id as $id => $d) {
            if (!$d->delete()) {
                throw new Exception('Unable to delete Patient Unavailable '.print_r($d->getErrors(), true));
            }
        }
    }

    public $_unavailable_dates;

    /**
     * make sure the cached dates array is reset when patient_unavailables is updated.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed|void
     */
    public function __set($name, $value)
    {
        if ($name == 'patient_unavailables') {
            $this->_unavailable_dates = null;
        }
        parent::__set($name, $value);
    }

    /**
     * Given a date (yyyy-mm-dd) check if the patient is available, and return true or false as appropriate.
     *
     * @param $date
     *
     * @return bool
     */
    public function isPatientAvailable($date)
    {
        if (!$this->_unavailable_dates) {
            $this->_unavailable_dates = array();
            // cache the patient unavailable dates as we don't want to do this every time
            foreach ($this->patient_unavailables as $step => $unavailable) {
                if ($unavailable->validate()) {
                    $dt = strtotime($unavailable->start_date);
                    while ($dt <= strtotime($unavailable->end_date)) {
                        $this->_unavailable_dates[] = date('Y-m-d', $dt);
                        $dt += 86400;
                    }
                }
            }
        }
        if (empty($this->_unavailable_dates)) {
            return true;
        }

        return !in_array($date, $this->_unavailable_dates);
    }

    public function getContainer_view_view()
    {
        return false;
    }
}
