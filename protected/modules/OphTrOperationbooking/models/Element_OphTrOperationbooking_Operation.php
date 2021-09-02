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
 * This is the model class for table "et_ophtroperationbooking_operation".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $consultant_required
 * @property int $any_grade_of_doctor
 * @property int $senior_fellow_to_do
 * @property int $site_id
 * @property int $priority_id
 * @property string $decision_date
 * @property string $comments
 * @property string $comments_rtt
 * @property string $on_hold_reason
 * @property string $on_hold_comment
 * @property int $referral_id
 * @property int $rtt_id
 * @property int $overnight_stay_required_id
 * @property int $complexity
 * @property tinyint $is_golden_patient
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property Eye $eye
 * @property OphTrOperationbooking_Operation_Procedures $procedures
 * @property AnaestheticType[] $anaesthetic_type
 * @property Site $site
 * @property Element_OphTrOperationbooking_Operation_Priority $priority
 * @property Referral $refferal
 * @property RTT $fixed_rtt - Because the active referral can change over time, we lock the operation booking to a specific RTT at the time of booking.
 * @property OphTrOperationbooking_Overnight_Stay_Required $overnight_stay_required
 */
class Element_OphTrOperationbooking_Operation extends BaseEventTypeElement
{
    public $count;
    public $reschedule;

    const LETTER_INVITE = 0;
    const LETTER_REMINDER_1 = 1;
    const LETTER_REMINDER_2 = 2;
    const LETTER_GP = 3;
    const LETTER_REMOVAL = 4;

    // these reflect an actual status, relating to actions required rather than letters sent
    const STATUS_WHITE = 0; // no action required.  the default status.
    const STATUS_PURPLE = 1; // no invitation letter has been sent
    const STATUS_GREEN1 = 2; // it's two weeks since an invitation letter was sent with no further letters going out
    const STATUS_GREEN2 = 3; // it's two weeks since 1st reminder was sent with no further letters going out
    const STATUS_ORANGE = 4; // it's two weeks since 2nd reminder was sent with no further letters going out
    const STATUS_RED = 5; // it's one week since gp letter was sent and they're still on the list
    const STATUS_NOTWAITING = null;

    // Other complexity levels may be added later
    const COMPLEXITY_LOW = 0;
    const COMPLEXITY_MEDIUM = 5;
    const COMPLEXITY_HIGH = 10;

    public static $complexity_captions = array(
        self::COMPLEXITY_LOW => 'Low',
        self::COMPLEXITY_MEDIUM => 'Medium',
        self::COMPLEXITY_HIGH => 'High'
    );

    public static $complexity_colors = array(
        self::COMPLEXITY_LOW => 'green',
        self::COMPLEXITY_MEDIUM => 'amber',
        self::COMPLEXITY_HIGH => 'red'
    );



    const OVERNIGHT_STAY_NOT_REQUIRED_ID = 1;

    public $service;

    public $anaesthetist_required_ids = array();

