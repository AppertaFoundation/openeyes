<?php
/**
 * OpenEyes.
 *
 * (C) Copyright Apperta Foundation, 2020
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

namespace OEModule\OphGeneric\models;

use OE\factories\models\traits\HasFactory;
use OEModule\OphGeneric\widgets\DeviceInformation as DeviceInformationWidget;

class DeviceInformation extends \BaseEventTypeElement
{
    use HasFactory;

    public $widgetClass = DeviceInformationWidget::class;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophgeneric_device_information';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // Only define rules for those attributes with user inputs.
        return array(
            array('manufacturer , model_version , software_version', 'length', 'max' => 255),
            // Remove attributes that should not be searched.
            array('manufacturer , model_version , software_version', 'safe', 'on' => 'search'),
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
            'event' => array(self::BELONGS_TO, \Event::class, 'event_id'),
            'createdUser' => array(self::BELONGS_TO, \User::class, 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, \User::class, 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'manufacturer' => 'Manufacturer',
            'manufacturer_model_name' => 'Manufacturer model name',
            'modality' => 'Modality',
            'series_description' => 'Series description',
            'laterality' => 'Laterality',
            'image_laterality' => 'Image laterality',
            'study_description' => 'Study description',
            'document_title' => 'Document title',
            'acquisition_date_time' => 'Acquisition date time',
            'study_date' => 'Study date',
            'study_time' => 'Study time',
            'content_date' => 'Content date',
            'content_time' => 'Content time',
            'station_name' => 'Station name',
            'operators_name' => 'Operators name',
            'last_request_id' => 'Last request id',
            'software_version' => 'Software version',
            'study_instance_uid' => 'Study instance UID',
            'series_instance_uid' => 'Series instance UID',
            'study_id' => 'Study id',
            'series_number' => 'Series number',
            'instance_number' => 'Instance number',
            'modifying_system' => 'Modifying system',
            'operator_identification_sequence' => 'Operator identification sequence',
            'model_version' => 'Model version',
            'sop_instance_uid' => 'SOP Instance UID',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria;

        $criteria->compare('comment', $this->comment, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}
