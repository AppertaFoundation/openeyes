<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Class SurgicalHistorySetEntry
 * @property integer $id
 * @property integer $surgical_history_set_id
 * @property string $gender
 * @property string $operation
 * @property int $age_min
 * @property int $age_max
 * @property int $last_modified_user_id
 * @property string $last_modified_date
 * @property int $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property SurgicalHistorySet $surgicalHistorySet
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 */
class SurgicalHistorySetEntry extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphCiExaminationSystemicDiagnosesSetEntry the static model class
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
        return 'ophciexamination_surgical_history_set_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('operation', 'required'),
            array('gender', 'length', 'max' => 1),
            array('operation', 'length', 'max' => 1024),
            array('age_min, age_max', 'numerical', 'min' => 1, 'max' => 120, 'integerOnly' => true),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, gender, age_min, age_max, last_modified_user_id, last_modified_date, 
                    created_user_id, created_date', 'safe', 'on' => 'search'),
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
            'surgical_history_set' => array(self::BELONGS_TO, SurgicalHistorySet::class, 'set_id'),
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'last_modified_user' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'operation' => 'Operation',
            'surgical_history_set_id', 'Surgical History Set',
            'gender' => 'Gender',
            'age_min' => 'Age Min',
            'age_max' => 'Age Max',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
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
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('operation', $this->operation, true);
        $criteria->compare('gender', $this->gender);
        $criteria->compare('age_min', $this->age_min);
        $criteria->compare('age_max', $this->age_max);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
