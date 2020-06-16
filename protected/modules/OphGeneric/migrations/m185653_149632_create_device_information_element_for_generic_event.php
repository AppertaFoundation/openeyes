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

class m185653_149632_create_device_information_element_for_generic_event extends \OEMigration
{
    public function safeUp()
    {
        $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphGeneric'))->queryScalar();
        $this->insert('element_type', [
            'name' => 'Device Information',
            'class_name' => 'OEModule\OphGeneric\models\DeviceInformation',
            'event_type_id' => $event_type_id,
            'display_order' => 11,
            'required' => 1]);

        $this->createOETable(
            'et_ophgeneric_device_information',
            [
                'id' => 'pk',
                'manufacturer' => 'text NULL',
                'manufacturer_model_name' => 'text NULL',
                'modality' => 'text NULL',
                'series_description' => 'text NULL',
                'laterality' => 'text NULL',
                'image_laterality' => 'text NULL',
                'study_description' => 'text NULL',
                'document_title' => 'text NULL',
                'acquisition_date_time' => 'text NULL',
                'study_date' => 'text NULL',
                'study_time' => 'text NULL',
                'content_date' => 'text NULL',
                'content_time' => 'text NULL',
                'station_name' => 'text NULL',
                'operators_name' => 'text NULL',
                'last_request_id' => 'int NULL',
                'software_version' => 'text NULL',
                'study_instance_uid' => 'text NULL',
                'series_instance_uid' => 'text NULL',
                'study_id' => 'text NULL',
                'series_number' => 'text NULL',
                'instance_number' => 'text NULL',
                'modifying_system' => 'text NULL',
                'operator_identification_sequence' => 'text NULL',
                'model_version' => 'text NULL',
                'sop_instance_uid' => 'text NULL',
                'event_id' => 'int(10) unsigned NOT NULL'
            ],
            true
        );
        $this->addForeignKey('et_ophgeneric_device_information_event_fk', 'et_ophgeneric_device_information', 'event_id', 'event', 'id');
    }

    public function safeDown()
    {
        $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphGeneric'))->queryScalar();
        $this->delete('element_type', ['class_name' => 'OEModule\OphGeneric\models\DeviceInformation', 'event_type_id' => $event_type_id]);
        $this->dropForeignKey('et_ophgeneric_device_information_event_fk', 'et_ophgeneric_device_information');
        $this->dropOETable('et_ophgeneric_device_information');
    }
}
