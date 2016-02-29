<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use Yii;

/**
 * This is the model class for table "et_ophciexamination_clinicoutcome".
 *
 * The followings are the available columns in table:
 * @property integer $id
 * @property Event $event
 * @property OphCiExamination_ClinicOutcome_Status $status
 * @property integer $followup_quantity
 * @property Period $followup_period
 * @property OphCiExamination_ClinicOutcome_Role $role
 * @property string $role_comments
 */

class Element_OphCiExamination_ClinicOutcome extends \BaseEventTypeElement
{
    const FOLLOWUP_Q_MIN = 1;
    const FOLLOWUP_Q_MAX = 12;

    /**
     * Returns the static model of the specified AR class.
     * @return Element_OphCiExamination_ClinicOutcome the static model class
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
        return 'et_ophciexamination_clinicoutcome';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('followup_quantity, followup_period_id, role_id, role_comments, community_patient', 'safe'),
                array('status_id', 'required'),
                array('status_id', 'statusDependencyValidation'),
                array('role_id', 'roleDependencyValidation'),
                array('followup_quantity', 'numerical', 'integerOnly' => true, 'min' => self::FOLLOWUP_Q_MIN, 'max' => self::FOLLOWUP_Q_MAX),
                array('id, event_id, status_id, followup_quantity, followup_period_id, role_id, role_comments', 'safe', 'on' => 'search'),
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
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'status' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status', 'status_id'),
                'followup_period' => array(self::BELONGS_TO, 'Period', 'followup_period_id'),
                'role' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Role', 'role_id'),
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
                'status_id' => "Status",
                'followup_quantity' => 'Follow-up',
                'followup_period_id' => 'Follow-up period',
                'role_id' => 'Role',
                'role_comments' => 'Role comment',
                'community_patient' => 'Patient suitable for community patient tariff',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('status_id', $this->status_id);
        $criteria->compare('followup_quanityt', $this->followup_quantity);
        $criteria->compare('followup_quantity', $this->followup_quantity);
        $criteria->compare('followup_period_id', $this->followup_period_id);
        $criteria->compare('role_id', $this->role_id);
        $criteria->compare('role_comments', $this->role_comments);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Follow up data is only required for status that are flagged for follow up
     *
     * @property string $attribute
     */
    public function statusDependencyValidation($attribute)
    {
        if ($this->status_id && $this->status->followup) {
            $v = \CValidator::createValidator('required', $this, array('followup_quantity', 'followup_period_id', 'role_id'));
            $v->validate($this);
        }
    }

    /**
     * Role comments are only required if role flags it
     * @property string $attribute
     */
    public function roleDependencyValidation($attribute)
    {
        if ($this->role && $this->role->requires_comment
                && !trim($this->role_comments)) {
            $this->addError($attribute, 'Role requires a comment');
        }
    }

    public function getFollowUpQuantityOptions()
    {
        $opts = array();
        for ($i = self::FOLLOWUP_Q_MIN; $i <= self::FOLLOWUP_Q_MAX; $i++) {
            $opts[(string) $i] = $i;
        }
        return $opts;
    }

    public function getFollowUp()
    {
        if ($this->status->followup) {
            return $this->followup_quantity . ' ' . $this->followup_period;
        }
    }

    /**
     * Returns the follow up period information
     *
     * @return string
     */
    public function getLetter_fup()
    {
        $text = array();

        $text[] = $this->getFollowUp();
        $text[] = $this->role->name;
        if ($this->role_comments) {
            $text[] = '(' . $this->role_comments . ')';
        }
        return implode(' ', $text);
    }

    public function afterSave()
    {
        // Update Episode status when outcome is saved
        if ($this->status && $this->status->episode_status) {
            if ($this->event->isLatestOfTypeInEpisode()) {
                $this->event->episode->episode_status_id = $this->status->episode_status_id;
                if (!$this->event->episode->save()) {
                    throw new Exception('Unable to save episode status: '.print_r($this->event->episode->getErrors(), true));
                }
            }
        }
        parent::afterSave();
    }

    /**
     * If the PatientTicketing module is installed, will use API to get Patient Ticket for this element event (if one exists)
     *
     * @return mixed
     */
    public function getPatientTicket()
    {
        if ($this->event && $this->event->id && $api = Yii::app()->moduleAPI->get('PatientTicketing')) {
            return $api->getTicketForEvent($this->event);
        }
    }

    /**
     * Will determine the queue options for the given firm
     *
     * @param $firm
     * @return array
     */
    public function getPatientTicketQueues($firm, $patient)
    {
        if ($api = Yii::app()->moduleAPI->get('PatientTicketing')) {
            return $api->getQueueSetList($firm, $patient);
        }
        return array();
    }
}
