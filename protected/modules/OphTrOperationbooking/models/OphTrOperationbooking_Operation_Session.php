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
 * This is the model class for table "ophtroperationbooking_operation_session".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $sequence_id
 * @property date $date
 * @property time $start_time
 * @property time $end_time
 * @property string $comments
 * @property int $available
 * @property bool $consultant
 * @property bool $paediatric
 * @property bool $anaesthetist
 * @property bool $general_anaesthetic
 * @property int $theatre_id
 * @property int $unavailablereason_id
 * @property int $max_procedures
 * @property tinyint $max_complex_bookings
 *
 * The followings are the available model relations:
 * @property OphTrOperationbooking_Operation_Sequence $sequence
 * @property OphTrOperationbooking_Operation_Theatre $theatre
 * @property OphTrOperationbooking_Operation_Session_UnavailableReason $unavailablereason
 */
class OphTrOperationbooking_Operation_Session extends BaseActiveRecordVersioned
{
    public static $DEFAULT_UNAVAILABLE_REASON = 'This session is unavailable at this time';
    public static $TOO_MANY_PROCEDURES_REASON = 'This operation has too many procedures for this session';

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $class_name
     * @return OphTrOperationbooking_Operation_Session|BaseActiveRecord static model class
     */
    public static function model($class_name = __CLASS__)
    {
        return parent::model($class_name);
    }

