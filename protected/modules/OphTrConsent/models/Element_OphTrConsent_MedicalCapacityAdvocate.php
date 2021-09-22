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
 * This is the model class for table "et_ophtrconsent_medical_capacity_advocate".
 *
 * The followings are the available columns in table 'et_ophtrconsent_medical_capacity_advocate':
 * @property integer $id
 * @property string $event_id
 * @property integer $instructed_id
 * @property string $outcome_decision
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 * @property MedicalCapacityAdvocateInstructed $medicalCapacityAdvocateInstructed
 *
 */
namespace OEModule\OphTrConsent\models;

class Element_OphTrConsent_MedicalCapacityAdvocate extends \BaseEventTypeElement
{
    public function getElementTypeName()
    {
        return "Independent Medical Capacity Advocate";
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_medical_capacity_advocate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('outcome_decision', 'validateOutcomeDecision'),
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('instructed_id, outcome_decision, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, instructed_id, outcome_decision, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
        );
    }

    public function validateOutcomeDecision()
    {
        if ((strcmp($this->instructed->name, "No") == 0 || strcmp($this->instructed->name, "Yes") == 0) && $this->outcome_decision == '') {
            $this->addError("outcome_decision", "If the answer to the IMCA been instructed question was Yes or No, then the ".lcfirst($this->getAttributeLabel("outcome_decision"))." field is mandatory");
        } elseif (strcmp($this->instructed->name, "N/A") == 0 && $this->outcome_decision != '') {
            $this->addError("outcome_decision", "If the answer to the IMCA been instructed question question was “N/A”, then the ".lcfirst($this->getAttributeLabel("outcome_decision"))." field must be blank");
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
            'instructed' => array(self::BELONGS_TO, 'OphTrConsent_Medical_Capacity_Advocate_Instructed', 'instructed_id'),
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
            'instructed_id' => 'Independent Medical Capacity Advocate (IMCA) for decisions about serious medical treatment. If there is no-one appropriate to consult other than paid staff has an IMCA been instructed?',
            'outcome_decision' => 'Outcome decision or any other comments',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
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
        $criteria->compare('instructed_id', $this->patient_has_not_refused);
        $criteria->compare('outcome_decision', $this->reason_for_procedure, true);
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
     * @return Element_OphTrConsent_MedicalCapacityAdvocate the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
