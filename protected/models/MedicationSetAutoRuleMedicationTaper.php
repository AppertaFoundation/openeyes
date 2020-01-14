<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "medication_set_auto_rule_medication_taper".
 *
 * The followings are the available columns in table 'medication_set_auto_rule_medication_taper':
 * @property integer $id
 * @property integer $medication_set_id
 * @property double $dose
 * @property integer $frequency_id
 * @property string $duration_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property MedicationDuration $duration
 * @property MedicationFrequency $frequency
 * @property MedicationSetItem $medicationSetItem
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class MedicationSetAutoRuleMedicationTaper extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_set_auto_rule_medication_taper';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('medication_set_auto_rule_id, frequency_id, duration_id', 'required'),
            array('frequency_id', 'numerical', 'integerOnly'=>true),
            array('dose', 'numerical'),
            array('duration_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, medication_set_item_id, dose, frequency_id, duration_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'duration' => array(self::BELONGS_TO, MedicationDuration::class, 'duration_id'),
            'frequency' => array(self::BELONGS_TO, MedicationFrequency::class, 'frequency_id'),
            'medicationSetAutoRule' => array(self::BELONGS_TO, MedicationSetAutoRuleMedication::class, 'medication_set_auto_rule_id'),
            'createdUser' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'medication_set_auto_rule_id' => 'Medication Set Auto Rule Medication',
            'dose' => 'Dose',
            'frequency_id' => 'Frequency',
            'duration_id' => 'Duration',
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
        $criteria->compare('medication_set_auto_rule_id', $this->medication_set_auto_rule_id);
        $criteria->compare('dose', $this->dose);
        $criteria->compare('frequency_id', $this->frequency_id);
        $criteria->compare('duration_id', $this->duration_id, true);
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
     * @return MedicationSetItemTaper the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
