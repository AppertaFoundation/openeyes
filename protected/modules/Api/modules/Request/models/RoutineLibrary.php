<?php
/**
 * (C) Copyright Apperta Foundation 2020
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
 * This is the model class for table "routine_library".
 *
 * The followings are the available columns in table 'routine_library':
 * @property string $routine_name
 * @property string $hash_code
 *
 * The followings are the available model relations:
 * @property RequestRoutine[] $requestRoutines
 * @property RequestType[] $requestTypes
 */
class RoutineLibrary extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RoutineLibrary the static model class
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
        return 'routine_library';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['routine_name', 'required'],
            ['routine_name', 'length', 'max' => 45],
            ['routine_name', 'filter', 'filter' => 'htmlspecialchars'],
            ['hash_code', 'length', 'max' => 100],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['routine_name, hash_code', 'safe', 'on' => 'search'],
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
            'requestRoutines' => [self::HAS_MANY, 'RequestRoutine', 'routine_name'],
            'requestTypes' => [self::HAS_MANY, 'RequestType', 'default_routine_name'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'routine_name' => 'Routine Name',
            'hash_code' => 'Hash Code',
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
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('routine_name', $this->routine_name, true);
        $criteria->compare('hash_code', $this->hash_code, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
}