    /**
     * @var OphTrOperationbooking_BookingHelper
     */
    public $helper;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationbooking_operation_session';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date, start_time, end_time, theatre_id', 'required'),
            array('sequence_id, theatre_id', 'length', 'max' => 10),
            array('unavailablereason_id', 'validateRequiredIfAttrMatches', 'match_attr' => 'available', 'match_val' => false, 'message' => 'unavailable reason required if session unavailable.'),
            array('max_procedures', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 127),
            array('max_complex_bookings', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 127),
            array('sequence_id, comments, available, unavailablereason_id, consultant, paediatric, anaesthetist, general_anaesthetic, firm_id, theatre_id, start_time, end_time, deleted, default_admission_time', 'safe'),
            array('date', 'CDateValidator', 'format' => array('yyyy-mm-dd', 'd MMM yyyy')),
            array('start_time, end_time, default_admission_time', 'CDateValidator', 'format' => array('h:m:s', 'h:m')),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sequence_id, theatre_id, date, start_time, end_time, comments, available, firm_id, site_id, weekday, consultant, paediatric, anaesthetist, general_anaesthetic', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='" . get_class($this) . "'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'session_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'session_usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'theatre_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'sequence' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Sequence', 'sequence_id'),
            'unavailablereason' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Session_UnavailableReason', 'unavailablereason_id'),

            'activeBookings' => array(self::HAS_MANY, 'OphTrOperationbooking_Operation_Booking', 'session_id',
                'on' => 'activeBookings.booking_cancellation_date is null',
                'order' => 'activeBookings.display_order ASC, activeBookings.id ASC',
                'with' => array(
                    'operation',
                    'operation.event' => array('joinType' => 'join'),
                    'operation.event.episode' => array('joinType' => 'join'),
                ),
            ),
        );
    }

    /**
     * @param null
     *
     * @return bool
     */
    private function isAdmin()
    {
        $user = Yii::app()->session['user'];

        if (Yii::app()->authManager->checkAccess('admin', $user->id)) {
            return true;
        }

        return false;
    }

    public function getActiveBookingsForWard($ward_id = null)
    {
        $criteria = array(
            'with' => array(
                'operation.anaesthetic_type',
                'operation.priority',
                'operation.event' => array('joinType' => 'join'),
                'operation.event.episode' => array('joinType' => 'join'),
                'operation.event.episode.patient',
                'operation.event.episode.patient.episodes',
                'operation.event.episode.patient.contact',
                'operation.event.episode.patient.allergies',
                'operation.procedures',
                'operation.op_usermodified',
                'operation.op_user',
                'operation.eye',
                'ward',
                'user',
            ),
        );

        if ((int)$ward_id != 'All') {
            $criteria['condition'] = 'ward.id = :ward_id';
            $criteria['params'][':ward_id'] = (int)$ward_id;
        }

        return $this->activeBookings($criteria);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'sequence_id' => 'Sequence ID',
            'firm_id' => Firm::contextLabel(),
            'theatre_id' => 'Theatre',
            'start_time' => 'Start time',
            'end_time' => 'End time',
            'general_anaesthetic' => 'General anaesthetic',
            'default_admission_time' => 'Default admission time',
            'unavailablereason_id' => 'Reason unavailable',
            'max_procedures' => 'Max procedures',
            'max_complex_bookings' => 'Max complex bookings',
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
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Wrapper for getting the helper class.
     *
     * @return OphTrOperationbooking_BookingHelper
     */
    public function getHelper()
    {
        if (!isset($this->helper)) {
            $this->helper = new OphTrOperationbooking_BookingHelper();
        }

        return $this->helper;
    }

    public function getDuration()
    {
        return (mktime(substr($this->end_time, 0, 2), substr($this->end_time, 3, 2), 0, 1, 1, date('Y')) - mktime(substr($this->start_time, 0, 2), substr($this->start_time, 3, 2), 0, 1, 1, date('Y'))) / 60;
    }

    public function getBookedMinutes()
    {
        $total = 0;

        foreach ($this->activeBookings as $booking) {
            $total += $booking->operation->total_duration;
        }

        return $total;
    }

    public function getAvailableMinutes()
    {
        return $this->duration - $this->bookedminutes;
    }

    public function getMinuteStatus()
    {
        return $this->availableMinutes >= 0 ? 'available' : 'overbooked';
    }

    public function getStatus()
    {
        return $this->availableMinutes >= 0 ? 'available' : 'full';
    }

    public function getTimeSlot()
    {
        return date('H:i', strtotime($this->start_time)) . ' - ' . date('H:i', strtotime($this->end_time));
    }

    public function getFirmName()
    {
        if ($this->firm) {
            return $this->firm->name . ' (' . $this->firm->serviceSubspecialtyAssignment->subspecialty->name . ')';
        } else {
            return 'Emergency List';
        }
    }

    public function getTheatreName()
    {
        if ($this->theatre) {
            return $this->theatre->name . ' (' . $this->theatre->site->short_name . ')';
        } else {
            return 'None';
        }
    }

    /**
     * Get the total number of procedures booked into this session across all bookings.
     *
     * @return int
     */
    public function getBookedProcedureCount()
    {
        $total = 0;

        foreach ($this->activeBookings as $booking) {
            $total += $booking->procedureCount;
        }

        return $total;
    }

    /**
     * Get the number of complex bookings booked into this session
     *
     * @return int
     */
    public function getComplexBookingCount()
    {
        $total = 0;

        foreach ($this->activeBookings as $booking) {
            if ($booking->isComplex()) {
                $total++;
            }
        }
        return $total;
    }

    /**
     * Return whether the number of procedures is limited
     *
     * @return bool
     */
    public function isProcedureCountLimited()
    {
        return !is_null($this->max_procedures);
    }

    /**
     * Return the max procedure count allowed in this session
     *
     * @return int
     */
    public function getMaxProcedureCount()
    {
        return $this->max_procedures;
    }

    /**
     * Return the remaining number of procedures allowed in this session.
     *
     * @return int
     */
    public function getAvailableProcedureCount()
    {
        if ($this->isProcedureCountLimited()) {
            return $this->getMaxProcedureCount() - $this->getBookedProcedureCount();
        } else {
            return 0;
        }
    }

    /**
     * Return whether the number of complex bookings is limited
     *
     * @return bool
     */
    public function isComplexBookingCountLimited()
    {
        return !is_null($this->max_complex_bookings);
    }

    /**
     * Return the max complex booking count allowed in this session
     *
     * @return int
     */
    public function getMaxComplexBookingCount()
    {
        return $this->max_complex_bookings;
    }

    /**
     * Return the remaining number of complex bookings allowed in this session.
     *
     * @return int
     */
    public function getAvailableComplexBookingCount()
    {
        return $this->getMaxComplexBookingCount() - $this->getComplexBookingCount();
    }


    /**
     * Test whether there is place in this session for the given operation considering only the maximum number of complex bookings
     *
     * @param $operation
     *
     * @return bool
     */
    public function isTherePlaceForComplexBooking($operation)
    {
        if ($this->isComplexBookingCountLimited() &&
          $this->getComplexBookingCount() >= $this->getMaxComplexBookingCount() &&
          $operation->isComplex()) {
            return false;
        }
        return true;
    }


    /**
     * Test whether the given operation can be booked into this session.
     *
     * @param $operation
     *
     * @return bool
     */
    public function operationBookable($operation)
    {
        if (!$this->available) {
            return false;
        }

        if ($this->isProcedureCountLimited()) {
            if ($this->getBookedProcedureCount() + $operation->getProcedureCount() > $this->getMaxProcedureCount()) {
                return false;
            }
        }

        $helper = $this->getHelper();
        if ($helper->checkSessionCompatibleWithOperation($this, $operation)) {
            return false;
        }

        if (($this->date < date('Y-m-d')) && !($this->isAdmin())) {
            return false;
        }

        if (!Yii::app()->user->checkAccess('Super schedule operation') && Yii::app()->params['future_scheduling_limit'] && $this->date > date('Y-m-d', strtotime('+' . Yii::app()->params['future_scheduling_limit']))) {
            return false;
        }

        return true;
    }

    /**
     * Return the reason an operation cannot be booked into this session.
     *
     * @param $operation
     *
     * @return string
     */
    public function unbookableReason($operation)
    {
        if (!$this->available) {
            if (!$this->unavailablereason) {
                return self::$DEFAULT_UNAVAILABLE_REASON;
            } else {
                return self::$DEFAULT_UNAVAILABLE_REASON . ': ' . $this->unavailablereason->name;
            }
        }

        if ($this->isProcedureCountLimited()) {
            if ($this->getBookedProcedureCount() + $operation->getProcedureCount() > $this->getMaxProcedureCount()) {
                return self::$TOO_MANY_PROCEDURES_REASON;
            }
        }

        $helper = $this->getHelper();
        if (($errors = $helper->checkSessionCompatibleWithOperation($this, $operation))) {
            switch ($errors[0]) {
                case $helper::ANAESTHETIST_REQUIRED:
                    return "The operation requires an anaesthetist, this session doesn't have one and so cannot be booked into.";
                case $helper::CONSULTANT_REQUIRED:
                    return "The operation requires a consultant, this session doesn't have one and so cannot be booked into.";
                case $helper::PAEDIATRIC_SESSION_REQUIRED:
                    return "The operation is for a paediatric patient, this session isn't paediatric and so cannot be booked into.";
                case $helper::GENERAL_ANAESTHETIC_REQUIRED:
                    return "The operation requires general anaesthetic, this session doesn't have this and so cannot be booked into.";
            }
        }

        if (($this->date < date('Y-m-d')) && !($this->isAdmin())) {
            return 'This session is in the past and so cannot be booked into.';
        }

        if (!Yii::app()->user->checkAccess('Super schedule operation') && Yii::app()->params['future_scheduling_limit'] && $this->date > date('Y-m-d', strtotime('+' . Yii::app()->params['future_scheduling_limit']))) {
            return 'This session is outside the allowed booking window of ' . Yii::app()->params['future_scheduling_limit'] . ' and so cannot be booked into.';
        }
    }

    /**
     * Get the weekday name from the date
     *
     * @return false|string
     */
    public function getWeekdayText()
    {
        return date('l', strtotime($this->date));
    }

    /**
     * Checks made before the validation runs
     *
     * @return bool
     */
    protected function beforeValidate()
    {
        if ($this->date && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->date)) {
            $this->date = date('Y-m-d', strtotime($this->date));
        }

        // Ensure we are still compatible with any active bookings
        $helper = $this->getHelper();
        foreach ($this->activeBookings as $booking) {
            foreach ($helper->checkSessionCompatibleWithOperation($this, $booking->operation) as $error) {
                switch ($error) {
                    case $helper::ANAESTHETIST_REQUIRED:
                        $this->addError('anaesthetist', 'One or more active bookings require an anaesthetist');
                        break;
                    case $helper::CONSULTANT_REQUIRED:
                        $this->addError('consultant', 'One or more active bookings require a consultant');
                        break;
                    case $helper::PAEDIATRIC_SESSION_REQUIRED:
                        $this->addError('paediatric', 'One or more active bookings are for a child');
                        break;
                    case $helper::GENERAL_ANAESTHETIC_REQUIRED:
                        $this->addError('general_anaesthetic', 'One or more active bookings require general anaesthetic');
                }
            }
        }

        $this->validateNewSessionConflict();

        return parent::beforeValidate();
    }

    protected function beforeSave()
    {
        if ($this->date && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->date)) {
            $this->date = date('Y-m-d', strtotime($this->date));
        }

        $this->default_admission_time = $this->setDefaultAdmissionTime($this->default_admission_time, $this->start_time);

        return parent::beforeSave();
    }

    /**
     * Dissociate the session from cancelled bookings and ERODs before deletion.
     */
    protected function beforeDelete()
    {
        OphTrOperationbooking_Operation_Booking::model()->updateAll(
            array('session_id' => null),
            'session_id = :session_id and booking_cancellation_date is not null',
            array(':session_id' => $this->id)
        );

        OphTrOperationbooking_Operation_EROD::model()->updateAll(
            array('session_id' => null),
            'session_id = :session_id',
            array(':session_id' => $this->id)
        );

        return parent::beforeDelete();
    }

    /**
     * Get the next session for the given firm id.
     *
     * @param $firm_id
     *
     * @return OphTrOperationbooking_Operation_Session|null
     */
    public static function getNextSessionForFirmId($firm_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('firm_id = :firm_id and date >= :date');
        $criteria->params = array(
            'firm_id' => $firm_id,
            'date' => date('Y-m-d'),
        );
        $criteria->order = 'date asc';

        if ($session = self::model()->find($criteria)) {
            return $session;
        }
    }

    /**
     * The $attribute is required if the $params['match_attr'] is equal to the $params['match_val'].
     *
     * @param $attribute - the element attribute that must be an earlier date
     * @param $params - 'later_date' is the attribute to compare it with
     */
    public function validateRequiredIfAttrMatches($attribute, $params)
    {
        $match_a = $params['match_attr'];
        $match_v = $params['match_val'];

        if ($this->$match_a == $match_v) {
            unset($params['match_attr']);
            unset($params['match_val']);
            $v = CValidator::createValidator('required', $this, array($attribute), $params);
            $v->validate($this);
        }
    }

    /**
     * Retrieves all valid OphTrOperationbooking_Operation_Session_UnavailableReason that can be used for this
     * instance (i.e. includes the current value even if its no longer active).
     *
     * @return OphTrOperationbooking_Operation_Session_UnavailableReason[]
     */
    public function getUnavailableReasonList()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'display_order asc';

        $reasons = OphTrOperationbooking_Operation_Session_UnavailableReason::model()->findAllAtLevel(
            ReferenceData::LEVEL_INSTITUTION,
            $criteria
        );
        // just use standard list
        if (!$this->unavailablereason_id) {
            return $reasons;
        }

        $all_reasons = array();
        $r_ids = array();

        foreach ($reasons as $reason) {
            $all_reasons[] = $reason;
            $r_ids[] = $reason->id;
        }

        if (!in_array($this->unavailablereason_id, $r_ids)) {
            $all_reasons[] = $this->unavailablereason;
        }

        return $all_reasons;
    }

    /**
     * Returns an array of warning messages when any limits on the session are exceeded.
     *
     * @return array
     */
    public function getWarnings()
    {
        $warnings = array();
        $mins = $this->getAvailableMinutes();
        if ($mins < 0) {
            $warnings[] = 'Overbooked by ' . abs($mins) . ' minutes';
        }

        $procs = $this->getAvailableProcedureCount();
        if ($procs < 0) {
            $warnings[] = 'Overbooked by ' . abs($procs) . ' procedures';
        }

        return $warnings;
    }


    /**
     * Validates new sessions to find conflicts with existing sessions
     */
    protected function validateNewSessionConflict()
    {
        if ($this->isNewRecord) {
            $criteria = new CDbCriteria();

            $criteria->addCondition('theatre_id = :theatre_id');
            $criteria->params[':theatre_id'] = $this->theatre_id;

            if ($this->id) {
                $criteria->addCondition('id <> :session_id');
                $criteria->params[':session_id'] = $this->id;
            }
            $criteria->addCondition('date = :date');
            $criteria->params[':date'] = $this->date;
            $conflicts = array();
            foreach ($this->findAll($criteria) as $session) {
                $start = strtotime("$this->date $this->start_time");
                $end = strtotime("$this->date $this->end_time");

                $s_start = strtotime("$session->date $session->start_time");
                $s_end = strtotime("$session->date $session->end_time");
                if ($start < $s_end && $start >= $s_start) {
                    if (!isset($conflicts[$session->id]['start_time'])) {
                        $this->addError('start_time', "This start time conflicts with session $session->id");
                        $conflicts[$session->id]['start_time'] = 1;
                    }
                }

                if ($end > $s_start && $end <= $s_end) {
                    if (!isset($conflicts[$session->id]['end_time'])) {
                        $this->addError('end_time', "This end time conflicts with session $session->id");
                        $conflicts[$session->id]['end_time'] = 1;
                    }
                }

                if ($start < $s_start && $end > $s_end) {
                    if (!isset($conflicts[$session->id]['end_time'], $conflicts[$session->id]['start_time'])) {
                        $this->addError('start_time', "This start time conflicts with session $session->id");
                        $conflicts[$session->id]['start_time'] = 1;
                        $this->addError('end_time', "This end time conflicts with session $session->id");
                        $conflicts[$session->id]['end_time'] = 1;
                    }
                }
            }
        }
    }

    /**
     * Get firms that been used atleast once in OphTrOperationbooking_Operation_Session table
     * @param Optional variable $subspecialty_id which if given returns a list of firms also by subspecialty
     * @return array CActiveRecord[] of firms
     */
    public function getFirmsBeenUsed($subspecialty_id = null)
    {
        $criteria = new \CDbCriteria;
        $criteria->select = "s.firm_id, t.*";
        $criteria->join = 'JOIN ophtroperationbooking_operation_session s ON t.id = s.firm_id';
        $criteria->distinct = true;

        if ($subspecialty_id) {
            $criteria->join .= ' JOIN service_subspecialty_assignment ssa ON t.service_subspecialty_assignment_id = ssa.id';
            $criteria->join .= ' JOIN subspecialty sub ON ssa.subspecialty_id = sub.id';
            $criteria->addCondition("sub.id = :sub_id");
            $criteria->addCondition('t.active = 1');
            $criteria->params[':sub_id'] = $subspecialty_id;
        }

        $criteria->compare('institution_id', Yii::app()->session['selected_institution_id']);

        return \Firm::model()->findAll($criteria);
    }
}
