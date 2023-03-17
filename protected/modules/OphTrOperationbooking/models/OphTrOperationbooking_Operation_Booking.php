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
 * This is the model class for table "ophtroperationbooking_operation_booking".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $element_id
 * @property int $session_id
 * @property int $display_order
 * @property int $ward_id
 * @property time $admission_time
 * @property int confirmed
 *
 * The followings are the available model relations:
 * @property Session $session
 * @property Element_OphTrOperationbooking_Operation $operation
 * @property User $user
 * @property User $usermodified
 * @property OphTrOperationbooking_Operation_Ward $ward
 */
class OphTrOperationbooking_Operation_Booking extends BaseActiveRecordVersioned
{
    use HasFactory;

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
        return 'ophtroperationbooking_operation_booking';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            //allow session to be set directly here for ease of testing.
            array('element_id, session_id, session, display_order, ward_id, admission_time, confirmed, session_date, session_start_time, session_end_time, session_theatre_id, transport_arranged, transport_arranged_date, booking_cancellation_date, cancellation_reason_id, cancellation_comment, cancellation_user_id', 'safe'),
            array('element_id', 'required'),
            array('display_order', 'numerical', 'integerOnly' => true),
            array('ward_id', 'numerical', 'integerOnly' => true),
            array('element_id, session_id', 'length', 'max' => 10),
            array('admission_time', 'match', 'pattern' => '/^[0-9]{1,2}.*?[0-9]{2}$/'),
            array('admission_time', 'lessThanSessionEndTimeValidate', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_id, session_id, display_order, ward_id, admission_time, confirmed', 'safe', 'on' => 'search'),
            array('ward_id,admission_time,session_id','notNullValidator')
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'usercancelled' => array(self::BELONGS_TO, 'User', 'cancellation_user_id'),
            'ward' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Ward', 'ward_id'),
            'session' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Session', 'session_id'),
            'operation' => array(self::BELONGS_TO, 'Element_OphTrOperationbooking_Operation', 'element_id'),
            'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'session_theatre_id'),
            'cancellationReason' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Cancellation_Reason', 'cancellation_reason_id'),
            'schedulingOption' => array(self::BELONGS_TO, 'Element_OphTrOperationbooking_ScheduleOperation', 'element_id'),
            'erod' => array(self::HAS_ONE, 'OphTrOperationbooking_Operation_EROD', 'booking_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'element_id' => 'Element Operation',
            'session_id' => 'Session',
            'display_order' => 'Display Order',
            'ward_id' => 'Ward',
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

    public function getCancellationReasonWithComment()
    {
        $return = $this->cancellationReason->text;
        if ($this->cancellation_comment) {
            $return .= " ($this->cancellation_comment)";
        }

        return $return;
    }

    public function audit($target, $action, $data = null, $log = false, $properties = array())
    {
        $properties['event_id'] = $this->operation->event_id;
        $properties['episode_id'] = $this->operation->event->episode_id;
        $properties['patient_id'] = $this->operation->event->episode->patient_id;

        return parent::audit($target, $action, $data, $log, $properties);
    }

    public function cancel($reason, $cancellation_comment, $reschedule = false)
    {
        $this->booking_cancellation_date = date('Y-m-d H:i:s');
        $this->cancellation_reason_id = $reason->id;
        $this->cancellation_comment = $cancellation_comment;
        $this->cancellation_user_id = Yii::app()->session['user']->id;

        if (!$this->save()) {
            throw new Exception('Unable to save booking: '.print_r($this->getErrors(), true));
        }

        OELog::log("Booking cancelled: $this->id");

        if (!$reschedule) {
            if (!$this->operation->event->addIssue('Operation requires scheduling')) {
                throw new Exception('Unable to add event issue: '.print_r($this->operation->event->getErrors(), true));
            }

            if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
                if (strtotime($this->session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
                    if (!is_array(Yii::app()->params['urgent_booking_notify_email'])) {
                        $targets = array(Yii::app()->params['urgent_booking_notify_email']);
                    } else {
                        $targets = Yii::app()->params['urgent_booking_notify_email'];
                    }
                    foreach ($targets as $email) {
                        if (!Mailer::mail($email, '[OpenEyes] Urgent cancellation made', "A cancellation was made with a TCI date within the next 24 hours.\n\nDisorder: ".$this->operation->getDisorderText()."\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.", 'From: '.Yii::app()->params['urgent_booking_notify_email_from']."\r\n"));
                        Yii::app()->user->setFlash('warning.email-failure', 'E-mail Failure. Failed to send one or more urgent booking notification E-mails.');
                    }
                }
            }

            $this->audit('booking', 'cancel');

            $this->operation->event->episode->episode_status_id = 3;
            if (!$this->operation->event->episode->save()) {
                throw new Exception('Unable to update episode status for episode: '.print_r($operation->event->episode->getErrors(), true));
            }

            // we've just cancelled a booking and updated the element_operation status to 'needs rescheduling'
            // any time we do that we need to add a new record to date_letter_sent
            $date_letter_sent = new OphTrOperationbooking_Operation_Date_Letter_Sent();
            $date_letter_sent->element_id = $this->element_id;
            if (!$date_letter_sent->save()) {
                throw new Exception('Unable to save date_letter_sent: '.print_r($date_letter_sent->getErrors(), true));
            }
        }
    }

    public function showWarning($type)
    {
        $subspecialty_id = ($this->session->firm) ? $this->session->firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        if ($rule = OphTrOperationbooking_Admission_Letter_Warning_Rule::getRule(
            $type,
            $this->session->theatre->site_id,
            $this->operation->event->episode->patient->isChild($this->session->date),
            $this->session->theatre_id,
            $subspecialty_id,
            $this->session->firm_id
        )
            ) {
            return $rule->show_warning;
        }

        return false;
    }

    public function getWarningHTML($type)
    {
        $return = '';

        $subspecialty_id = ($this->session->firm) ? $this->session->firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        if ($rule = OphTrOperationbooking_Admission_Letter_Warning_Rule::getRule(
            $type,
            $this->session->theatre->site_id,
            $this->operation->event->episode->patient->isChild($this->session->date),
            $this->session->theatre_id,
            $subspecialty_id,
            $this->session->firm_id
        )) {
            if ($rule->strong) {
                $return .= '<strong>';
            }
            if ($rule->emphasis) {
                $return .= '<em>';
            }
            $return .= $rule->warning_text;
            if ($rule->emphasis) {
                $return .= '</em>';
            }
            if ($rule->strong) {
                $return .= '</strong>';
            }
        }

        return $return;
    }

    /**
     * calculate the appropriate default displayorder for this record.
     *
     * @return int
     */
    protected function calculateDefaultDisplayOrder()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('session_id', $this->session->id);
        $criteria->order = 'display_order desc';
        $criteria->limit = 1;

        return ($booking2 = self::model()->find($criteria)) ? $booking2->display_order + 1 : 1;
    }

    /**
     * Ensure display_order is set.
     *
     * @return bool
     */
    protected function beforeValidate()
    {
        if ($this->session && $this->isNewRecord) {
            $this->display_order = $this->calculateDefaultDisplayOrder();
        }

        return parent::beforeValidate();
    }

    protected function afterValidate()
    {
        if (preg_match('/^([0-9]{1,2}).*?([0-9]{2})$/', $this->admission_time, $m)) {
            if ((integer) $m[1] > 23 || (integer) $m[2] > 59) {
                $this->addError('admission_time', 'Please enter a valid admission time in the 24-hour clock format');
            }
        }

        return parent::afterValidate();
    }

    protected function afterSave()
    {
        Yii::app()->event->dispatch(
            'OphTrOperationbooking_booking_after_save',
            array(
                'patient' => $this->operation->event->episode->patient,
                'admission_date' => $this->session->date,
                'admission_time' => $this->admission_time,
                'firm' => $this->session->firm,
                'site' => $this->ward->site,
                'ward_code' => $this->ward->code,
                'theatre_code' => $this->session->theatre ? $this->session->theatre->code : null,
                'cancellation_date' => $this->booking_cancellation_date,
                'new' => $this->isNewRecord,
            )
        );

        parent::afterSave();
    }

    /**
     * Pass through convenience function.
     *
     * @return int
     */
    public function getProcedureCount()
    {
        return $this->operation->getProcedureCount();
    }

    /**
     * Pass through convenience function.
     *
     * @return bool
     */
    public function isComplex()
    {
        return $this->operation->isComplex();
    }

    /**
     * check if admission time is less than session_end_time.
     */
    public function lessThanSessionEndTimeValidate()
    {
        if (!isset($this->session->end_time)) {
            $this->addError('admission_time', 'Session End Time required to check Admission Time');
        } elseif (strtotime($this->session->end_time) <= strtotime($this->admission_time)) {
            $this->addError('admission_time', 'Admission time cannot be later or equal than Session End Time');
        }
    }
    public function notNullValidator($attribute, $param){
        if (!isset($this->$attribute) || empty($this->$attribute)) {
            $this->addError($attribute, $this->getAttribute($attribute).' can not be blank.');
        }
    }
}
