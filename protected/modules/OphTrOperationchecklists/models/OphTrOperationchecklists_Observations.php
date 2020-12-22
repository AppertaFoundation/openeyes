<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "ophtroperationchecklists_observations".
 *
 * The followings are the available columns in table 'ophtroperationchecklists_observations':
 * @property integer $id
 * @property string $checklist_result_id
 * @property string $blood_pressure_systolic
 * @property string $blood_pressure_diastolic
 * @property string $pulse
 * @property string $temperature
 * @property string $respiration
 * @property string $o2_sat
 * @property string $ews
 * @property string $blood_glucose
 * @property string $hba1c
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class OphTrOperationchecklists_Observations extends \BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationchecklists_observations';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('blood_pressure_systolic, blood_pressure_diastolic, pulse, temperature, respiration, o2_sat, ews, hba1c', 'default', 'setOnEmpty' => true, 'value' => null),
            array('checklist_result_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('blood_pressure_systolic, blood_pressure_diastolic, pulse,  respiration, o2_sat, ews', 'length', 'max'=>3),
            array('temperature', 'type', 'type' => 'float'),
            array('temperature', 'length', 'max' => 5),
            array('blood_glucose, hba1c', 'length', 'max'=>4),
            array('last_modified_date, checklist_result_id, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, checklist_result_id, blood_pressure_systolic, blood_pressure_diastolic, pulse, temperature, respiration, o2_sat, ews, blood_glucose, hba1c, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'checklistResults' => array(self::BELONGS_TO, 'OphTrOperationchecklists_AdmissionResults', 'checklist_result_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'blood_pressure' => 'Blood Pressure (mmHg/mmHg)',
            'blood_pressure_systolic' => 'Blood Pressure Systolic',
            'blood_pressure_diastolic' => 'Blood Pressure Diastolic',
            'pulse' => 'Pulse (bpm)',
            'temperature' => 'Temperature' ,
            'respiration' => 'Respirations (per min)',
            'o2_sat' => 'O2 Sat (air)',
            'ews' => 'EWS',
            'blood_glucose' => 'Blood Glucose (mmol/l)',
            'hba1c' => 'HbA1c (mmol/mol)',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('checklist_result_id', $this->checklist_result_id, true);
        $criteria->compare('blood_pressure_systolic', $this->blood_pressure_systolic, true);
        $criteria->compare('blood_pressure_diastolic', $this->blood_pressure_diastolic, true);
        $criteria->compare('pulse', $this->pulse, true);
        $criteria->compare('temperature', $this->temperature, true);
        $criteria->compare('respiration', $this->respiration, true);
        $criteria->compare('o2_sat', $this->o2_sat, true);
        $criteria->compare('ews', $this->ews, true);
        $criteria->compare('blood_glucose', $this->blood_glucose, true);
        $criteria->compare('hba1c', $this->hba1c, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphTrOperationchecklists_Observations the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
