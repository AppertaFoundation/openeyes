<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m130913_000004_consolidation_for_ophtroperationbooking extends OEMigration
{
    private $element_types;

    public function setData()
    {
        $this->element_types = array(
            'Element_OphTrOperationbooking_Diagnosis' => array('name' => 'Diagnosis', 'display_order' => 10),
            'Element_OphTrOperationbooking_Operation' => array('name' => 'Operation', 'display_order' => 20),
            'Element_OphTrOperationbooking_ScheduleOperation' => array('name' => 'Schedule operation', 'display_order' => 30),
        );
    }

    public function up()
    {
        if (!$this->consolidate(
            array(
                'm121114_105958_event_type_OphTrOperationbooking',
                'm121128_122049_ophtroperationbooking_operation_name_rule',
                'm121128_150949_admission_letter_warning_rules',
                'm121129_091456_waiting_list_contacts',
                'm130225_112407_add_completed_status',
                'm130307_163805_fix_letter_warning_rules',
                'm130423_163100_fix_theatre_sort',
                'm130513_124510_rename_colliding_fields',
                'm130515_100455_point_operation_at_latest_booking',
                'm130524_143046_add_firm_id_to_letter_warning_rules_table',
                'm130531_140931_letter_contact_rules_is_child',
                'm130604_102839_patient_shortcodes',
                'm130611_100204_theatre_ward_assignments',
                'm130621_105848_soft_deletion_of_sequences_and_sessions',
                'm130621_153035_soft_deletion_of_theatres',
            )
        )
        ) {
            $this->createTables();
        }
    }

    public function down()
    {
        echo "You cannot migrate down past a consolidation migration\n";

        return false;
    }

    public function safeUp()
    {
        $this->up();
    }

    public function safeDown()
    {
        $this->down();
    }

    protected function createTables()
    {
        $this->setData();
        //disable foreign keys check
        Yii::app()->cache->flush();
        $this->execute('SET foreign_key_checks = 0');

        $event_type_id = $this->insertOEEventType('Operation booking', 'OphTrOperationbooking', 'Tr');
        $this->insertOEElementType($this->element_types, $event_type_id);

        $this->execute("CREATE TABLE `et_ophtroperationbooking_diagnosis` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `disorder_id` BIGINT unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationbooking_diagnosis_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationbooking_diagnosis_cui_fk` (`created_user_id`),
			  KEY `et_ophtroperationbooking_diagnosis_ev_fk` (`event_id`),
			  KEY `et_ophtroperationbooking_diagnosis_eye_id_fk` (`eye_id`),
			  CONSTRAINT `et_ophtroperationbooking_diagnosis_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_diagnosis_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_diagnosis_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_diagnosis_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtroperationbooking_operation` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `consultant_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `anaesthetic_type_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `overnight_stay` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `site_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `priority_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `decision_date` date DEFAULT NULL,
			  `comments` text,
			  `total_duration` smallint(5) unsigned NOT NULL,
			  `status_id` int(10) unsigned NOT NULL,
			  `anaesthetist_required` tinyint(1) unsigned DEFAULT '0',
			  `operation_cancellation_date` datetime DEFAULT NULL,
			  `cancellation_user_id` int(10) unsigned DEFAULT NULL,
			  `cancellation_reason_id` int(10) unsigned DEFAULT NULL,
			  `cancellation_comment` varchar(200) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `latest_booking_id` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationbooking_operation_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationbooking_operation_cui_fk` (`created_user_id`),
			  KEY `et_ophtroperationbooking_operation_ev_fk` (`event_id`),
			  KEY `et_ophtroperationbooking_operation_eye_id_fk` (`eye_id`),
			  KEY `et_ophtroperationbooking_operation_anaesthetic_type_id_fk` (`anaesthetic_type_id`),
			  KEY `et_ophtroperationbooking_operation_site_id_fk` (`site_id`),
			  KEY `et_ophtroperationbooking_operation_priority_fk` (`priority_id`),
			  KEY `et_ophtroperationbooking_operation_cancellation_reason_id_fk` (`cancellation_reason_id`),
			  KEY `et_ophtroperationbooking_operation_status_id_fk` (`status_id`),
			  KEY `et_ophtroperationbooking_operation_cancellation_user_id_fk` (`cancellation_user_id`),
			  KEY `et_ophtroperationbooking_operation_latest_booking_id_fk` (`latest_booking_id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_latest_booking_id_fk` FOREIGN KEY (`latest_booking_id`) REFERENCES `ophtroperationbooking_operation_booking` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_anaesthetic_type_id_fk` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_cancellation_reason_id_fk` FOREIGN KEY (`cancellation_reason_id`) REFERENCES `ophtroperationbooking_operation_cancellation_reason` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_cancellation_user_id_fk` FOREIGN KEY (`cancellation_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_priority_fk` FOREIGN KEY (`priority_id`) REFERENCES `ophtroperationbooking_operation_priority` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_status_is_fk` FOREIGN KEY (`status_id`) REFERENCES `ophtroperationbooking_operation_status` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtroperationbooking_scheduleope` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `schedule_options_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationbooking_scheduleope_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationbooking_scheduleope_cui_fk` (`created_user_id`),
			  KEY `et_ophtroperationbooking_scheduleope_ev_fk` (`event_id`),
			  KEY `et_ophtroperationbooking_scheduleope_schedule_options_fk` (`schedule_options_id`),
			  CONSTRAINT `et_ophtroperationbooking_scheduleope_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_scheduleope_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_scheduleope_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_scheduleope_schedule_options_fk` FOREIGN KEY (`schedule_options_id`) REFERENCES `ophtroperationbooking_scheduleope_schedule_options` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_admission_letter_warning_rule` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `rule_type_id` int(10) unsigned NOT NULL,
			  `parent_rule_id` int(10) unsigned DEFAULT NULL,
			  `rule_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `site_id` int(10) unsigned DEFAULT NULL,
			  `theatre_id` int(10) unsigned DEFAULT NULL,
			  `subspecialty_id` int(10) unsigned DEFAULT NULL,
			  `is_child` tinyint(1) unsigned DEFAULT NULL,
			  `show_warning` tinyint(1) unsigned NOT NULL DEFAULT '1',
			  `warning_text` text NOT NULL,
			  `emphasis` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `strong` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `firm_id` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_admission_lwr_rti_fk` (`rule_type_id`),
			  KEY `ophtroperationbooking_admission_lwr_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_admission_lwr_cui_fk` (`created_user_id`),
			  KEY `ophtroperationbooking_admission_lwr_pri_fk` (`parent_rule_id`),
			  KEY `ophtroperationbooking_admission_lwr_ti_fk` (`theatre_id`),
			  KEY `ophtroperationbooking_admission_lwr_si_fk` (`subspecialty_id`),
			  KEY `ophtroperationbooking_admission_lwr_site_fk` (`site_id`),
			  KEY `ophtroperationbooking_alw_rule_fidfk` (`firm_id`),
			  CONSTRAINT `ophtroperationbooking_alw_rule_fidfk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
			  CONSTRAINT `ophtroperationbooking_admission_lwr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_admission_lwr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_admission_lwr_pri_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperationbooking_admission_letter_warning_rule` (`id`),
			  CONSTRAINT `ophtroperationbooking_admission_lwr_rti_fk` FOREIGN KEY (`rule_type_id`) REFERENCES `ophtroperationbooking_admission_letter_warning_rule_type` (`id`),
			  CONSTRAINT `ophtroperationbooking_admission_lwr_site_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `ophtroperationbooking_admission_lwr_si_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
			  CONSTRAINT `ophtroperationbooking_admission_lwr_ti_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_admission_letter_warning_rule_type` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_admission_letter_wrt_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_admission_letter_wrt_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_admission_letter_wrt_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_admission_letter_wrt_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_letter_contact_rule` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `parent_rule_id` int(10) unsigned DEFAULT NULL,
			  `rule_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `site_id` int(10) unsigned DEFAULT NULL,
			  `subspecialty_id` int(10) unsigned DEFAULT NULL,
			  `theatre_id` int(10) unsigned DEFAULT NULL,
			  `firm_id` int(10) unsigned DEFAULT NULL,
			  `refuse_telephone` varchar(64) NOT NULL,
			  `health_telephone` varchar(64) NOT NULL,
			  `refuse_title` varchar(64) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `is_child` tinyint(1) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_letter_contact_rule_pi_fk` (`parent_rule_id`),
			  KEY `ophtroperationbooking_letter_contact_rule_site_id_fk` (`site_id`),
			  KEY `ophtroperationbooking_letter_contact_rule_subspecialty_id_fk` (`subspecialty_id`),
			  KEY `ophtroperationbooking_letter_contact_rule_theatre_id_fk` (`theatre_id`),
			  KEY `ophtroperationbooking_letter_contact_rule_firm_id_fk` (`firm_id`),
			  KEY `ophtroperationbooking_letter_contact_rule_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_letter_contact_rule_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_letter_contact_rule_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_letter_contact_rule_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
			  CONSTRAINT `ophtroperationbooking_letter_contact_rule_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_letter_contact_rule_pi_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperationbooking_letter_contact_rule` (`id`),
			  CONSTRAINT `ophtroperationbooking_letter_contact_rule_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `ophtroperationbooking_letter_contact_rule_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
			  CONSTRAINT `ophtroperationbooking_letter_contact_rule_theatre_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_booking` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_id` int(10) unsigned NOT NULL,
			  `session_id` int(10) unsigned DEFAULT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '0',
			  `ward_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `admission_time` time NOT NULL,
			  `confirmed` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `session_date` date NOT NULL,
			  `session_start_time` time NOT NULL,
			  `session_end_time` time NOT NULL,
			  `session_theatre_id` int(10) unsigned NOT NULL,
			  `transport_arranged` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `transport_arranged_date` date DEFAULT NULL,
			  `booking_cancellation_date` datetime DEFAULT NULL,
			  `cancellation_reason_id` int(10) unsigned DEFAULT NULL,
			  `cancellation_comment` varchar(200) NOT NULL,
			  `cancellation_user_id` int(10) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationbooking_operation_booking_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationbooking_operation_booking_cui_fk` (`created_user_id`),
			  KEY `et_ophtroperationbooking_operation_booking_ele_fk` (`element_id`),
			  KEY `et_ophtroperationbooking_operation_booking_wid_fk` (`ward_id`),
			  KEY `et_ophtroperationbooking_operation_booking_sti_fk` (`session_theatre_id`),
			  KEY `et_ophtroperationbooking_operation_booking_cri_fk` (`cancellation_reason_id`),
			  KEY `et_ophtroperationbooking_operation_booking_ses_fk` (`session_id`),
			  KEY `et_ophtroperationbooking_operation_booking_caui_fk` (`cancellation_user_id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_booking_caui_fk` FOREIGN KEY (`cancellation_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_booking_cri_fk` FOREIGN KEY (`cancellation_reason_id`) REFERENCES `ophtroperationbooking_operation_cancellation_reason` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_booking_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_booking_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperationbooking_operation` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_booking_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_booking_ses_fk` FOREIGN KEY (`session_id`) REFERENCES `ophtroperationbooking_operation_session` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_booking_sti_fk` FOREIGN KEY (`session_theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`),
			  CONSTRAINT `et_ophtroperationbooking_operation_booking_wid_fk` FOREIGN KEY (`ward_id`) REFERENCES `ophtroperationbooking_operation_ward` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_cancellation_reason` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `text` varchar(255) NOT NULL DEFAULT '',
			  `parent_id` int(10) unsigned DEFAULT NULL,
			  `list_no` tinyint(2) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_cancellation_reason_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_cancellation_reason_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_cancellation_reason_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_cancellation_reason_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_date_letter_sent` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_id` int(10) unsigned NOT NULL,
			  `date_invitation_letter_sent` datetime DEFAULT NULL,
			  `date_1st_reminder_letter_sent` datetime DEFAULT NULL,
			  `date_2nd_reminder_letter_sent` datetime DEFAULT NULL,
			  `date_gp_letter_sent` datetime DEFAULT NULL,
			  `date_scheduling_letter_sent` datetime DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `element_id` (`element_id`),
			  KEY `ophtroperationbooking_operation_dls_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_dls_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_dls_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_dls_element_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperationbooking_operation` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_dls_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_erod` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `session_id` int(10) unsigned NOT NULL,
			  `session_date` date NOT NULL,
			  `session_start_time` time NOT NULL,
			  `session_end_time` time NOT NULL,
			  `firm_id` int(10) unsigned NOT NULL,
			  `consultant` tinyint(1) unsigned NOT NULL,
			  `paediatric` tinyint(1) unsigned NOT NULL,
			  `anaesthetist` tinyint(1) unsigned NOT NULL,
			  `general_anaesthetic` tinyint(1) unsigned NOT NULL,
			  `session_duration` int(10) unsigned NOT NULL,
			  `total_operations_time` int(10) unsigned NOT NULL,
			  `available_time` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_erod_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_erod_cui_fk` (`created_user_id`),
			  KEY `ophtroperationbooking_operation_erod_element_id_fk` (`element_id`),
			  KEY `ophtroperationbooking_operation_erod_session_id_fk` (`session_id`),
			  KEY `ophtroperationbooking_operation_erod_firm_id_fk` (`firm_id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperationbooking_operation` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_session_id_fk` FOREIGN KEY (`session_id`) REFERENCES `ophtroperationbooking_operation_session` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_erod_rule` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `subspecialty_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_erod_rule_sid_fk` (`subspecialty_id`),
			  KEY `ophtroperationbooking_operation_erod_rule_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_erod_rule_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_rule_sid_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_rule_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_rule_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_erod_rule_item` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `erod_rule_id` int(10) unsigned NOT NULL,
			  `item_type` varchar(64) NOT NULL,
			  `item_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_erod_rule_item_eri_fk` (`erod_rule_id`),
			  KEY `ophtroperationbooking_operation_erod_rule_item_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_erod_rule_item_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_rule_item_eri_fk` FOREIGN KEY (`erod_rule_id`) REFERENCES `ophtroperationbooking_operation_erod_rule` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_rule_item_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_erod_rule_item_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_name_rule` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `theatre_id` int(10) unsigned DEFAULT NULL,
			  `name` varchar(64) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_name_rt_id_fk` (`theatre_id`),
			  KEY `ophtroperationbooking_operation_name_r_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_name_r_cid_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_name_rt_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_name_r_cid_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_name_r_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_priority` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_priority_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_priority_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_priority_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_priority_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_procedures_procedures` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_id` int(10) unsigned NOT NULL,
			  `proc_id` int(10) unsigned NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_procedures_procedures_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_procedures_procedures_cui_fk` (`created_user_id`),
			  KEY `ophtroperationbooking_operation_procedures_procedures_ele_fk` (`element_id`),
			  KEY `ophtroperationbooking_operation_procedures_procedures_lku_fk` (`proc_id`),
			  CONSTRAINT `ophtroperationbooking_operation_procedures_procedures_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_procedures_procedures_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_procedures_procedures_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperationbooking_operation` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_procedures_procedures_lku_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_sequence` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `firm_id` int(10) unsigned DEFAULT NULL,
			  `theatre_id` int(10) unsigned NOT NULL,
			  `start_date` date NOT NULL,
			  `start_time` time NOT NULL,
			  `end_time` time NOT NULL,
			  `end_date` date DEFAULT NULL,
			  `interval_id` int(10) unsigned NOT NULL,
			  `weekday` tinyint(1) DEFAULT NULL,
			  `week_selection` tinyint(1) DEFAULT NULL,
			  `consultant` tinyint(1) unsigned NOT NULL DEFAULT '1',
			  `paediatric` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `anaesthetist` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `general_anaesthetic` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `last_generate_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_sequence_firm_id_fk` (`firm_id`),
			  KEY `ophtroperationbooking_operation_sequence_theatre_id_fk` (`theatre_id`),
			  KEY `ophtroperationbooking_operation_sequence_interval_id_fk` (`interval_id`),
			  KEY `ophtroperationbooking_operation_sequence_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_sequence_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_sequence_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_sequence_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_sequence_interval_id_fk` FOREIGN KEY (`interval_id`) REFERENCES `ophtroperationbooking_operation_sequence_interval` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_sequence_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_sequence_theatre_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_sequence_interval` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(32) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_sequencei_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_sequencei_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_sequencei_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_sequencei_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_session` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `sequence_id` int(10) unsigned NOT NULL,
			  `firm_id` int(10) unsigned DEFAULT NULL,
			  `date` date NOT NULL,
			  `start_time` time NOT NULL,
			  `end_time` time NOT NULL,
			  `comments` text,
			  `available` tinyint(1) unsigned NOT NULL DEFAULT '1',
			  `consultant` tinyint(1) unsigned NOT NULL DEFAULT '1',
			  `paediatric` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `anaesthetist` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `general_anaesthetic` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `theatre_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_session_sequence_id_fk` (`sequence_id`),
			  KEY `ophtroperationbooking_operation_session_firm_id_fk` (`firm_id`),
			  KEY `ophtroperationbooking_operation_session_theatre_id_fk` (`theatre_id`),
			  KEY `ophtroperationbooking_operation_session_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_session_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_session_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_session_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_session_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_session_sequence_id_fk` FOREIGN KEY (`sequence_id`) REFERENCES `ophtroperationbooking_operation_sequence` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_session_theatre_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_status` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_status_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_status_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_theatre` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) DEFAULT NULL,
			  `site_id` int(10) unsigned NOT NULL,
			  `code` varchar(4) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `display_order` int(10) NOT NULL DEFAULT '1',
			  `ward_id` int(10) unsigned DEFAULT NULL,
			  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_theatre_site_id_fk` (`site_id`),
			  KEY `ophtroperationbooking_operation_theatre_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_theatre_cui_fk` (`created_user_id`),
			  KEY `ophtroperationbooking_operation_theatre_ward_id_fk` (`ward_id`),
			  CONSTRAINT `ophtroperationbooking_operation_theatre_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_theatre_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_theatre_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_theatre_ward_id_fk` FOREIGN KEY (`ward_id`) REFERENCES `ophtroperationbooking_operation_ward` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_operation_ward` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `site_id` int(10) unsigned NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `long_name` varchar(255) NOT NULL,
			  `directions` varchar(255) NOT NULL,
			  `restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `code` varchar(10) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_operation_ward_site_id_fk` (`site_id`),
			  KEY `ophtroperationbooking_operation_ward_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_operation_ward_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_operation_ward_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_ward_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_operation_ward_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_scheduleope_schedule_options` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_scheduleope_schedule_options_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_scheduleope_schedule_options_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_scheduleope_schedule_options_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_scheduleope_schedule_options_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtroperationbooking_waiting_list_contact_rule` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `parent_rule_id` int(10) unsigned DEFAULT NULL,
			  `rule_order` int(10) unsigned NOT NULL DEFAULT '0',
			  `site_id` int(10) unsigned DEFAULT NULL,
			  `service_id` int(10) unsigned DEFAULT NULL,
			  `firm_id` int(10) unsigned DEFAULT NULL,
			  `is_child` tinyint(1) unsigned DEFAULT NULL,
			  `name` varchar(64) NOT NULL,
			  `telephone` varchar(64) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationbooking_waiting_list_cr_parent_rule_id_fk` (`parent_rule_id`),
			  KEY `ophtroperationbooking_waiting_list_cr_site_id_fk` (`site_id`),
			  KEY `ophtroperationbooking_waiting_list_cr_service_id_fk` (`service_id`),
			  KEY `ophtroperationbooking_waiting_list_cr_firm_id_fk` (`firm_id`),
			  KEY `ophtroperationbooking_waiting_list_cr_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtroperationbooking_waiting_list_cr_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationbooking_waiting_list_cr_parent_rule_id_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperationbooking_waiting_list_contact_rule` (`id`),
			  CONSTRAINT `ophtroperationbooking_waiting_list_cr_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `ophtroperationbooking_waiting_list_cr_service_id_fk` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
			  CONSTRAINT `ophtroperationbooking_waiting_list_cr_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
			  CONSTRAINT `ophtroperationbooking_waiting_list_cr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationbooking_waiting_list_cr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);

        //enable foreign keys check
        $this->execute('SET foreign_key_checks = 1');
    }
}
