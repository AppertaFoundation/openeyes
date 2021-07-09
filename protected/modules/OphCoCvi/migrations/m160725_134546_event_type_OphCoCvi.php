<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class m160725_134546_event_type_OphCoCvi extends CDbMigration
{
	public function up()
	{
		if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphCoCvi'))->queryRow()) {
			$group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name',array(':name'=>'Communication events'))->queryRow();
			$this->insert('event_type', array('class_name' => 'OphCoCvi', 'name' => 'CVI','event_group_id' => $group['id']));
		}
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphCoCvi'))->queryRow();

		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name'=>'Event Info',':eventTypeId'=>$event_type['id']))->queryRow()) {
			$this->insert('element_type', array('name' => 'Event Info','class_name' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo', 'event_type_id' => $event_type['id'], 'display_order' => 1, 'required' => 1));
		}

		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name'=>'Clinical Info',':eventTypeId'=>$event_type['id']))->queryRow()) {
			$this->insert('element_type', array('name' => 'Clinical Info','class_name' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo', 'event_type_id' => $event_type['id'], 'display_order' => 10, 'required' => 1));
		}

		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name'=>'Consent Signature',':eventTypeId'=>$event_type['id']))->queryRow()) {
			$this->insert('element_type', array('name' => 'Consent Signature','class_name' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsentSignature', 'event_type_id' => $event_type['id'], 'display_order' => 20, 'required' => 1));
		}

		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name'=>'Clerical Info',':eventTypeId'=>$event_type['id']))->queryRow()) {
			$this->insert('element_type', array('name' => 'Clerical Info','class_name' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo', 'event_type_id' => $event_type['id'], 'display_order' => 30, 'required' => 1));
		}



		$this->createTable('et_ophcocvi_eventinfo', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'is_draft' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
			'generated_document_id' => 'int(10) unsigned',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `et_ophcocvi_eventinfo_lmui_fk` (`last_modified_user_id`)',
			'KEY `et_ophcocvi_eventinfo_cui_fk` (`created_user_id`)',
			'KEY `et_ophcocvi_eventinfo_ev_fk` (`event_id`)',
			'KEY `et_ophcocvi_eventinfo_generated_document_id_fk` (`generated_document_id`)',
			'CONSTRAINT `et_ophcocvi_eventinfo_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_eventinfo_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_eventinfo_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `et_ophcocvi_eventinfo_generated_document_id_fk` FOREIGN KEY (`generated_document_id`) REFERENCES `protected_file` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('et_ophcocvi_eventinfo_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'event_id' => 'int(10) unsigned NOT NULL',
			'is_draft' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // Is draft
			'generated_document_id' => 'int(10) unsigned', // Generated file
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_et_ophcocvi_eventinfo_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_et_ophcocvi_eventinfo_cui_fk` (`created_user_id`)',
			'KEY `acv_et_ophcocvi_eventinfo_ev_fk` (`event_id`)',
			'KEY `et_ophcocvi_eventinfo_aid_fk` (`id`)',
			'KEY `acv_et_ophcocvi_eventinfo_generated_document_id_fk` (`generated_document_id`)',
			'CONSTRAINT `acv_et_ophcocvi_eventinfo_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_eventinfo_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_eventinfo_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `et_ophcocvi_eventinfo_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophcocvi_eventinfo` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_eventinfo_generated_document_id_fk` FOREIGN KEY (`generated_document_id`) REFERENCES `protected_file` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clinicinfo_low_vision_status', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `ophcocvi_clinicinfo_low_vision_status_lmui_fk` (`last_modified_user_id`)',
			'KEY `ophcocvi_clinicinfo_low_vision_status_cui_fk` (`created_user_id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_low_vision_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_low_vision_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clinicinfo_low_vision_status_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_ophcocvi_clinicinfo_low_vision_status_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_ophcocvi_clinicinfo_low_vision_status_cui_fk` (`created_user_id`)',
			'KEY `ophcocvi_clinicinfo_low_vision_status_aid_fk` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_low_vision_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_low_vision_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_low_vision_status_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcocvi_clinicinfo_low_vision_status` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('ophcocvi_clinicinfo_low_vision_status',array('name'=>'Has been assessed','display_order'=>1));
		$this->insert('ophcocvi_clinicinfo_low_vision_status',array('name'=>'To be referred / assessed','display_order'=>2));
		$this->insert('ophcocvi_clinicinfo_low_vision_status',array('name'=>'Not relevant or the patient does not want an assessment','display_order'=>3));

		$this->createTable('ophcocvi_clinicinfo_field_of_vision', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `ophcocvi_clinicinfo_field_of_vision_lmui_fk` (`last_modified_user_id`)',
			'KEY `ophcocvi_clinicinfo_field_of_vision_cui_fk` (`created_user_id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_field_of_vision_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_field_of_vision_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clinicinfo_field_of_vision_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_ophcocvi_clinicinfo_field_of_vision_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_ophcocvi_clinicinfo_field_of_vision_cui_fk` (`created_user_id`)',
			'KEY `ophcocvi_clinicinfo_field_of_vision_aid_fk` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_field_of_vision_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_field_of_vision_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_field_of_vision_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcocvi_clinicinfo_field_of_vision` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('ophcocvi_clinicinfo_field_of_vision',array('name'=>'Total loss of visual field','display_order'=>1));
		$this->insert('ophcocvi_clinicinfo_field_of_vision',array('name'=>'Extensive loss of visual field (including hemianopia)','display_order'=>2));
		$this->insert('ophcocvi_clinicinfo_field_of_vision',array('name'=>'Primary loss of peripheral field','display_order'=>3));
		$this->insert('ophcocvi_clinicinfo_field_of_vision',array('name'=>'Primary loss of central field','display_order'=>4));

		$this->createTable('ophcocvi_clinicinfo_disorder', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'default' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `ophcocvi_clinicinfo_disorder_lmui_fk` (`last_modified_user_id`)',
			'KEY `ophcocvi_clinicinfo_disorder_cui_fk` (`created_user_id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_disorder_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_disorder_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clinicinfo_disorder_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'default' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_ophcocvi_clinicinfo_disorder_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_ophcocvi_clinicinfo_disorder_cui_fk` (`created_user_id`)',
			'KEY `ophcocvi_clinicinfo_disorder_aid_fk` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_disorder_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_disorder_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_disorder_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcocvi_clinicinfo_disorder` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('et_ophcocvi_clinicinfo', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'examination_date' => 'date',

			'is_considered_blind' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',

			'sight_varies_by_light_levels' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',

			'unaided_right_va' => 'varchar(255) DEFAULT \'\'',

			'unaided_left_va' => 'varchar(255) DEFAULT \'\'',

			'best_corrected_right_va' => 'varchar(255) DEFAULT \'\'',

			'best_corrected_left_va' => 'varchar(255) DEFAULT \'\'',

			'best_corrected_binocular_va' => 'varchar(255) DEFAULT \'\'',

			'low_vision_status_id' => 'int(10) unsigned',

			'field_of_vision_id' => 'int(10) unsigned',

			'diagnoses_not_covered' => 'text DEFAULT \'\'',

			'consultant_id' => 'int(10) unsigned',

			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `et_ophcocvi_clinicinfo_lmui_fk` (`last_modified_user_id`)',
			'KEY `et_ophcocvi_clinicinfo_cui_fk` (`created_user_id`)',
			'KEY `et_ophcocvi_clinicinfo_ev_fk` (`event_id`)',
			'KEY `ophcocvi_clinicinfo_low_vision_status_fk` (`low_vision_status_id`)',
			'KEY `ophcocvi_clinicinfo_field_of_vision_fk` (`field_of_vision_id`)',
			'KEY `et_ophcocvi_clinicinfo_consultant_id_fk` (`consultant_id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_low_vision_status_fk` FOREIGN KEY (`low_vision_status_id`) REFERENCES `ophcocvi_clinicinfo_low_vision_status` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_field_of_vision_fk` FOREIGN KEY (`field_of_vision_id`) REFERENCES `ophcocvi_clinicinfo_field_of_vision` (`id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('et_ophcocvi_clinicinfo_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'event_id' => 'int(10) unsigned NOT NULL',
			'examination_date' => 'date', // Examination date
			'is_considered_blind' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // Is considered blind
			'sight_varies_by_light_levels' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // Sight varies by light levels
			'unaided_right_va' => 'varchar(255) DEFAULT \'\'', // Unaided right VA
			'unaided_left_va' => 'varchar(255) DEFAULT \'\'', // Unaided left VA
			'best_corrected_right_va' => 'varchar(255) DEFAULT \'\'', // Best corrected right VA
			'best_corrected_left_va' => 'varchar(255) DEFAULT \'\'', // Best corrected left VA
			'best_corrected_binocular_va' => 'varchar(255) DEFAULT \'\'', // Best corrected binocular VA
			'low_vision_status_id' => 'int(10) unsigned', // Low vision status
			'field_of_vision_id' => 'int(10) unsigned', // Field of vision
			'diagnoses_not_covered' => 'text DEFAULT \'\'', // Diagnoses not covered
			'consultant_id' => 'int(10) unsigned', // Consultant
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_et_ophcocvi_clinicinfo_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_et_ophcocvi_clinicinfo_cui_fk` (`created_user_id`)',
			'KEY `acv_et_ophcocvi_clinicinfo_ev_fk` (`event_id`)',
			'KEY `et_ophcocvi_clinicinfo_aid_fk` (`id`)',
			'KEY `acv_ophcocvi_clinicinfo_low_vision_status_fk` (`low_vision_status_id`)',
			'KEY `acv_ophcocvi_clinicinfo_field_of_vision_fk` (`field_of_vision_id`)',
			'KEY `acv_et_ophcocvi_clinicinfo_consultant_id_fk` (`consultant_id`)',
			'CONSTRAINT `acv_et_ophcocvi_clinicinfo_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clinicinfo_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clinicinfo_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophcocvi_clinicinfo` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_low_vision_status_fk` FOREIGN KEY (`low_vision_status_id`) REFERENCES `ophcocvi_clinicinfo_low_vision_status` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_field_of_vision_fk` FOREIGN KEY (`field_of_vision_id`) REFERENCES `ophcocvi_clinicinfo_field_of_vision` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clinicinfo_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('et_ophcocvi_clinicinfo_disorder_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'element_id' => 'int(10) unsigned NOT NULL',
			'ophcocvi_clinicinfo_disorder_id' => 'int(10) unsigned NOT NULL',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `et_ophcocvi_clinicinfo_disorder_assignment_lmui_fk` (`last_modified_user_id`)',
			'KEY `et_ophcocvi_clinicinfo_disorder_assignment_cui_fk` (`created_user_id`)',
			'KEY `et_ophcocvi_clinicinfo_disorder_assignment_ele_fk` (`element_id`)',
			'KEY `et_ophcocvi_clinicinfo_disorder_assignment_lku_fk` (`ophcocvi_clinicinfo_disorder_id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_assignment_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcocvi_clinicinfo` (`id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_assignment_lku_fk` FOREIGN KEY (`ophcocvi_clinicinfo_disorder_id`) REFERENCES `ophcocvi_clinicinfo_disorder` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('et_ophcocvi_clinicinfo_disorder_assignment_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'element_id' => 'int(10) unsigned NOT NULL',
			'ophcocvi_clinicinfo_disorder_id' => 'int(10) unsigned NOT NULL',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_et_ophcocvi_clinicinfo_disorder_assignment_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_et_ophcocvi_clinicinfo_disorder_assignment_cui_fk` (`created_user_id`)',
			'KEY `acv_et_ophcocvi_clinicinfo_disorder_assignment_ele_fk` (`element_id`)',
			'KEY `acv_et_ophcocvi_clinicinfo_disorder_assignment_lku_fk` (`ophcocvi_clinicinfo_disorder_id`)',
			'KEY `et_ophcocvi_clinicinfo_disorder_assignment_aid_fk` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clinicinfo_disorder_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clinicinfo_disorder_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clinicinfo_disorder_assignment_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcocvi_clinicinfo` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clinicinfo_disorder_assignment_lku_fk` FOREIGN KEY (`ophcocvi_clinicinfo_disorder_id`) REFERENCES `ophcocvi_clinicinfo_disorder` (`id`)',
			'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_assignment_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophcocvi_clinicinfo_disorder_assignment` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');



		$this->createTable('et_ophcocvi_consentsig', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'is_patient' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
			'signature_date' => 'date',
			'representative_name' => 'varchar(255) DEFAULT \'\'',
			'signature_file_id' => 'int(10) unsigned',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `et_ophcocvi_consentsig_lmui_fk` (`last_modified_user_id`)',
			'KEY `et_ophcocvi_consentsig_cui_fk` (`created_user_id`)',
			'KEY `et_ophcocvi_consentsig_ev_fk` (`event_id`)',
			'KEY `et_ophcocvi_consentsig_signature_file_id_fk` (`signature_file_id`)',
			'CONSTRAINT `et_ophcocvi_consentsig_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_consentsig_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_consentsig_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `et_ophcocvi_consentsig_signature_file_id_fk` FOREIGN KEY (`signature_file_id`) REFERENCES `protected_file` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('et_ophcocvi_consentsig_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'event_id' => 'int(10) unsigned NOT NULL',
			'is_patient' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // Is patient
			'signature_date' => 'date', // Signature date
			'representative_name' => 'varchar(255) DEFAULT \'\'', // Representative name
			'signature_file_id' => 'int(10) unsigned', // Signature File
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_et_ophcocvi_consentsig_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_et_ophcocvi_consentsig_cui_fk` (`created_user_id`)',
			'KEY `acv_et_ophcocvi_consentsig_ev_fk` (`event_id`)',
			'KEY `et_ophcocvi_consentsig_aid_fk` (`id`)',
			'KEY `acv_et_ophcocvi_consentsig_signature_file_id_fk` (`signature_file_id`)',
			'CONSTRAINT `acv_et_ophcocvi_consentsig_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_consentsig_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_consentsig_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `et_ophcocvi_consentsig_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophcocvi_consentsig` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_consentsig_signature_file_id_fk` FOREIGN KEY (`signature_file_id`) REFERENCES `protected_file` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clericinfo_employment_status', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `ophcocvi_clericinfo_employment_status_lmui_fk` (`last_modified_user_id`)',
			'KEY `ophcocvi_clericinfo_employment_status_cui_fk` (`created_user_id`)',
			'CONSTRAINT `ophcocvi_clericinfo_employment_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_employment_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clericinfo_employment_status_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_ophcocvi_clericinfo_employment_status_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_ophcocvi_clericinfo_employment_status_cui_fk` (`created_user_id`)',
			'KEY `ophcocvi_clericinfo_employment_status_aid_fk` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_employment_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_employment_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_employment_status_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcocvi_clericinfo_employment_status` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Retired','display_order'=>1));
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Employed','display_order'=>2));
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Unemployed','display_order'=>3));
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Child','display_order'=>4));
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Student','display_order'=>5));

		$this->createTable('ophcocvi_clericinfo_preferred_info_fmt', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `ophcocvi_clericinfo_preferred_info_fmt_lmui_fk` (`last_modified_user_id`)',
			'KEY `ophcocvi_clericinfo_preferred_info_fmt_cui_fk` (`created_user_id`)',
			'CONSTRAINT `ophcocvi_clericinfo_preferred_info_fmt_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_preferred_info_fmt_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clericinfo_preferred_info_fmt_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_ophcocvi_clericinfo_preferred_info_fmt_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_ophcocvi_clericinfo_preferred_info_fmt_cui_fk` (`created_user_id`)',
			'KEY `ophcocvi_clericinfo_preferred_info_fmt_aid_fk` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_preferred_info_fmt_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_preferred_info_fmt_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_preferred_info_fmt_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcocvi_clericinfo_preferred_info_fmt` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('ophcocvi_clericinfo_preferred_info_fmt',array('name'=>'In large print','display_order'=>1));
		$this->insert('ophcocvi_clericinfo_preferred_info_fmt',array('name'=>'On CD','display_order'=>2));
		$this->insert('ophcocvi_clericinfo_preferred_info_fmt',array('name'=>'In braille','display_order'=>3));
		$this->insert('ophcocvi_clericinfo_preferred_info_fmt',array('name'=>'By email','display_order'=>4));

		$this->createTable('ophcocvi_clericinfo_contact_urgency', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `ophcocvi_clericinfo_contact_urgency_lmui_fk` (`last_modified_user_id`)',
			'KEY `ophcocvi_clericinfo_contact_urgency_cui_fk` (`created_user_id`)',
			'CONSTRAINT `ophcocvi_clericinfo_contact_urgency_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_contact_urgency_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clericinfo_contact_urgency_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_ophcocvi_clericinfo_contact_urgency_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_ophcocvi_clericinfo_contact_urgency_cui_fk` (`created_user_id`)',
			'KEY `ophcocvi_clericinfo_contact_urgency_aid_fk` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_contact_urgency_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_contact_urgency_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_contact_urgency_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcocvi_clericinfo_contact_urgency` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('ophcocvi_clericinfo_contact_urgency',array('name'=>'Immediately (i.e. potential risk factors present)','display_order'=>1));
		$this->insert('ophcocvi_clericinfo_contact_urgency',array('name'=>'Within the next 2 weeks (in line with Association of Directors of Social Services\' national standards)','display_order'=>2));
		$this->insert('ophcocvi_clericinfo_contact_urgency',array('name'=>'As soon as possible','display_order'=>3));



		$this->createTable('et_ophcocvi_clericinfo', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'employment_status_id' => 'int(10) unsigned',

			'preferred_info_fmt_id' => 'int(10) unsigned',

			'info_email' => 'varchar(255) DEFAULT \'\'',

			'contact_urgency_id' => 'int(10) unsigned',

			'preferred_language_id' => 'int(10) unsigned',

			'social_service_comments' => 'text DEFAULT \'\'',

			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `et_ophcocvi_clericinfo_lmui_fk` (`last_modified_user_id`)',
			'KEY `et_ophcocvi_clericinfo_cui_fk` (`created_user_id`)',
			'KEY `et_ophcocvi_clericinfo_ev_fk` (`event_id`)',
			'KEY `ophcocvi_clericinfo_employment_status_fk` (`employment_status_id`)',
			'KEY `ophcocvi_clericinfo_preferred_info_fmt_fk` (`preferred_info_fmt_id`)',
			'KEY `ophcocvi_clericinfo_contact_urgency_fk` (`contact_urgency_id`)',
			'KEY `et_ophcocvi_clericinfo_preferred_language_id_fk` (`preferred_language_id`)',
			'CONSTRAINT `et_ophcocvi_clericinfo_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_clericinfo_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `et_ophcocvi_clericinfo_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_employment_status_fk` FOREIGN KEY (`employment_status_id`) REFERENCES `ophcocvi_clericinfo_employment_status` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_preferred_info_fmt_fk` FOREIGN KEY (`preferred_info_fmt_id`) REFERENCES `ophcocvi_clericinfo_preferred_info_fmt` (`id`)',
			'CONSTRAINT `ophcocvi_clericinfo_contact_urgency_fk` FOREIGN KEY (`contact_urgency_id`) REFERENCES `ophcocvi_clericinfo_contact_urgency` (`id`)',
			'CONSTRAINT `et_ophcocvi_clericinfo_preferred_language_id_fk` FOREIGN KEY (`preferred_language_id`) REFERENCES `language` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('et_ophcocvi_clericinfo_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'event_id' => 'int(10) unsigned NOT NULL',
			'employment_status_id' => 'int(10) unsigned', // Employment status
			'preferred_info_fmt_id' => 'int(10) unsigned', // Preferred information format
			'info_email' => 'varchar(255) DEFAULT \'\'', // Info email
			'contact_urgency_id' => 'int(10) unsigned', // Contact urgency
			'preferred_language_id' => 'int(10) unsigned', // Preferred language
			'social_service_comments' => 'text DEFAULT \'\'', // Social service comments
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_et_ophcocvi_clericinfo_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_et_ophcocvi_clericinfo_cui_fk` (`created_user_id`)',
			'KEY `acv_et_ophcocvi_clericinfo_ev_fk` (`event_id`)',
			'KEY `et_ophcocvi_clericinfo_aid_fk` (`id`)',
			'KEY `acv_ophcocvi_clericinfo_employment_status_fk` (`employment_status_id`)',
			'KEY `acv_ophcocvi_clericinfo_preferred_info_fmt_fk` (`preferred_info_fmt_id`)',
			'KEY `acv_ophcocvi_clericinfo_contact_urgency_fk` (`contact_urgency_id`)',
			'KEY `acv_et_ophcocvi_clericinfo_preferred_language_id_fk` (`preferred_language_id`)',
			'CONSTRAINT `acv_et_ophcocvi_clericinfo_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clericinfo_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clericinfo_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `et_ophcocvi_clericinfo_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophcocvi_clericinfo` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_employment_status_fk` FOREIGN KEY (`employment_status_id`) REFERENCES `ophcocvi_clericinfo_employment_status` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_preferred_info_fmt_fk` FOREIGN KEY (`preferred_info_fmt_id`) REFERENCES `ophcocvi_clericinfo_preferred_info_fmt` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clericinfo_contact_urgency_fk` FOREIGN KEY (`contact_urgency_id`) REFERENCES `ophcocvi_clericinfo_contact_urgency` (`id`)',
			'CONSTRAINT `acv_et_ophcocvi_clericinfo_preferred_language_id_fk` FOREIGN KEY (`preferred_language_id`) REFERENCES `language` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

	}

	public function down()
	{
		$this->dropTable('et_ophcocvi_eventinfo_version');
		$this->dropTable('et_ophcocvi_eventinfo');



		$this->dropTable('et_ophcocvi_clinicinfo_disorder_assignment_version');
		$this->dropTable('et_ophcocvi_clinicinfo_disorder_assignment');
		$this->dropTable('et_ophcocvi_clinicinfo_version');
		$this->dropTable('et_ophcocvi_clinicinfo');


		$this->dropTable('ophcocvi_clinicinfo_low_vision_status_version');
		$this->dropTable('ophcocvi_clinicinfo_low_vision_status');
		$this->dropTable('ophcocvi_clinicinfo_field_of_vision_version');
		$this->dropTable('ophcocvi_clinicinfo_field_of_vision');
		$this->dropTable('ophcocvi_clinicinfo_disorder_version');
		$this->dropTable('ophcocvi_clinicinfo_disorder');

		$this->dropTable('et_ophcocvi_consentsig_version');
		$this->dropTable('et_ophcocvi_consentsig');



		$this->dropTable('et_ophcocvi_clericinfo_version');
		$this->dropTable('et_ophcocvi_clericinfo');


		$this->dropTable('ophcocvi_clericinfo_employment_status_version');
		$this->dropTable('ophcocvi_clericinfo_employment_status');
		$this->dropTable('ophcocvi_clericinfo_preferred_info_fmt_version');
		$this->dropTable('ophcocvi_clericinfo_preferred_info_fmt');
		$this->dropTable('ophcocvi_clericinfo_contact_urgency_version');
		$this->dropTable('ophcocvi_clericinfo_contact_urgency');


		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphCoCvi'))->queryRow();

		foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id'=>$event_type['id']))->queryAll() as $row) {
			$this->delete('audit', 'event_id='.$row['id']);
			$this->delete('event', 'id='.$row['id']);
		}

		$this->delete('element_type', 'event_type_id='.$event_type['id']);
		$this->delete('event_type', 'id='.$event_type['id']);
	}
}
