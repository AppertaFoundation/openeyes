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
 * This is the model class for table "request_routine_execution".
 *
 * The followings are the available columns in table 'request_routine_execution':
 * @property integer $id
 * @property string $log_text
 * @property integer $request_routine_id
 * @property string $execution_date_time
 * @property string $status
 * @property integer $try_number
 *
 * The followings are the available model relations:
 * @property RequestRoutine $requestRoutine
 */
class RequestRoutineExecution extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RequestRoutineExecution the static model class
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
        return 'request_routine_execution';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['request_routine_id, execution_date_time, status, try_number', 'required'],
            ['request_routine_id, try_number', 'numerical', 'integerOnly' => true],
            ['log_text', 'safe'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['id, log_text, request_routine_id, execution_date_time, status, try_number', 'safe', 'on' => 'search'],
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
            'requestRoutine' => [self::BELONGS_TO, 'RequestRoutine', 'request_routine_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'log_text' => 'Log Text',
            'request_routine_id' => 'Request Routine',
            'execution_date_time' => 'Execution Date Time',
            'status' => 'Status',
            'try_number' => 'Try Number',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('log_text', $this->log_text, true);
        $criteria->compare('request_routine_id', $this->request_routine_id);
        $criteria->compare('execution_date_time', $this->execution_date_time, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('try_number', $this->try_number);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
}
