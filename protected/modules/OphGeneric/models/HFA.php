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

namespace OEModule\OphGeneric\models;


use BaseActiveRecord;
use BaseEventTypeElement;
use CActiveDataProvider;
use CDbCriteria;
use CDbException;
use Event;
use Exception;
use Eye;
use OE\factories\models\traits\HasFactory;
use OEModule\OphGeneric\widgets\HFA as HFAWidget;
use PatientStatistic;
use PatientStatisticDatapoint;
use User;

/**
 * This is the model class for table "et_ophgeneric_hfa".
 *
 * The followings are the available columns in table 'et_ophgeneric_hfa':
 * @property int $id
 * @property string $event_id
 * @property int $eye_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property string $class_name
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property Event $event
 * @property Eye $eye
 * @property User $lastModifiedUser
 * @property HFAEntry[] $supportingInformationDetails
 */
class HFA extends BaseEventTypeElement
{
    use HasFactory;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    /**
     * @var string $widgetClass
     */
    public $widgetClass = HFAWidget::class;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophgeneric_hfa';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            ['hfaEntry', 'required'],
            array('last_modified_date, created_date, hfaEntry', 'safe'),
            // The following rule is used by search().
            array(
                'id, event_id, eye_id, last_modified_user_id, last_modified_date, created_user_id, created_date',
                'safe',
                'on' => 'search'
            ),
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
            'createdUser' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'event' => array(self::BELONGS_TO, Event::class, 'event_id'),
            'eye' => array(self::BELONGS_TO, Eye::class, 'eye_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            'hfaEntry' => array(self::HAS_MANY, HFAEntry::class, 'element_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'eye_id' => 'Eye',
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
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('eye_id', $this->eye_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array('criteria' => $criteria,));
    }

    public function isRequiredInUI()
    {
        return true;
    }

    /**
     * @throws CDbException
     * @throws Exception
     */
    public function softDelete()
    {
        parent::softDelete();

        // For each MD/VFI entry, remove the relevant datapoint from the datapoint table,
        // and flag the affected statistics for remodelling.
        // Age value recorded in statistic table for both statistics is point-in-time
        // (ie. current age as of event date).
        $md_datapoint = PatientStatisticDatapoint::model()->findByAttributes(
            array(
                'stat_type_mnem' => 'md',
                'event_id' => $this->event->id,
            )
        );

        /**
         * @var $md_stat PatientStatistic
         */
        $md_stat = PatientStatistic::model()->findByAttributes(
            array(
                'stat_type_mnem' => 'md',
                'patient_id' => $this->event->episode->patient_id,
                'eye_id' => $this->eye_id,
            )
        );

        if ($md_datapoint) {
            // Covers the edge cases where the statistic may have already been deleted.
            $md_datapoint->delete();
        }

        if ($md_stat) {
            $md_stat->refresh();

            // If the statistic has no more datapoints, delete it. Otherwise, flag it for remodelling.
            if (count($md_stat->datapoints) === 0) {
                $md_stat->delete();
            } else {
                $md_stat->process_datapoints = true;
                $md_stat->save();
            }
        }

        $vfi_datapoint = PatientStatisticDatapoint::model()->findByAttributes(
            array(
                'stat_type_mnem' => 'vfi',
                'event_id' => $this->event->id,
            )
        );
        /**
         * @var $vfi_stat PatientStatistic
         */
        $vfi_stat = PatientStatistic::model()->findByAttributes(
            array(
                'stat_type_mnem' => 'vfi',
                'patient_id' => $this->event->episode->patient_id,
                'eye_id' => $this->eye_id,
            )
        );

        if ($vfi_datapoint) {
            // Covers the edge cases where the statistic may have already been deleted.
            $vfi_datapoint->delete();
        }

        if ($vfi_stat) {
            $vfi_stat->refresh();

            // If the statistic has no more datapoints, delete it. Otherwise, flag it for remodelling.
            if (count($vfi_stat->datapoints) === 0) {
                $vfi_stat->delete();
            } else {
                $vfi_stat->process_datapoints = true;
                $vfi_stat->save();
            }
        }
    }

    public function getSidedData()
    {
        return [
            'right' => array_values(array_filter($this->hfaEntry, function ($item) {
                return $item->eye_id == Eye::RIGHT;
            })),
            'left' => array_values(array_filter($this->hfaEntry, function ($item) {
                return $item->eye_id == Eye::LEFT;
            }))
        ];
    }
}
