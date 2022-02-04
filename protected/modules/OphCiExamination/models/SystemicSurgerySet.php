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
 * This is the model class for table "ophciexamination_systemic_surgery_set".
 *
 * The followings are the available columns in table 'ophciexamination_systemic_surgery_set':
 * @property integer $id
 * @property string $name
 * @property integer $institution_id
 * @property string $firm_id
 * @property string $subspecialty_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Institution $institution
 * @property \Firm $firm
 * @property \Subspecialty $subspecialty
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 * @property SystemicSurgerySetEntry[] $entries
 */
class SystemicSurgerySet extends \BaseActiveRecordVersioned
{
    protected $auto_update_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_systemic_surgery_set';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['name', 'length', 'max'=>255],
            ['name', 'required'],
            ['firm_id, subspecialty_id, last_modified_user_id, created_user_id', 'length', 'max'=>10],
            ['last_modified_date, created_date', 'safe'],
            // The following rule is used by search().
            ['id, name, institution_id, firm_id, subspecialty_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'],
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
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'firm' => [self::BELONGS_TO, 'Firm', 'firm_id'],
            'subspecialty' => [self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'],
            'created_user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'last_modified_user' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'entries' => [self::HAS_MANY, SystemicSurgerySetEntry::class, 'set_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'institution_id' => 'Institution',
            'firm_id' => 'Firm',
            'subspecialty_id' => 'Subspecialty',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
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
    public function search($current_institution_only = false)
    {
        $criteria=new  \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('institution_id', $this->institution_id, true);
        $criteria->compare('firm_id', $this->firm_id, true);
        $criteria->compare('subspecialty_id', $this->subspecialty_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        if ($current_institution_only) {
            $criteria->addCondition('institution_id = :institution_id');
            $criteria->params[':institution_id'] = \Yii::app()->session['selected_institution_id'];
        }

        return new \CActiveDataProvider($this, [
            'criteria'=>$criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SystemicSurgerySet the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
