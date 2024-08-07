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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_systemic_surgery_set_entry".
 *
 * The followings are the available columns in table 'ophciexamination_systemic_surgery_set_entry':
 * @property integer $id
 * @property integer $set_id
 * @property string $operation
 * @property string $gender
 * @property string $age_min
 * @property string $age_max
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property SystemicSurgerySet $systemicSurgerySet
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 */
class SystemicSurgerySetEntry extends \BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_systemic_surgery_set_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['set_id', 'numerical', 'integerOnly'=>true],
            ['gender', 'length', 'max'=>1],
            ['operation', 'length', 'max'=>1024],
            ['age_min, age_max', 'length', 'max'=>3],
            ['last_modified_user_id, created_user_id', 'length', 'max'=>10],
            ['last_modified_date, created_date', 'safe'],
            // The following rule is used by search().
            ['id, set_id, operation, gender, age_min, age_max, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'systemic_surgery_set' => [self::BELONGS_TO, SystemicSurgerySet::class, 'set_id'],
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'last_modified_user' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'set_id' => 'Set',
            'operation' => 'Operation',
            'gender' => 'Sex',
            'age_min' => 'Age Min',
            'age_max' => 'Age Max',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
    }

    public function beforeSave()
    {
        if (!$this->age_min || $this->age_min === 0) {
            $this->age_min = null;
        }

        if (!$this->age_max || $this->age_max === 0) {
            $this->age_max = null;
        }

        if (!$this->gender) {
            $this->gender = null;
        }

        return parent::beforeSave();
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
     * @return \CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('set_id', $this->set_id);
        $criteria->compare('operation', $this->operation, true);
        $criteria->compare('gender', $this->gender, true);
        $criteria->compare('age_min', $this->age_min, true);
        $criteria->compare('age_max', $this->age_max, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, [
            'criteria'=>$criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SystemicSurgerySetEntry the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
