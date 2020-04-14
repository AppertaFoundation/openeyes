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
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "request_queue".
 *
 * The followings are the available columns in table 'request_queue':
 * @property string $request_queue
 * @property integer $maximum_active_threads
 * @property integer $total_active_thread_count
 * @property integer $total_execute_count
 * @property integer $busy_yield_ms
 * @property integer $idle_yield_ms
 * @property string $last_poll_date
 * @property string $last_thread_spawn_date
 * @property integer $last_thread_spawn_request_id
 *
 * The followings are the available model relations:
 * @property Request[] $requests
 * @property RequestRoutine[] $requestRoutines
 * @property RequestType[] $requestTypes
 */
class RequestQueue extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RequestQueue the static model class
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
        return 'request_queue';
    }

    public function behaviors()
    {
        return array(
            'OeDateFormat' => array(
                'class' => 'application.behaviors.OeDateFormat',
                'date_columns' => ['last_poll_date', 'last_thread_spawn_date'],
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
            ['request_queue, maximum_active_threads, busy_yield_ms, idle_yield_ms', 'required'],
            ['maximum_active_threads, total_active_thread_count, total_execute_count, busy_yield_ms, idle_yield_ms, last_thread_spawn_request_id', 'numerical', 'integerOnly' => true],
            ['request_queue', 'length', 'max' => 45],
            ['last_poll_date, last_thread_spawn_date', 'safe'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['request_queue, maximum_active_threads, total_active_thread_count, total_execute_count, busy_yield_ms, idle_yield_ms, last_poll_date, last_thread_spawn_date, last_thread_spawn_request_id', 'safe', 'on' => 'search'],
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
            'requests' => [self::HAS_MANY, 'Request', 'request_override_default_queue'],
            'requestRoutines' => [self::HAS_MANY, 'RequestRoutine', 'execute_request_queue'],
            'requestTypes' => [self::HAS_MANY, 'RequestType', 'default_request_queue'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'request_queue' => 'Request Queue',
            'maximum_active_threads' => 'Maximum Active Threads',
            'total_active_thread_count' => 'Total Active Thread Count',
            'total_execute_count' => 'Total Execute Count',
            'busy_yield_ms' => 'Busy Yield Ms',
            'idle_yield_ms' => 'Idle Yield Ms',
            'last_poll_date' => 'Last Poll Date',
            'last_thread_spawn_date' => 'Last Thread Spawn Date',
            'last_thread_spawn_request_id' => 'Last Thread Spawn Request',
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

        $criteria->compare('request_queue', $this->request_queue, true);
        $criteria->compare('maximum_active_threads', $this->maximum_active_threads);
        $criteria->compare('total_active_thread_count', $this->total_active_thread_count);
        $criteria->compare('total_execute_count', $this->total_execute_count);
        $criteria->compare('busy_yield_ms', $this->busy_yield_ms);
        $criteria->compare('idle_yield_ms', $this->idle_yield_ms);
        $criteria->compare('last_poll_date', $this->last_poll_date, true);
        $criteria->compare('last_thread_spawn_date', $this->last_thread_spawn_date, true);
        $criteria->compare('last_thread_spawn_request_id', $this->last_thread_spawn_request_id);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
}
