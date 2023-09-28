<?php

use OE\factories\models\traits\HasFactory;

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * @property int $dispense_condition_institution_id
 * @property int $dispense_location_institution_id
 */
class OphDrPrescription_DispenseCondition_Assignment extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrprescription_dispense_condition_assignment';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['dispense_condition_institution_id, dispense_location_institution_id', 'required'],
            ['dispense_condition_institution_id, dispense_location_institution_id', 'numerical', 'integerOnly' => true],
            ['created_user_id', 'length', 'max' => 10],
            ['created_date, name, created_user_id, last_modified_user_id, last_modified_date, dispense_condition_institution_id, dispense_location_institution_id', 'safe'],
            ['created_date, name, created_user_id, last_modified_user_id, last_modified_date, dispense_condition_institution_id, dispense_location_institution_id', 'safe', 'on'=>'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'dispense_condition_institutions' => [self::BELONGS_TO, 'OphDrPrescription_DispenseCondition_Institution', 'dispense_condition_institution_id'],
            'dispense_location_institutions' => [self::BELONGS_TO, 'OphDrPrescription_DispenseLocation_Institution', 'dispense_location_institution_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dispense_condition_institution_id' => 'Dispense condition',
            'dispense_location_institution_id' => 'Dispense location',
            'created_user_id' => 'Created By',
        ];
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

        $criteria->compare('id', $this->id);
        $criteria->compare('dispense_condition_institution_id', $this->dispense_condition_institution_id);
        $criteria->compare('dispense_location_institution_id', $this->dispense_location_institution_id);

        return new CActiveDataProvider(get_class($this), [
            'criteria' => $criteria,
        ]);
    }
}
