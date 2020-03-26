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
 * This is the model class for table "request_routine".
 *
 * The followings are the available columns in table 'request_routine':
 * @property integer $id
 * @property integer $request_id
 * @property string $execute_request_queue
 * @property string $status
 * @property string $routine_name
 * @property integer $try_count
 * @property string $next_try_date_time
 *
 * The followings are the available model relations:
 * @property RequestQueue $executeRequestQueue
 * @property Request $request
 * @property RoutineLibrary $routineName
 * @property RequestRoutineExecution[] $requestRoutineExecutions
 */
class RequestRoutine extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RequestRoutine the static model class
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
        return 'request_routine';
    }

    public function behaviors()
    {
        return array(
            'OeDateFormat' => array(
                'class' => 'application.behaviors.OeDateFormat',
                'date_columns' => ['next_try_date_time'],
            ),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['request_id, execute_request_queue, status, routine_name', 'required'],
            ['request_id, try_count', 'numerical', 'integerOnly' => true],
            ['execute_request_queue, status, routine_name', 'length', 'max' => 45],
            ['next_try_date_time', 'safe'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['id, request_id, execute_request_queue, status, routine_name, try_count, next_try_date_time', 'safe', 'on' => 'search'],
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
            'executeRequestQueue' => [self::BELONGS_TO, 'RequestQueue', 'execute_request_queue'],
            'request' => [self::BELONGS_TO, 'Request', 'request_id'],
            'routineName' => [self::BELONGS_TO, 'RoutineLibrary', 'routine_name'],
            'requestRoutineExecutions' => [self::HAS_MANY, 'RequestRoutineExecution', 'request_routine_id' , 'order' => 'requestRoutineExecutions.id desc'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_id' => 'Request',
            'execute_request_queue' => 'Execute Request Queue',
            'status' => 'Status',
            'routine_name' => 'Routine Name',
            'try_count' => 'Try Count',
            'next_try_date_time' => 'Next Try Date Time',
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
        $criteria->compare('request_id', $this->request_id);
        $criteria->compare('execute_request_queue', $this->execute_request_queue, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('routine_name', $this->routine_name, true);
        $criteria->compare('try_count', $this->try_count);
        $criteria->compare('next_try_date_time', $this->next_try_date_time, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
}