    protected $errorExceptions = array(
      'Element_OphTrOperationbooking_Operation_procedures' => 'select_procedure_id_procs',
    );

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
        return 'et_ophtroperationbooking_operation';
    }

    public function init()
    {
        $this->anaesthetist_required_ids = array(
            Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('code=:code', array(':code' => 'Sed'))->queryScalar(),
            Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('code=:code', array(':code' => 'GA'))->queryScalar(),
        );

        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('consultant_required, senior_fellow_to_do, named_consultant_id, any_grade_of_doctor, decision_date, special_equipment_details, comments,comments_rtt','safe'),
            array('site_id, anaesthetic_choice_id, stop_medication, stop_medication_details, total_duration, operation_cancellation_date',       'safe'),
            array('status_id, cancellation_comment, cancellation_user_id, latest_booking_id, referral_id, special_equipment',                   'safe'),
            array('priority_id, eye_id, organising_admission_user_id, overnight_booking_required_id, complexity, is_golden_patient',    'safe'),
            array('on_hold_reason, on_hold_comment', 'safe'),

            array('named_consultant_id', 'RequiredIfFieldValidator', 'field' => 'consultant_required', 'value' => true, 'on' => 'insert'),
            array('cancellation_comment', 'length', 'max' => 200),
            array('procedures', 'required', 'message' => 'At least one procedure must be entered'),
            array('total_duration', 'validateDuration'),
            array('referral_id', 'validateReferral'),
            array('decision_date', 'OEDateValidatorNotFuture'),
            array('eye_id, consultant_required, overnight_stay_required_id', 'required'),
            array('anaesthetic_choice_id, stop_medication', 'required', 'on' => 'insert'),
            array('complexity', 'required'),
            array('stop_medication_details', 'RequiredIfFieldValidator', 'field' => 'stop_medication', 'value' => true),
            array('site_id, priority_id, decision_date', 'required'),
            array('special_equipment', 'required', 'on' => 'insert'),
            array('special_equipment_details', 'RequiredIfFieldValidator', 'field' => 'special_equipment', 'value' => true),
            array('organising_admission_user_id', 'required', 'on' => 'insert'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, consultant_required, site_id, priority_id, decision_date, comments, comments_rtt, on_hold_reason, on_hold_comment', 'safe', 'on' => 'search'),
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
            'op_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'op_usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'procedureItems' => array(self::HAS_MANY, 'OphTrOperationbooking_Operation_Procedures', 'element_id'),
            'procedures' => array(self::MANY_MANY, 'Procedure', 'ophtroperationbooking_operation_procedures_procedures(element_id, proc_id)'),
            'anaesthetic_type_assignments' => array(self::HAS_MANY, 'OphTrOperationbooking_AnaestheticAnaestheticType', 'et_ophtroperationbooking_operation_id'),
            'anaesthetic_type' => array(self::HAS_MANY, 'AnaestheticType', 'anaesthetic_type_id',
                'through' => 'anaesthetic_type_assignments', ),
            'anaesthetic_choice' => array(self::BELONGS_TO, 'OphTrOperationbooking_Anaesthetic_Choice', 'anaesthetic_choice_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'priority' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Priority', 'priority_id'),
            'status' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Status', 'status_id'),
            'date_letter_sent' => array(self::HAS_ONE, 'OphTrOperationbooking_Operation_Date_Letter_Sent', 'element_id', 'order' => 'date_letter_sent.id DESC'),
            'cancellation_user' => array(self::BELONGS_TO, 'User', 'cancellation_user_id'),
            'cancellation_reason' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Cancellation_Reason', 'cancellation_reason_id'),
            'cancelledBookings' => array(self::HAS_MANY, 'OphTrOperationbooking_Operation_Booking', 'element_id', 'condition' => 'booking_cancellation_date is not null', 'order' => 'booking_cancellation_date'),
            'booking' => array(self::HAS_ONE, 'OphTrOperationbooking_Operation_Booking', 'element_id', 'condition' => 'booking_cancellation_date is null'),
            'cancelledBooking' => array(self::HAS_ONE, 'OphTrOperationbooking_Operation_Booking', 'element_id', 'condition' => 'booking_cancellation_date is not null'),
            'latestBooking' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Booking', 'latest_booking_id'),
            'firstBooking' => array(self::HAS_ONE, 'OphTrOperationbooking_Operation_Booking', 'element_id', 'order' => 'created_date ASC'),
            'allBookings' => array(self::HAS_MANY, 'OphTrOperationbooking_Operation_Booking', 'element_id'),
            'referral' => array(self::BELONGS_TO, 'Referral', 'referral_id'),
            'fixed_rtt' => array(self::BELONGS_TO, 'RTT', 'rtt_id'),
            'organising_admission_user' => array(self::BELONGS_TO, 'User', 'organising_admission_user_id'),
            'consultant' => array(self::BELONGS_TO, 'User', 'named_consultant_id'),
            'overnight_stay_required' => array(self::BELONGS_TO, 'OphTrOperationbooking_Overnight_Stay_Required', 'overnight_stay_required_id'),
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
            'eye_id' => 'Eyes',
            'procedures' => 'Operations',
            'consultant_required' => 'Named consultant list',
            'any_grade_of_doctor' => 'Any other doctor to do',
            'anaesthetic_type_id' => 'Anaesthetic type',
            'anaesthetic_choice_id' => 'Anaesthetic choice is',
            'stop_medication' => 'Patient needs to stop medication',
            'stop_medication_details' => 'Please provide details',
            'site_id' => 'Site',
            'priority_id' => 'Priority',
            'decision_date' => 'Decision date',
            'special_equipment' => 'Special equipment required',
            'special_equipment_details' => 'Please specify',
            'comments' => 'Add comments (Scheduling guidance for admissions team)',
            'comments_rtt' => 'Add RTT comments',
            'referral_id' => 'Referral',
            'rtt_id' => 'RTT',
            'organising_admission_user_id' => 'Doctor organising admission',
            'senior_fellow_to_do' => 'Senior fellow to do',
            'named_consultant_id' => 'Consultant',
            'anaesthetic_type' => 'Anaesthetic Type',
            'overnight_stay_required_id' => 'Overnight stay required',
            'complexity' => 'Complexity',
            'is_golden_patient' => 'Suitable as golden patient',
            'cancellation_reason_id' => ($this->reschedule ? 'Reschedule Reason' : 'Cancellation Reason'),
            'on_hold_comment' => 'On Hold Comment',
            'on_hold_reason' => 'On Hold Reason',
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

        $criteria->compare('eye_id', $this->eye_id);
        $criteria->compare('procedures', $this->procedures);
        $criteria->compare('consultant_required', $this->consultant_required);
        $criteria->compare('any_grade_of_doctor', $this->any_grade_of_doctor);
        $criteria->compare('site_id', $this->site_id);
        $criteria->compare('priority_id', $this->priority_id);
        $criteria->compare('decision_date', $this->decision_date);
        $criteria->compare('comments', $this->comments);
        $criteria->compare('comments_rtt', $this->comments_rtt);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    /**
     * Set default values for forms on create.
     * @param Patient $patient
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        $patient_id = (int) $_REQUEST['patient_id'];
        $firm = Yii::app()->getController()->firm;
        $episode = Episode::getCurrentEpisodeByFirm($patient_id, $firm);
        if ($episode && $episode->diagnosis) {
            $this->eye_id = $episode->eye_id;
        }
        $this->site_id = Yii::app()->session['selected_site_id'];

        $this->senior_fellow_to_do = null;
        $this->any_grade_of_doctor = null;

        // TODO: determine if this is necessary anymore with Patient being passed in
        if ($patient = Patient::model()->findByPk($patient_id)) {
            $key = $patient->isChild() ? 'ophtroperationbooking_default_anaesthetic_child' : 'ophtroperationbooking_default_anaesthetic';

            if (isset(Yii::app()->params[$key])) {
                if ($at = AnaestheticType::model()->find('code=?', array(Yii::app()->params[$key]))) {
                    $this->anaesthetic_type = array($at);
                }
            }
        }
        $this->special_equipment = false;
        $preassesment_booking_default_value = Yii::app()->params['pre_assessment_booking_default_value'];
        $this->overnight_stay_required_id = self::OVERNIGHT_STAY_NOT_REQUIRED_ID;

        $this->organising_admission_user_id = Yii::app()->user->id;

        if ($priority = OphTrOperationbooking_Operation_Priority::model()->notDeleted()->find('`default`=1')) {
            $this->priority_id = $priority->id;
        }
    }

    public function getproc_defaults()
    {
        $ids = array();
        foreach (OphTrOperationbooking_Operation_Defaults::model()->findAll() as $item) {
            $ids[] = $item->value_id;
        }

        return $ids;
    }

    protected $_has_bookings = null;
    protected $_original_referral_id = null;

    public function afterFind()
    {
        parent::afterFind();
        $this->_has_bookings = ($this->allBookings) ? true : false;
        $this->_original_referral_id = $this->referral_id;
    }

    /**
     * @inheritdoc
     */

    public function beforeValidate()
    {
        if (!isset($this->overnight_stay_required_id) || is_null($this->overnight_stay_required_id)) {
            $this->overnight_stay_required_id = self::OVERNIGHT_STAY_NOT_REQUIRED_ID;
        }
        return parent::beforeValidate();
    }

    /**
     * Sets flags based on element properties.
     *
     * @return bool
     */
    protected function beforeSave()
    {
        if (!$this->status_id) {
            $this->status_id = 1; //@TODO: change hardcoded id to a query
        }

        if (!$this->stop_medication) {
            $this->stop_medication_details = null;
        }
        if (!$this->special_equipment) {
            $this->special_equipment_details = null;
        }

        return parent::beforeSave();
    }

    /**
     * Stores procedures identified by the given procedure ids against this element.
     *
     * @param array $procedure_ids
     *
     * @throws Exception
     */
    public function updateProcedures($procedure_ids)
    {
        $existing_ids = array();

        foreach (OphTrOperationbooking_Operation_Procedures::model()->findAll('element_id = :elementId', array(':elementId' => $this->id)) as $item) {
            $existing_ids[$item->proc_id] = $item->id;
        }
        foreach ($procedure_ids as $id) {
            if (in_array($id, $existing_ids)) {
                unset($existing_ids[$id]);
            } else {
                $item = new OphTrOperationbooking_Operation_Procedures();
                $item->element_id = $this->id;
                $item->proc_id = $id;

                if (!$item->save()) {
                    throw new Exception('Unable to save MultiSelect item: '.print_r($item->getErrors(), true));
                }
            }
        }

        OphTrOperationbooking_Operation_Procedures::model()->deleteByPk(array_values($existing_ids));
    }

    /**
     * Update the Anaesthetic Type associated with the element.
     *
     * @param $type_ids
     * @throws Exception
     */
    public function updateAnaestheticType($type_ids)
    {
        $curr_by_id = array();
        foreach ($this->anaesthetic_type as $type) {
            $curr_by_id[$type->id] = OphTrOperationbooking_AnaestheticAnaestheticType::model()->findByAttributes(array(
                'et_ophtroperationbooking_operation_id' => $this->id,
                'anaesthetic_type_id' => $type->id
            ));
        }

        if (!empty($type_ids)) {
            foreach ($type_ids as $type_id) {
                if (!isset($curr_by_id[$type_id])) {
                    $type = new OphTrOperationbooking_AnaestheticAnaestheticType();
                    $type->et_ophtroperationbooking_operation_id = $this->id;
                    $type->anaesthetic_type_id = $type_id;

                    if (!$type->save()) {
                        throw new Exception('Unable to save anaesthetic agent assignment: '.print_r($type->getErrors(), true));
                    }
                } else {
                    unset($curr_by_id[$type_id]);
                }
            }
        }
        foreach ($curr_by_id as $type) {
            if (!$type->delete()) {
                throw new Exception('Unable to delete anaesthetic agent assignment: '.print_r($type->getErrors(), true));
            }
        }
    }

    protected function afterValidate()
    {
        if ($this->booking) {
            if ($this->consultant_required && !$this->booking->session->consultant) {
                $this->addError('consultant', 'The booked session does not have a consultant present, you must change the session or cancel the booking before making this change');
            }
        }

        if ( !count($this->anaesthetic_type_assignments)) {
            $this->addError('anaesthetic_type', 'Type cannot be empty.');
        }

        return parent::afterValidate();
    }

    /**
     * Wrapper function to get the element patient.
     *
     * @return null|Patient
     */
    public function getPatient()
    {
        return $this->event->episode->patient;
    }

    /**
     * Wrapper function to get the element firm.
     *
     * @return Firm
     */
    public function getFirm()
    {
        return $this->event->episode->firm;
    }

    public static function getLetterOptions()
    {
        return array(
            '' => 'Any',
            self::LETTER_INVITE => 'Invitation',
            self::LETTER_REMINDER_1 => '1st Reminder',
            self::LETTER_REMINDER_2 => '2nd Reminder',
            self::LETTER_GP => 'Refer to '.\SettingMetadata::model()->getSetting('gp_label'),
        );
    }

    public function getLetterType()
    {
        $letterTypes = $this->getLetterOptions();
        $letterType = ($this->getDueLetter() !== null && isset($letterTypes[$this->getDueLetter()])) ? $letterTypes[$this->getDueLetter()] : false;

        if ($letterType == false && $this->getLastLetter() == self::LETTER_GP) {
            $letterType = 'Refer to '.\SettingMetadata::model()->getSetting('gp_label');
        }

        return $letterType;
    }

    public function getHas_gp()
    {
        return $this->getDueLetter() != self::LETTER_GP || ($this->getPatient()->practice && $this->getPatient()->practice->contact->address);
    }

    public function getHas_address()
    {
        if ($patient = $this->getPatient()) {
            return (bool) $patient->contact->correspondAddress;
        }

        return false;
    }

    public function getLastLetter()
    {
        if (!$this->date_letter_sent) {
            return;
        }
        if (
            $this->date_letter_sent->date_invitation_letter_sent and    // an invitation letter has been sent
            is_null($this->date_letter_sent->date_1st_reminder_letter_sent) and // but no 1st reminder
            is_null($this->date_letter_sent->date_2nd_reminder_letter_sent) and // no 2nd reminder
            is_null($this->date_letter_sent->date_gp_letter_sent) // no gp letter
        ) {
            return self::LETTER_INVITE;
        }
        if (
            $this->date_letter_sent->date_invitation_letter_sent and    // an invitation letter has been sent
            $this->date_letter_sent->date_1st_reminder_letter_sent and // and a 1st reminder
            is_null($this->date_letter_sent->date_2nd_reminder_letter_sent) and // but no 2nd reminder
            is_null($this->date_letter_sent->date_gp_letter_sent) // no gp letter
        ) {
            return self::LETTER_REMINDER_1;
        }
        if (
            $this->date_letter_sent->date_invitation_letter_sent and    // an invitation letter has been sent
            $this->date_letter_sent->date_1st_reminder_letter_sent and // and a 1st reminder
            $this->date_letter_sent->date_2nd_reminder_letter_sent and // and a 2nd reminder
            is_null($this->date_letter_sent->date_gp_letter_sent) // no gp letter
        ) {
            return self::LETTER_REMINDER_2;
        }
        if (
            $this->date_letter_sent->date_invitation_letter_sent and    // an invitation letter has been sent
            $this->date_letter_sent->date_1st_reminder_letter_sent and // and a 1st reminder
            $this->date_letter_sent->date_2nd_reminder_letter_sent and // and a 2nd reminder
            $this->date_letter_sent->date_gp_letter_sent // and a gp letter
        ) {
            return self::LETTER_GP;
        }

        return;
    }

    /**
     * @return int
     */
    public function getNextLetter()
    {
        if (is_null($this->getLastLetter())) {
            return self::LETTER_INVITE;
        } else {
            $lastletter = $this->getLastLetter();
            if ($lastletter == self::LETTER_INVITE) {
                return self::LETTER_REMINDER_1;
            } elseif ($lastletter == self::LETTER_REMINDER_1) {
                return self::LETTER_REMINDER_2;
            } elseif ($lastletter == self::LETTER_REMINDER_2) {
                return self::LETTER_GP;
            } elseif ($lastletter == self::LETTER_GP) {
                return self::LETTER_REMOVAL;
            }
        }
    }

    /**
     * get the code for the letter that is due on this operation, based on the current status
     * will return null if in an unknown state.
     *
     * @TODO: throw an exception for unknown state instead of returning null.
     *
     * @return int|null
     */
    public function getDueLetter()
    {
        $lastletter = $this->getLastLetter();
        if (!$this->getWaitingListStatus()) { // if getwaitingliststatus returns null, we're white
            return $lastletter; // no new letter is due, so we should print the last one
        }
        if ($this->getWaitingListStatus() == self::STATUS_PURPLE) {
            return self::LETTER_INVITE;
        } elseif ($this->getWaitingListStatus() == self::STATUS_GREEN1) {
            return self::LETTER_REMINDER_1;
        } elseif ($this->getWaitingListStatus() == self::STATUS_GREEN2) {
            return self::LETTER_REMINDER_2;
        } elseif ($this->getWaitingListStatus() == self::STATUS_ORANGE) {
            return self::LETTER_GP;
        } elseif ($this->getWaitingListStatus() == self::STATUS_RED) {
            // this used to return null, but now returning GP so that gp letters can be re-printed if necessary
            return self::LETTER_GP;
        } else {
            return; // possibly this should return $lastletter ?
        }
    }

    /**
     * Returns the letter status for an operation.
     *
     * Checks to see if it's an operation to be scheduled or an operation to be rescheduled. If it's the former it bases its calculation
     *   on the operation creation date. If it's the latter it bases it on the most recent cancelled_booking creation date.
     *
     * return int
     */
    public function getWaitingListStatus()
    {
        if (is_null($this->getLastLetter())) {
            return self::STATUS_PURPLE; // no invitation letter has been sent
        } elseif (
            is_null($this->date_letter_sent->date_invitation_letter_sent) and
            is_null($this->date_letter_sent->date_1st_reminder_letter_sent) and
            is_null($this->date_letter_sent->date_2nd_reminder_letter_sent) and
            is_null($this->date_letter_sent->date_gp_letter_sent)
        ) {
            return self::STATUS_PURPLE; // no invitation letter has been sent
        }

        $now = new DateTime();
        $now->setTime(0, 0, 0); // $two_weeks_ago = $now->modify('-14 days');
        $now = new DateTime();
        $now->setTime(0, 0, 0); // $one_week_ago = $now->modify('-7 days');

        // if the last letter was the invitation and it was sent over two weeks ago from now:
        $date_sent = new DateTime($this->date_letter_sent->date_invitation_letter_sent);
        $date_sent->setTime(0, 0, 0);
        if (($this->getLastLetter() == self::LETTER_INVITE) and ($now->getTimestamp() - $date_sent->getTimestamp() > 1209600)) {
            return self::STATUS_GREEN1;
        }

        // if the last letter was the 1st reminder and it was sent over two weeks ago from now:
        $date_sent = new DateTime($this->date_letter_sent->date_1st_reminder_letter_sent);
        $date_sent->setTime(0, 0, 0);
        if (($this->getLastLetter() == self::LETTER_REMINDER_1) and ($now->getTimestamp() - $date_sent->getTimestamp() > 1209600)) {
            return self::STATUS_GREEN2;
        }

        // if the last letter was the 2nd reminder and it was sent over two weeks ago from now:
        $date_sent = new DateTime($this->date_letter_sent->date_2nd_reminder_letter_sent);
        $date_sent->setTime(0, 0, 0);
        if (($this->getLastLetter() == self::LETTER_REMINDER_2) and ($now->getTimestamp() - $date_sent->getTimestamp() > 1209600)) {
            return self::STATUS_ORANGE;
        }
        // if the last letter was the gp letter and it was sent over one week ago from now:
        $date_sent = new DateTime($this->date_letter_sent->date_gp_letter_sent);
        $date_sent->setTime(0, 0, 0);
        if (($this->getLastLetter() == self::LETTER_GP) and ($now->getTimestamp() - $date_sent->getTimestamp() > 604800)) {
            return self::STATUS_RED;
        }

        return;
    }

    public function getWaitingListLetterStatus()
    {
        echo var_export($this->date_letter_sent, true);
        Yii::app()->end();
    }

    public function getMinDate()
    {
        $date = strtotime($this->event->created_date);

        $thisMonth = mktime(0, 0, 0, date('m'), 1, date('Y'));

        if ($date < $thisMonth) {
            return $thisMonth;
        }

        return $date;
    }

    public function getSchedule_timeframe()
    {
        return Element_OphTrOperationbooking_ScheduleOperation::model()->find('event_id=?', array($this->event_id));
    }

    /**
     * @param $firm
     * @param $timestamp
     * @param Element_OphTrOperationbooking_ScheduleOperation $schedule_options
     *
     * @return array
     */
    public function getFirmCalendarForMonth($firm, $timestamp, $schedule_options = null)
    {
        $sessions = array();

        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);

        $rtt_date = $this->getRTTBreach();

        $criteria = new CDbCriteria();
        if ($firm->id) {
            $criteria->compare('firm_id', $firm->id);
        } else {
            $criteria->addCondition('firm_id is null');
        }
        $criteria->compare('available', 1);
        $criteria->addSearchCondition('date', "$year-$month-%", false);
        $criteria->order = 'date asc';

        $days = array();
        $sessiondata = array();

        foreach (OphTrOperationbooking_Operation_Session::model()->findAll($criteria) as $session) {
            $day = date('D', strtotime($session->date));

            $sessiondata[$session->date][] = $session;
            $days[$day][] = $session->date;
        }

        $sessions = array();

        foreach ($days as $day => $dates) {
            for ($i = 1; $i <= date('t', mktime(0, 0, 0, $month, 1, $year)); ++$i) {
                if (date('D', mktime(0, 0, 0, $month, $i, $year)) == $day) {
                    $date = "$year-$month-".str_pad($i, 2, '0', STR_PAD_LEFT);
                    if (in_array($date, $dates) && (Yii::app()->user->checkAccess('Super schedule operation') || !Yii::app()->params['future_scheduling_limit'] || $date <= date('Y-m-d', strtotime('+'.Yii::app()->params['future_scheduling_limit'])))) {
                        $open = $full = 0;
                        $status = '';
                        if (strtotime($date) < strtotime(date('Y-m-d'))) {
                            $status = 'inthepast';
                        } else {
                            $hasFreeProcedures = false;
                            foreach ($sessiondata[$date] as $session) {
                                // Check if at least one session has enough allocated max_procedures to allow the booking
                                if (!$session->operationBookable($this)) {
                                    continue;
                                }

                                $hasFreeProcedures |= $session->isTherePlaceForComplexBooking($this);

                                if ($session->availableMinutes >= $this->total_duration) {
                                    ++$open;
                                } else {
                                    ++$full;
                                }
                            }

                            if (!$schedule_options->isPatientAvailable($date)) {
                                $status = 'patient-unavailable';
                            } elseif (!$hasFreeProcedures) {
                                $status = 'full';
                            } elseif ($full == count($sessiondata[$date])) {
                                $status = 'full';
                            } elseif ($full > 0 and $open > 0) {
                                $status = 'limited';
                            } elseif ($open == count($sessiondata[$date])) {
                                $status = 'available';
                            }
                        }

                        if ($rtt_date && $date >= $rtt_date) {
                            $status .= ' outside_rtt';
                        }
                    } else {
                        $status = 'closed';
                    }

                    $sessions[$day][$date] = array(
                        'status' => $status,
                    );
                }
            }
        }

        return $this->fixCalendarDateOrdering($sessions);
    }

    public function fixCalendarDateOrdering($sessions)
    {
        $return = array();

        foreach (array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun') as $day) {
            if (isset($sessions[$day])) {
                $return[$day] = $sessions[$day];
            }
        }

        $max = 0;

        $datelist = array();

        $dayn = 0;
        $day_lookup = array();
        $session_lookup = array();

        foreach ($return as $day => $dates) {
            foreach ($dates as $date => $session) {
                $datelist[$dayn][] = $date;
                $session_lookup[$date] = $session;
                if ($date > $max) {
                    $max = $date;
                }
            }
            $day_lookup[$dayn] = $day;
            ++$dayn;
        }

        while (1) {
            $changed = false;
            $datelist2 = array();
            foreach ($datelist as $day => $dates) {
                foreach ($dates as $i => $date) {
                    if ($date < $max && isset($datelist[$day + 1][$i]) && $date > $datelist[$day + 1][$i]) {
                        // fill in missing day
                        // appending ' 13:00:00' to the date to avoid daylight saving problems near midnight
                        // TODO: use DateTime object
                        if (!isset($datelist2[$day]) || !in_array(date('Y-m-d', strtotime($date .' 13:00:00') - (86400 * 7)), $datelist2[$day])) {
                            $datelist2[$day][] = date('Y-m-d', strtotime($date . ' 13:00:00') - (86400 * 7));
                            $session_lookup[date('Y-m-d', strtotime($date . ' 13:00:00') - (86400 * 7))] = array('status' => 'blank');
                            $changed = true;
                        }
                    }
                    if (!isset($datelist2[$day]) || !in_array($date, $datelist2[$day])) {
                        $datelist2[$day][] = $date;
                    }
                }
            }

            if (!$changed) {
                break;
            }
            $datelist = $datelist2;
        }

        $sessions = array();

        foreach ($datelist2 as $dayn => $dates) {
            foreach ($dates as $date) {
                $sessions[$day_lookup[$dayn]][$date] = $session_lookup[$date];
            }
        }

        return $sessions;
    }

    public function getTheatres($date, $firm_id = false)
    {
        if (empty($date)) {
            throw new Exception('Date is required.');
        }

        $criteria = new CDbCriteria();

        if (empty($firm_id) || $firm_id == 'EMG') {
            $criteria->addCondition('sessions.firm_id is null');
        } else {
            $criteria->addCondition('sessions.firm_id = :firmId');
            $criteria->params[':firmId'] = $firm_id;
        }

        $criteria->addCondition('sessions.date = :date');
        $criteria->params[':date'] = $date;

        $criteria->order = 'sessions.start_time';

        return OphTrOperationbooking_Operation_Theatre::model()
            ->with('sessions')
            ->active()
            ->findAll($criteria);
    }

    public function getWardOptions($session)
    {
        if (!$session || !$session->id) {
            throw new Exception('Session is required.');
        }
        $siteId = $session->theatre->site_id;
        $theatreId = $session->theatre_id;

        $results = array();

        if (!empty($theatreId)) {
            if ($session->theatre->ward) {
                $results[$session->theatre->ward_id] = $session->theatre->ward->name;
            }
        }

        if (empty($results)) {
            // otherwise select by site and patient age/gender
            $patient = $this->getPatient();
            $genderRestrict = $ageRestrict = 0;
            $genderRestrict = ('M' == $patient->gender) ? OphTrOperationbooking_Operation_Ward::RESTRICTION_MALE : OphTrOperationbooking_Operation_Ward::RESTRICTION_FEMALE;
            $ageRestrict = ($patient->isChild($session->date)) ? OphTrOperationbooking_Operation_Ward::RESTRICTION_CHILD : OphTrOperationbooking_Operation_Ward::RESTRICTION_ADULT;

            $criteria = new CDbCriteria();
            $criteria->addCondition('`t`.site_id = :siteId');
            $criteria->addCondition('`t`.restriction & :r1 >0');
            $criteria->addCondition('`t`.restriction & :r2 >0');
            $criteria->params[':siteId'] = $siteId;
            $criteria->params[':r1'] = $genderRestrict;
            $criteria->params[':r2'] = $ageRestrict;
            $criteria->order = 't.display_order asc';

            $results = CHtml::listData(OphTrOperationbooking_Operation_Ward::model()->active()->findAll($criteria), 'id', 'name');
        }

        return $results;
    }

    /**
     * @param $code
     * @return bool
     */
    public function hasAnaestheticTypeByCode($code)
    {
        return count(
            array_filter(
                $this->anaesthetic_type,
                static function ($a_type) use ($code) {
                    return $a_type->code === $code;
                }
            )
        ) > 0;
    }

    /**
     * @return string
     */
    public function getAnaestheticTypeDisplay()
    {
        return implode(', ', $this->anaesthetic_type);
    }

    /**
     * Calculate the EROD for this operation - the firm used to determine the service can be overridden by providing a firm.
     * (Note that this handles the emergency list by having a firm placeholder object that does not have an id - at this time,
     * no sessions are assigned to A&E firms, having the effect that no EROD can be calculated for emergency bookings).
     *
     * @param Firm $firm
     *
     * @return OphTrOperationbooking_Operation_EROD|null
     *
     * @throws Exception
     */
    public function calculateEROD($firm = null)
    {
        $criteria = new CDbCriteria();

        $criteria->params[':one'] = 1;
        //consultant required
        if ($this->consultant_required) {
            $criteria->addCondition('`t`.consultant = :one');
        }

        //anaesthetic requirements
        if ($this->anaesthetist_required || ($this->hasAnaestheticTypeByCode('GA'))) {
            $criteria->addCondition('`t`.anaesthetist = :one and `t`.general_anaesthetic = :one');
        }

        // child conditions
        $patient = $this->getPatient();
        if ($patient->isChild()) {
            // need to get the point at which patient becomes an adult. All sessions up to that point need the pediatric flag
            $criteria->params[':adult_date'] = $patient->getBecomesAdultDate();
            $criteria->addCondition('(`t`.date < :adult_date AND `t`.paediatric = :one) OR `t`.date >= :adult_date');
        }

        // if their are firms that are set for the subspecialty of the episode, use their sessions
        if ($rule = OphTrOperationbooking_Operation_EROD_Rule::model()->find('subspecialty_id=?', array($this->getFirm()->getSubspecialtyID()))) {
            $firm_ids = array();
            foreach ($rule->items as $item) {
                if ($item->item_type == 'firm') {
                    $firm_ids[] = $item->item_id;
                }
            }

            $criteria->addInCondition('firm.id', $firm_ids);
        } else {
            // otherwise, use the given firm to define the set of valid sessions by subspecialty
            if (!$firm) {
                $firm = $this->event->episode->firm;
            }

            if (!$firm->id) {
                // booking into the emergency list
                if (!$subspecialty = Subspecialty::model()->find('ref_spec=?', array('AE'))) {
                    throw new Exception('A&E subspecialty not found');
                }

                if (!$service_subspecialty_assignment = ServiceSubspecialtyAssignment::model()->find('subspecialty_id=?', array($subspecialty->id))) {
                    throw new Exception('A&E service_subspecialty_assignment not found');
                }
                $service_subspecialty_assignment_id = $service_subspecialty_assignment->id;
            } else {
                if (!$service_subspecialty_assignment_id = $firm->service_subspecialty_assignment_id) {
                    throw new Exception('Firm must have service_subspecialty_assignment for EROD calculation');
                }
            }
            $criteria->addCondition('service_subspecialty_assignment_id = :serviceSubspecialtyAssignmentId');
            $criteria->params[':serviceSubspecialtyAssignmentId'] = $service_subspecialty_assignment_id;
        }

        // session must be available
        $criteria->addCondition('`t`.available = :one');

        // work out the lead date
        $lead_decision_date = strtotime($this->decision_date);
        if ($lead_weeks = Yii::app()->params['erod_lead_time_weeks']) {
            $lead_decision_date += 86400 * 7 * $lead_weeks;
        }

        $lead_current_date = time();
        if ($lead_days = Yii::app()->params['erod_lead_current_date_days']) {
            $lead_current_date += (86400 * $lead_days);
        }
        $lead_time_date = ($lead_decision_date > $lead_current_date) ? date('Y-m-d', $lead_decision_date) : date('Y-m-d', $lead_current_date);

        $criteria->addCondition('`t`.date > :leadTimeDate');
        $criteria->params[':leadTimeDate'] = $lead_time_date;

        $criteria->order = '`t`.date, `t`.start_time';

        foreach (OphTrOperationbooking_Operation_Session::model()
                                 ->with(array(
                                'firm' => array(
                                        'joinType' => 'JOIN',
                                 ),
                                 ))
                         ->findAll($criteria) as $session) {
            $available_time = $session->availableMinutes;
            if ($available_time < $this->total_duration) {
                continue;
            }

            if ($session->isProcedureCountLimited() && $this->getProcedureCount() > $session->getAvailableProcedureCount()) {
                continue;
            }

            if ($session->isComplexBookingCountLimited() && $this->isComplex() && $session->getAvailableComplexBookingCount() <= 0) {
                continue;
            }

            $erod = new OphTrOperationbooking_Operation_EROD();
            $erod->session_id = $session->id;
            $erod->session_date = $session->date;
            $erod->session_start_time = $session->start_time;
            $erod->session_end_time = $session->end_time;
            $erod->firm_id = $session->firm_id;
            $erod->consultant = $session->consultant;
            $erod->paediatric = $session->paediatric;
            $erod->anaesthetist = $session->anaesthetist;
            $erod->general_anaesthetic = $session->general_anaesthetic;
            $erod->session_duration = $session->duration;
            $erod->total_operations_time = $session->bookedMinutes;
            // Note that I have not subtracted the duration of this operation from the available time when storing this
            // as it is now being generated before the booking is made. When the booking is made, it will have been saved
            // before the EROD is calculated, so the available time will reflect the same value as in the prior calculations
            $erod->available_time = $available_time;

            return $erod;
        }
    }

    public function audit($target, $action, $data = null, $log = false, $properties = array())
    {
        $properties['event_id'] = $this->event_id;
        $properties['episode_id'] = $this->event->episode_id;
        $properties['patient_id'] = $this->getPatient()->id;

        return parent::audit($target, $action, $data, $log, $properties);
    }

    public function cancel($reason_id, $comment = null, $cancellation_user_id = false, $reschedule_later = false)
    {
        if (!$reason = OphTrOperationbooking_Operation_Cancellation_Reason::model()->findByPk($reason_id)) {
            return array(
                'result' => false,
                'errors' => array(array('Please select a cancellation reason')),
            );
        }

        $this->operation_cancellation_date = date('Y-m-d H:i:s');
        $this->cancellation_reason_id = $reason_id;
        $this->cancellation_comment = $comment;
        $this->cancellation_user_id = $cancellation_user_id ? $cancellation_user_id : Yii::app()->session['user']->id;

        $this->status_id = OphTrOperationbooking_Operation_Status::model()->find('name=?', array('Cancelled'))->id;

        if (!$this->save()) {
            return array(
                'result' => false,
                'errors' => $this->getErrors(),
            );
        }

        OELog::log("Operation cancelled: $this->id");

        $this->audit('operation', 'cancel');

        $episode = $this->event->episode;
        $episode->episode_status_id = 5;

        if (!$episode->save()) {
            throw new Exception('Unable to change episode status for episode '.$episode->id);
        }

        if ($this->booking) {
            $this->booking->booking_cancellation_date = date('Y-m-d H:i:s');
            $this->booking->cancellation_reason_id = $reason_id;
            $this->booking->cancellation_comment = $comment;
            $this->booking->cancellation_user_id = $cancellation_user_id ? $cancellation_user_id : Yii::app()->session['user']->id;

            if (!$this->booking->save()) {
                return array(
                    'result' => false,
                    'errors' => $this->booking->getErrors(),
                );
            }
            OELog::log("Booking cancelled: {$this->booking->id}");

            $this->booking->audit('booking', 'cancel');

            if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
                if (strtotime($this->booking->session_date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
                    if (!is_array(Yii::app()->params['urgent_booking_notify_email'])) {
                        $targets = array(Yii::app()->params['urgent_booking_notify_email']);
                    } else {
                        $targets = Yii::app()->params['urgent_booking_notify_email'];
                    }
                    foreach ($targets as $email) {
                        mail(
                            $email,
                            '[OpenEyes] Urgent cancellation made',
                            "A cancellation was made with a TCI date within the next 24 hours.\n\nDisorder: "
                                .$this->getDisorderText()."\n\nPlease see: http://".@$_SERVER['SERVER_NAME']
                                .Yii::app()->createUrl('/OphTrOperationbooking/transport')."\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.",
                            'From: '.Yii::app()->params['urgent_booking_notify_email_from']."\r\n"
                        );
                    }
                }
            }
        }

        return array('result' => true);
    }

    public function isEditable()
    {
        return !in_array($this->status->name, array('Cancelled', 'Completed'));
    }

    /**
     * @param OphTrOperationbooking_Operation_Booking $booking
     * @param $operation_comments
     * @param $session_comments
     * @param $operation_comments_rtt
     * @param bool                                            $reschedule
     * @param null                                            $cancellation_data
     * @param Element_OphTrOperationbooking_ScheduleOperation $schedule_op
     *
     * @throws RaceConditionException
     * @throws Exception
     *
     * @internal param $booking_attributes
     *
     * @return array|bool
     */
    public function schedule(
        $booking,
        $operation_comments,
        $session_comments,
        $operation_comments_rtt,
        $_reschedule = false,
        $cancellation_data = null,
        $schedule_op = null
    ) {
        $this->reschedule = $_reschedule;
        if ($schedule_op == null) {
            throw new Exception('schedule_op argument required for scheduling');
        }

        if (Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] && !$this->referral) {
            return array(array('Referral required to schedule operation'));
        }

        $session = $booking->session;

        if (!$schedule_op->isPatientAvailable($session->date)) {
            return array(array('Cannot book patient into a session they are not available for.'));
        }

        if (!$session->operationBookable($this)) {
            return array(array('Attempted to book operation into incompatible session: '.$session->unbookableReason($this)));
        }

        $reschedule = in_array($this->status_id, array(2, 3, 4)); //@TODO: change hardcoded id to a query

        if (preg_match('/(^[0-9]{1,2}).*?([0-9]{2})$/', $booking->admission_time, $m)) {
            $booking->admission_time = $m[1].':'.$m[2];
        }

        // parse the cancellation data
        $cancellation_submitted = false;
        $cancellation_reason_id = null;
        $cancellation_comment = null;
        if ($cancellation_data) {
            $cancellation_submitted = $cancellation_data['submitted'];
            $cancellation_reason_id = $cancellation_data['reason_id'];
            $cancellation_comment = $cancellation_data['comment'];
        }


        if ($this->booking && !$reschedule) {
            // race condition, two users attempted to book the same operation at the same time
            throw new RaceConditionException('This operation has already been scheduled by '.($this->booking->user->fullName));
        }

        if ($reschedule && !$cancellation_submitted && $this->booking) {
            // race condition, two users attempted to book the same operation at the same time
            throw new RaceConditionException('This operation has already been scheduled by '.($this->booking->user->fullName));
        }


        if ($reschedule && $this->booking) {
            if (!$reason = OphTrOperationbooking_Operation_Cancellation_Reason::model()->findByPk($cancellation_reason_id)) {
                $this->addError('cancellation_reason_id', 'Please select a rescheduling reason');
                return $this->getErrors();
            }
            $this->booking->cancel($reason, $cancellation_comment, $reschedule);
            $this->booking = $booking;
        }

        foreach (array('date', 'start_time', 'end_time', 'theatre_id') as $field) {
            $booking->{'session_'.$field} = $booking->session->$field;
        }

        if (!$booking->save()) {
            return $booking->getErrors();
        }

        OELog::log('Booking '.($reschedule ? 'rescheduled' : 'made')." $booking->id");
        $booking->audit('booking', $reschedule ? 'reschedule' : 'create');

        $this->latest_booking_id = $booking->id;

        $this->comments = $operation_comments;
        $this->comments_rtt = $operation_comments_rtt;
        $this->site_id = $booking->session->theatre->site_id;

        if ($reschedule) {
            $this->setStatus('Rescheduled', false);
        } else {
            $this->setStatus('Scheduled', false);
            // Lock the RTT for this operation booking
            if ($ref = $this->referral) {
                if ($active = $ref->getActiveRTT()) {
                    if (count($active) == 1) {
                        $this->rtt_id = $active[0]->id;
                    }
                }
            }
        }

        if (!$this->save()) {
            throw new Exception('Unable to update operation data: '.print_r($this->getErrors(), true));
        }

        $session->comments = $session_comments;

        if (!$session->save()) {
            throw new Exception('Unable to save session comments: '.print_r($session->getErrors(), true));
        }

        // if the session has no firm, this implies it's an emergency booking so there is no need to calculate EROD
        if ($booking->session->firm && $erod = $this->calculateEROD($booking->session->firm)) {
            $erod->booking_id = $booking->id;
            if (!$erod->save()) {
                throw new Exception('Unable to save erod: '.print_r($erod->getErrors(), true));
            }
        }

        $this->event->episode->episode_status_id = 3;

        if (!$this->event->episode->save()) {
            throw new Exception('Unable to change episode status id for episode '.$this->event->episode->id);
        }

        $this->event->deleteIssues();

        if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
            if (strtotime($session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
                if (!is_array(Yii::app()->params['urgent_booking_notify_email'])) {
                    $targets = array(Yii::app()->params['urgent_booking_notify_email']);
                } else {
                    $targets = Yii::app()->params['urgent_booking_notify_email'];
                }

                if ($reschedule) {
                    $subject = '[OpenEyes] Urgent reschedule made';
                    $body = "A patient booking was rescheduled with a TCI date within the next 24 hours.\n\nDisorder: ".$this->getDisorderText()."\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.";
                } else {
                    $subject = '[OpenEyes] Urgent booking made';
                    $body = "A patient booking was made with a TCI date within the next 24 hours.\n\nDisorder: ".$this->getDisorderText()."\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.";
                }
                $from = Yii::app()->params['urgent_booking_notify_email_from'];

                foreach ($targets as $email) {
                    if (!Mailer::mail($email, $subject, $body, $from)) {
                        Yii::app()->user->setFlash('warning.email-failure', 'E-mail Failure. Failed to send one or more urgent booking notification E-mails.');
                    }
                }
            }
        }

        Yii::app()->event->dispatch("operation_booking_created", array('element' => $this));

        return true;
    }

    /**
     * Set the status based on the name passed in. If $save is false, we don't save and it is the responsibility
     * of the caller to ensure the instance is saved.
     *
     * @param $name
     * @param bool $save
     *
     * @throws Exception
     */
    public function setStatus($name, $save = true)
    {
        if (!$status = OphTrOperationbooking_Operation_Status::model()->find('name=?', array($name))) {
            throw new Exception('Invalid status: '.$name);
        }

        $this->status_id = $status->id;

        if ($save && !$this->save()) {
            throw new Exception('Unable to change operation status: '.print_r($this->getErrors(), true));
        }
    }

    public function getProceduresCommaSeparated($field = 'term')
    {
        $procedures = array();
        foreach ($this->procedures as $procedure) {
            $procedures[] = $procedure->$field;
        }

        return empty($procedures) ? 'No procedures' : implode(', ', $procedures);
    }

    public function getRefuseContact()
    {
        if (!$contact = $this->letterContact) {
            # FIXME: need to handle problems with letters more gracefully than throwing unhandled exceptions.
            return 'N/A';
            throw new Exception('Unable to find letter contact for operation '.$this->id);
        }

        if ($contact->refuse_title) {
            return $contact->refuse_title.' on '.$contact->refuse_telephone;
        }

        return 'the '.$this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->name.' Admission Coordinator on '.$contact->refuse_telephone;
    }

    public function getHealthContact()
    {
        return $this->letterContact->health_telephone;
    }

    public function getLetterContact()
    {
        $site_id = $this->booking->theatre->site_id;
        $subspecialty_id = $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id;
        $theatre_id = $this->booking->session->theatre_id;
        $firm_id = $this->booking->session->firm_id;
        $is_child = $this->getPatient()->isChild($this->booking->session->date);

        $criteria = new CDbCriteria();
        $criteria->addCondition('parent_rule_id is null');
        $criteria->order = 'rule_order asc';

        foreach (OphTrOperationbooking_Letter_Contact_Rule::model()->findAll($criteria) as $rule) {
            if ($rule->applies($site_id, $subspecialty_id, $theatre_id, $firm_id, $is_child)) {
                return $rule->parse($site_id, $subspecialty_id, $theatre_id, $firm_id, $is_child);
            }
        }

        return false;
    }

    public function getWaitingListContact()
    {
        $site_id = $this->site->id;
        $service_id = $this->event->episode->firm->serviceSubspecialtyAssignment->service_id;
        $firm_id = $this->event->episode->firm_id;
        $is_child = $this->getPatient()->isChild();

        $criteria = new CDbCriteria();
        $criteria->addCondition('parent_rule_id is null');
        $criteria->order = 'rule_order asc';

        foreach (OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll($criteria) as $rule) {
            if ($rule->applies($site_id, $service_id, $firm_id, $is_child)) {
                $rule = $rule->parse($site_id, $service_id, $firm_id, $is_child);

                return $rule->name.' on '.$rule->telephone;
            }
        }

        return false;
    }

    public function getDiagnosis()
    {
        return Element_OphTrOperationbooking_Diagnosis::model()->find('event_id=?', array($this->event_id));
    }

    public function getTextOperationName()
    {
        if ($rule = OphTrOperationbooking_Operation_Name_Rule::model()->find('theatre_id=?', array($this->booking->session->theatre_id))) {
            return $this->getPatient()->childPrefix.$rule->name;
        }

        if ($rule = OphTrOperationbooking_Operation_Name_Rule::model()->find('theatre_id is null')) {
            return $this->getPatient()->childPrefix.$rule->name;
        }

        return $this->getPatient()->childPrefix.'operation';
    }

    public function confirmLetterPrinted($confirmto = null, $confirmdate = null)
    {
        // admin users can set confirmto and confirm up to a specific point, steamrollering whatever else is in there
        if (!is_null($confirmto)) {
            if (!$dls = $this->date_letter_sent) {
                $dls = new OphTrOperationbooking_Operation_Date_Letter_Sent();
                $dls->element_id = $this->id;
            }
            if ($confirmto == self::LETTER_GP) {
                $dls->date_invitation_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_1st_reminder_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_2nd_reminder_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_gp_letter_sent = Helper::convertNHS2MySQL($confirmdate);
            }
            if ($confirmto == self::LETTER_INVITE) {
                $dls->date_invitation_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_1st_reminder_letter_sent = null;
                $dls->date_2nd_reminder_letter_sent = null;
                $dls->date_gp_letter_sent = null;
            }
            if ($confirmto == self::LETTER_REMINDER_1) {
                $dls->date_invitation_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_1st_reminder_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_2nd_reminder_letter_sent = null;
                $dls->date_gp_letter_sent = null;
            }
            if ($confirmto == self::LETTER_REMINDER_2) {
                $dls->date_invitation_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_1st_reminder_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_2nd_reminder_letter_sent = Helper::convertNHS2MySQL($confirmdate);
                $dls->date_gp_letter_sent = null;
            }
            if ($confirmto == 'noletters') {
                $dls->date_invitation_letter_sent = null;
                $dls->date_1st_reminder_letter_sent = null;
                $dls->date_2nd_reminder_letter_sent = null;
                $dls->date_gp_letter_sent = null;
            }
            if (!$dls->save()) {
                throw new Exception('Unable to save date letter sent: '.print_r($dls->getErrors(), true));
            }

            OELog::log("Letter print confirmed, datelettersent=$dls->id confirmdate='$confirmdate'");

        // Only confirm if letter is actually due
        } elseif ($this->getDueLetter() !== $this->getLastLetter()) {
            if ($dls = $this->date_letter_sent) {
                if ($dls->date_invitation_letter_sent == null) {
                    $dls->date_invitation_letter_sent = date('Y-m-d H:i:s');
                } elseif ($dls->date_1st_reminder_letter_sent == null) {
                    $dls->date_1st_reminder_letter_sent = date('Y-m-d H:i:s');
                } elseif ($dls->date_2nd_reminder_letter_sent == null) {
                    $dls->date_2nd_reminder_letter_sent = date('Y-m-d H:i:s');
                } elseif ($dls->date_gp_letter_sent == null) {
                    $dls->date_gp_letter_sent = date('Y-m-d H:i:s');
                } elseif ($dls->date_scheduling_letter_sent == null) {
                    $dls->date_scheduling_letter_sent = date('Y-m-d H:i:s');
                }
                if (!$dls->save()) {
                    throw new SystemException("Unable to update date_letter_sent record {$dls->id}: ".print_r($dls->getErrors(), true));
                }

                OELog::log("Letter print confirmed, datelettersent=$dls->id");
            } else {
                $dls = new OphTrOperationbooking_Operation_Date_Letter_Sent();
                $dls->element_id = $this->id;
                $dls->date_invitation_letter_sent = date('Y-m-d H:i:s');
                if (!$dls->save()) {
                    throw new SystemException('Unable to save new date_letter_sent record: '.print_r($dls->getErrors(), true));
                }

                OELog::log("Letter print confirmed, datelettersent=$dls->id");
            }
        }
    }

    public function getDisorderText()
    {
        if (!$diagnosis = Element_OphTrOperationbooking_Diagnosis::model()->find('event_id=?', array($this->event_id))) {
            throw new Exception("Unable to find diagnosis element for event_id $this->event_id");
        }

        return $diagnosis->disorder->term;
    }

    public function sentInvitation()
    {
        if (is_null($last_letter = $this->lastLetter)) {
            return false;
        }

        return in_array($last_letter, array(
            self::LETTER_INVITE,
            self::LETTER_REMINDER_1,
            self::LETTER_REMINDER_2,
            self::LETTER_GP,
        ));
    }

    public function sent1stReminder()
    {
        if (is_null($last_letter = $this->lastLetter)) {
            return false;
        }

        return in_array($last_letter, array(
            self::LETTER_REMINDER_1,
            self::LETTER_REMINDER_2,
            self::LETTER_GP,
        ));
    }

    public function sent2ndReminder()
    {
        if (is_null($last_letter = $this->lastLetter)) {
            return false;
        }

        return in_array($last_letter, array(
            self::LETTER_REMINDER_2,
            self::LETTER_GP,
        ));
    }

    public function sentGPLetter()
    {
        if (is_null($last_letter = $this->lastLetter)) {
            return false;
        }

        return in_array($last_letter, array(
            self::LETTER_GP,
        ));
    }

    public function delete()
    {
        // Delete related records
        OphTrOperationbooking_Operation_Date_Letter_Sent::model()->deleteAll('element_id = ?', array($this->id));
        OphTrOperationbooking_Operation_Procedures::model()->deleteAll('element_id = ?', array($this->id));
        OphTrOperationbooking_Operation_Booking::model()->deleteAll('element_id = ?', array($this->id));
        OphTrOperationbooking_Operation_EROD::model()->deleteAll('element_id = ?', array($this->id));
        parent::delete();
    }

    public function getTransportColour()
    {
        $booking = $this->latestBooking;

        if (!$booking->transport_arranged) {
            if (strtotime($booking->session_date) <= (time() + 3600)) {
                return 'Red';
            }

            return 'Green';
        }

        return 'Grey';
    }

    public function getTransportStatus()
    {
        if ($this->latestBooking->booking_cancellation_date) {
            return 'Cancelled';
        }

        if ($this->status_id == 6) {
            return 'Completed';
        }

        if ($this->status_id == 4) {
            return 'Rescheduled';
        }

        return 'Booked';
    }

    public function getContainer_view_view()
    {
        return false;
    }

    /**
     * Calculates the total number of procedures this operation requires.
     *
     * @return int
     */
    public function getProcedureCount()
    {
        $total = count($this->procedures);

        if ($this->eye_id == Eye::BOTH) {
            $total *= 2;
        }

        return $total;
    }

    /**
     * Whether the operation is complex
     *
     * @return bool
     */
    public function isComplex()
    {
        return $this->complexity == self::COMPLEXITY_HIGH;
    }

    /**
     * Whether the referral for the operation is still changeable - simple wrapper at the moment.
     *
     * @return bool
     */
    public function canChangeReferral()
    {
        return !$this->_has_bookings;
    }

    /**
     * Ensure that referral assigned to the element is for the correct patient.
     *
     * @param $attribute
     * @param $params
     *
     * @throws Exception
     */
    public function validateReferral($attribute, $params)
    {
        if (!$this->canChangeReferral()
                && $this->$attribute != $this->_original_referral_id) {
            $this->addError($attribute, 'Referral cannot be changed after an operation has been scheduled');
        } elseif ($referral_id = $this->$attribute) {
            if (!$referral = Referral::model()->findByPk($referral_id)) {
                throw new Exception('Invalid referral id set on '.get_class($this));
            }
            if ($referral->patient_id != $this->getPatient()->id) {
                $this->addError($attribute, 'Referral must be for the patient of the event');
            }
        }
    }

    /**
     * Validate a duration only if a procedure exists so we don't have messages for non existent elements.
     *
     * @param $attribute
     *
     * @return bool
     */
    public function validateDuration($attribute)
    {
        if (!$this->getProcedureCount()) {
            return true;
        }

        if (!$this->total_duration) {
            $this->addError($attribute, 'Total Duration cannot be blank');
        }
    }

    /**
     * returns the RTT for the operation booking if there is one available.
     *
     * @return RTT|null
     */
    public function getRTT()
    {
        if ($this->fixed_rtt) {
            return $this->fixed_rtt;
        } elseif ($ref = $this->referral) {
            $rtts = $ref->getActiveRTT();
            if (count($rtts) == 1) {
                return $rtts[0];
            }
        }

        return;
    }

    /**
     * Get the RTT breach for this operation
     * (if either an RTT is set, or if there is a psuedo-breach based on a week count from DTA).
     *
     * @return string 'Y-m-d'|null
     */
    public function getRTTBreach()
    {
        if ($rtt = $this->getRTT()) {
            return $rtt->breach;
        } elseif ($rtt_weeks = Yii::app()->params['ophtroperationboooking_rtt_limit']) {
            return date('Y-m-d', strtotime('+'.$rtt_weeks.' weeks', strtotime($this->decision_date)));
        }
    }

    /**
     * Returns complexity name as string
     *
     * @return string
     */

    public function getComplexityCaption()
    {
        return array_key_exists($this->complexity, self::$complexity_captions) ? self::$complexity_captions[$this->complexity] : 'N/A';
    }

    public function getComplexityColor()
    {
        return array_key_exists($this->complexity, self::$complexity_colors) ? self::$complexity_colors[$this->complexity] : '';
    }

    public function getAnaesthetist_required()
    {
        $anaesthetist_required = false;
        foreach ($this->anaesthetic_type as $anaesthetic_type) {
            if ( in_array($anaesthetic_type->id, $this->anaesthetist_required_ids) ) {
                $anaesthetist_required = true;
            }
        }
        return $anaesthetist_required;
    }

    public function getAllBookingRisks()
    {
        $risks = array();
        foreach ($this->procedures as $procedure) {
            $risks = array_merge(
                $risks,
                $procedure->risks
            );
        }
        return $risks;
    }

    public function getAllBookingRiskIds()
    {
        $risks = array();
        foreach ($this->procedures as $procedure) {
            $risks = array_merge(
                $risks,
                array_map(static function ($risk) {
                    return $risk->id;
                }, $procedure->risks)
            );
        }
        return $risks;
    }

    public function getAllProcedureOpnotes()
    {
        $opnotes = array();
        foreach ($this->procedures as $procedure) {
            if (isset($procedure->operationNotes)) {
                $opnotes = array_merge(
                    $opnotes,
                    $procedure->operationNotes
                );
            }
        }
        return $opnotes;
    }

    /**
     * @inheritDoc
     */
    public function afterSave()
    {
        parent::afterSave();
    }
}
