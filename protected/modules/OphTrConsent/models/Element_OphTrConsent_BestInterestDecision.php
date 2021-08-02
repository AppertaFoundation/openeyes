<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
/**
 * This is the model class for table "et_ophtrconsent_best_interest_decision".
 *
 * The followings are the available columns in table 'et_ophtrconsent_best_interest_decision':
 * @property integer $id
 * @property string $event_id
 * @property integer $patient_has_not_refused
 * @property string $reason_for_procedure
 * @property integer $treatment_cannot_wait
 * @property string $treatment_cannot_wait_reason
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 */

namespace OEModule\OphTrConsent\models;

class Element_OphTrConsent_BestInterestDecision extends \BaseEventTypeElement
{
    public function getElementTypeName()
    {
        return "Assessment of patient's best interests";
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_best_interest_decision';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('patient_has_not_refused', 'validatePatientHasNotRefused'),
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('patient_has_not_refused, reason_for_procedure, treatment_cannot_wait, treatment_cannot_wait_reason, wishes, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, patient_has_not_refused, reason_for_procedure, treatment_cannot_wait, treatment_cannot_wait_reason, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
        );
    }

    public function validatePatientHasNotRefused()
    {
        if ($this->patient_has_not_refused && $this->reason_for_procedure == '') {
            $this->addError("patient_has_not_refused", "You must give a reason for procedure");
        }
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
            'createdUser' => array(self::BELONGS_TO, \User::class, 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, \User::class, 'last_modified_user_id'),
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
            'patient_has_not_refused' => 'Patient has not refused this procedure in a valid advance directive',
            'reason_for_procedure' => 'To the best of my knowledge, the patient has not refused this procedure in a valid advance directive.\nWhere possible and appropriate, I have encouraged the patient to participate in the decision and I have consulted with those close to the patient and with colleagues and those close to the patient.\nIn the case of a patient who does not have anyone close enough to help in the decision-making process and for whom serious medical treatment is proposed, I have consulted an Independent Medical Capacity Advocate and I believe the procedure to be in the patient’s best interests because:\n',
            'treatment_cannot_wait' => 'Treatment cannot wait until the patient recovers capacity',
            'treatment_cannot_wait_reason' => 'Where incapacity is likely to be temporary',
            'wishes' => 'The persons past and present wishes, feelings values and beliefs relating to the decision (if they can ascertained)',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'patient_has_not_refused_view' => 'To the best of my knowledge, the patient has not refused this procedure in a valid advance directive.\nWhere possible and appropriate, I have encouraged the patient to participate in the decision and I have consulted with those close to the patient and with colleagues and those close to the patient.\nIn the case of a patient who does not have anyone close enough to help in the decision-making process and for whom serious medical treatment is proposed, I have consulted an Independent Medical Capacity Advocate and I believe the procedure to be in the patient’s best interests because:\n',
            'treatment_cannot_wait_reason_view' => 'Where incapacity is likely to be temporary, treatment cannot wait until the patient recovers capacity because'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('patient_has_not_refused', $this->patient_has_not_refused);
        $criteria->compare('reason_for_procedure', $this->reason_for_procedure, true);
        $criteria->compare('treatment_cannot_wait', $this->treatment_cannot_wait);
        $criteria->compare('treatment_cannot_wait_reason', $this->treatment_cannot_wait_reason, true);
        $criteria->compare('wishes', $this->wishes, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphTrConsent_BestInterestDecision the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
