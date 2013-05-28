<?php

class m121114_132152_remove_booking_tables extends CDbMigration
{
	public function up()
	{
		$element_operation = ElementType::model()->find('event_type_id=? and class_name=?',array(25,'ElementOperation'));
		$element_diagnosis = ElementType::model()->find('event_type_id=? and class_name=?',array(25,'ElementDiagnosis'));
		$this->delete('element_type_anaesthetic_type','element_type_id='.$element_operation->id);
		$this->delete('element_type_eye','element_type_id='.$element_operation->id);
		$this->delete('element_type_eye','element_type_id='.$element_diagnosis->id);
		$this->delete('element_type_priority','element_type_id='.$element_operation->id);
		$this->delete('element_type','id='.$element_operation->id);
		$this->delete('element_type','id='.$element_diagnosis->id);

		$this->dropTable('element_diagnosis');
		$this->dropTable('operation_procedure_assignment');
		$this->dropTable('erod_rule_item');
		$this->dropTable('erod_rule');
		$this->dropTable('booking');
		$this->dropTable('element_operation_erod');
		$this->dropTable('session_firm_assignment');
		$this->dropTable('session');
		$this->dropTable('sequence_firm_assignment');
		$this->dropTable('sequence');
		$this->dropTable('theatre_ward_assignment');
		$this->dropTable('ward');
		$this->dropTable('cancelled_booking');
		$this->dropTable('theatre');
		$this->dropTable('cancelled_operation');
		$this->dropTable('date_letter_sent');
		$this->dropTable('element_operation');
		$this->dropTable('cancellation_reason');
		$this->dropTable('transport_list');
	}

	public function down()
	{
		$this->createTable('cancellation_reason', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'text' => "varchar(255) COLLATE utf8_bin NOT NULL DEFAULT ''",
				'parent_id' => 'int(10) unsigned DEFAULT NULL',
				'list_no' => 'tinyint(2) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `cancellation_reason_lmui_fk` (`last_modified_user_id`)',
				'KEY `cancellation_reason_cui_fk` (`created_user_id`)',
				'CONSTRAINT `cancellation_reason_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `cancellation_reason_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('element_operation', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'event_id' => "int(10) unsigned NOT NULL",
				'eye_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'comments' => "text COLLATE utf8_bin",
				'total_duration' => "smallint(5) unsigned NOT NULL",
				'consultant_required' => "tinyint(1) unsigned DEFAULT '0'",
				'anaesthetist_required' => "tinyint(1) unsigned DEFAULT '0'",
				'anaesthetic_type_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'overnight_stay' => "tinyint(1) unsigned DEFAULT '0'",
				'schedule_timeframe' => "tinyint(1) unsigned DEFAULT '0'",
				'status' => "int(10) unsigned NOT NULL",
				'decision_date' => "date NOT NULL",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'priority_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'site_id' => "int(10) unsigned NOT NULL DEFAULT '0'",
				'PRIMARY KEY (`id`)',
				'KEY `event_id` (`event_id`)',
				'KEY `element_operation_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_operation_created_user_id_fk` (`created_user_id`)',
				'KEY `element_operation_site_id_fk` (`site_id`)',
				'KEY `element_operation_anaesthetic_type_id_fk` (`anaesthetic_type_id`)',
				'KEY `element_operation_eye_id_fk` (`eye_id`)',
				'KEY `element_operation_priority_id_fk` (`priority_id`)',
				'CONSTRAINT `element_operation_anaesthetic_type_id_fk` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`)',
				'CONSTRAINT `element_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_operation_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
				'CONSTRAINT `element_operation_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_operation_priority_id_fk` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`id`)',
				'CONSTRAINT `element_operation_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('date_letter_sent', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'element_operation_id' => "int(10) unsigned NOT NULL",
				'date_invitation_letter_sent' => "datetime DEFAULT NULL",
				'date_1st_reminder_letter_sent' => "datetime DEFAULT NULL",
				'date_2nd_reminder_letter_sent' => "datetime DEFAULT NULL",
				'date_gp_letter_sent' => "datetime DEFAULT NULL",
				'date_scheduling_letter_sent' => "datetime DEFAULT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1901-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1901-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `element_operation_id` (`element_operation_id`)',
				'KEY `date_letter_sent_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `date_letter_sent_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `date_letter_sent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `date_letter_sent_element_operation_fk` FOREIGN KEY (`element_operation_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `date_letter_sent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('cancelled_booking', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'element_operation_id' => "int(10) unsigned NOT NULL",
				'date' => "date NOT NULL",
				'start_time' => "time NOT NULL",
				'end_time' => "time NOT NULL",
				'theatre_id' => "int(10) unsigned NOT NULL",
				'cancelled_date' => "datetime DEFAULT NULL",
				'created_user_id' => "int(10) unsigned NOT NULL",
				'cancelled_reason_id' => "int(10) unsigned NOT NULL",
				'cancellation_comment' => "varchar(200) COLLATE utf8_bin NOT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `element_operation_id` (`element_operation_id`)',
				'KEY `cancelled_reason_id` (`cancelled_reason_id`)',
				'KEY `cancelled_booking_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `cancelled_booking_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `booking_1` FOREIGN KEY (`cancelled_reason_id`) REFERENCES `cancellation_reason` (`id`)',
				'CONSTRAINT `cancelled_booking_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `cancelled_booking_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('cancelled_operation', array(
				'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT",
				'element_operation_id' => "int(10) unsigned NOT NULL",
				'cancelled_date' => "datetime DEFAULT NULL",
				'created_user_id' => "int(10) unsigned NOT NULL",
				'cancelled_reason_id' => "int(10) unsigned NOT NULL",
				'cancellation_comment' => "varchar(200) COLLATE utf8_bin NOT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `cancelled_reason_id` (`cancelled_reason_id`)',
				'KEY `operation_2` (`element_operation_id`)',
				'KEY `cancelled_operation_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `cancelled_operation_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `cancelled_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `cancelled_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `operation_1` FOREIGN KEY (`cancelled_reason_id`) REFERENCES `cancellation_reason` (`id`)',
				'CONSTRAINT `operation_2` FOREIGN KEY (`element_operation_id`) REFERENCES `element_operation` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('theatre', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'name' => "varchar(255) COLLATE utf8_bin DEFAULT NULL",
				'site_id' => "int(10) unsigned NOT NULL",
				'code' => "varchar(4) COLLATE utf8_bin NOT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `site_id` (`site_id`)',
				'KEY `theatre_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `theatre_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `theatre_1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `theatre_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `theatre_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ward', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'site_id' => "int(10) unsigned NOT NULL",
				'name' => "varchar(255) COLLATE utf8_bin NOT NULL",
				'restriction' => "tinyint(1) DEFAULT NULL",
				'code' => "varchar(10) COLLATE utf8_bin NOT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `site_id` (`site_id`)',
				'KEY `ward_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `ward_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `ward_1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `ward_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ward_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('theatre_ward_assignment', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'theatre_id' => "int(10) unsigned NOT NULL",
				'ward_id' => "int(10) unsigned NOT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `theatre_id` (`theatre_id`)',
				'KEY `ward_id` (`ward_id`)',
				'KEY `theatre_ward_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `theatre_ward_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `theatre_ward_assignment_1` FOREIGN KEY (`theatre_id`) REFERENCES `theatre` (`id`)',
				'CONSTRAINT `theatre_ward_assignment_2` FOREIGN KEY (`ward_id`) REFERENCES `ward` (`id`)',
				'CONSTRAINT `theatre_ward_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `theatre_ward_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('sequence', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'theatre_id' => "int(10) unsigned NOT NULL",
				'start_date' => "date NOT NULL",
				'start_time' => "time NOT NULL",
				'end_time' => "time NOT NULL",
				'end_date' => "date DEFAULT NULL",
				'repeat_interval' => "tinyint(1) unsigned NOT NULL",
				'weekday' => "tinyint(1) DEFAULT NULL",
				'week_selection' => "tinyint(1) DEFAULT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'consultant' => "tinyint(1) unsigned NOT NULL DEFAULT '1'",
				'paediatric' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'anaesthetist' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'general_anaesthetic' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'PRIMARY KEY (`id`)',
				'KEY `theatre_id` (`theatre_id`)',
				'KEY `sequence_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `sequence_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `sequence_1` FOREIGN KEY (`theatre_id`) REFERENCES `theatre` (`id`)',
				'CONSTRAINT `sequence_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `sequence_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('sequence_firm_assignment', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'sequence_id' => "int(10) unsigned NOT NULL",
				'firm_id' => "int(10) unsigned NOT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `firm_id` (`firm_id`)',
				'KEY `sequence_firm_assignment_1` (`sequence_id`)',
				'KEY `sequence_firm_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `sequence_firm_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `sequence_firm_assignment_1` FOREIGN KEY (`sequence_id`) REFERENCES `sequence` (`id`)',
				'CONSTRAINT `sequence_firm_assignment_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `sequence_firm_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `sequence_firm_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('session', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'sequence_id' => "int(10) unsigned NOT NULL",
				'date' => "date NOT NULL",
				'start_time' => "time NOT NULL",
				'end_time' => "time NOT NULL",
				'comments' => "text COLLATE utf8_bin",
				'status' => "int(10) unsigned NOT NULL DEFAULT '0'",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'consultant' => "tinyint(1) unsigned NOT NULL DEFAULT '1'",
				'paediatric' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'anaesthetist' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'general_anaesthetic' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'theatre_id' => "int(10) unsigned NOT NULL",
				'PRIMARY KEY (`id`)',
				'KEY `sequence_id` (`sequence_id`)',
				'KEY `session_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `session_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `session_1` FOREIGN KEY (`sequence_id`) REFERENCES `sequence` (`id`)',
				'CONSTRAINT `session_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `session_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('session_firm_assignment', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'session_id' => "int(10) unsigned NOT NULL",
				'firm_id' => "int(10) unsigned NOT NULL",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL",
				'PRIMARY KEY (`id`)',
				'KEY `session_firm_assignment_firm` (`firm_id`)',
				'CONSTRAINT `session_firm_assignment_firm` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `session_firm_assignment_session` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('element_operation_erod', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'element_operation_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'session_id' => "int(10) unsigned NOT NULL",
				'session_date' => "date NOT NULL",
				'session_start_time' => "time NOT NULL",
				'session_end_time' => "time NOT NULL",
				'firm_id' => "int(10) unsigned NOT NULL",
				'consultant' => "tinyint(1) unsigned NOT NULL",
				'paediatric' => "tinyint(1) unsigned NOT NULL",
				'anaesthetist' => "tinyint(1) unsigned NOT NULL",
				'general_anaesthetic' => "tinyint(1) unsigned NOT NULL",
				'session_duration' => "int(10) unsigned NOT NULL",
				'total_operations_time' => "int(10) unsigned NOT NULL",
				'available_time' => "int(10) unsigned NOT NULL",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `element_operation_erod_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_operation_erod_created_user_id_fk` (`created_user_id`)',
				'KEY `element_operation_erod_element_operation_id_fk` (`element_operation_id`)',
				'KEY `element_operation_erod_session_id_fk` (`session_id`)',
				'KEY `element_operation_erod_firm_id_fk` (`firm_id`)',
				'CONSTRAINT `element_operation_erod_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_operation_erod_element_operation_id_fk` FOREIGN KEY (`element_operation_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `element_operation_erod_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `element_operation_erod_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_operation_erod_session_id_fk` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('booking', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'element_operation_id' => "int(10) unsigned NOT NULL",
				'session_id' => "int(10) unsigned NOT NULL",
				'display_order' => "int(10) NOT NULL",
				'ward_id' => "int(11) unsigned DEFAULT '0'",
				'admission_time' => "time NOT NULL",
				'confirmed' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `element_operation_id` (`element_operation_id`)',
				'KEY `session_id` (`session_id`)',
				'KEY `booking_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `booking_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `appointment_1` FOREIGN KEY (`element_operation_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `appointment_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`)',
				'CONSTRAINT `booking_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `booking_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('erod_rule', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'subspecialty_id' => "int(10) unsigned NOT NULL",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `erod_rule_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `erod_rule_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `erod_rule_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `erod_rule_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('erod_rule_item', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'erod_rule_id' => "int(10) unsigned NOT NULL",
				'item_type' => "varchar(64) COLLATE utf8_bin NOT NULL",
				'item_id' => "int(10) unsigned NOT NULL",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `erod_rule_item_erod_rule_id_fk` (`erod_rule_id`)',
				'KEY `erod_rule_item_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `erod_rule_item_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `erod_rule_item_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `erod_rule_item_erod_rule_id_fk` FOREIGN KEY (`erod_rule_id`) REFERENCES `erod_rule` (`id`)',
				'CONSTRAINT `erod_rule_item_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('operation_procedure_assignment', array(
				'operation_id' => "int(10) unsigned NOT NULL",
				'proc_id' => "int(10) unsigned NOT NULL",
				'display_order' => "tinyint(3) unsigned DEFAULT '0'",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`operation_id`,`proc_id`)',
				'KEY `operation_id` (`operation_id`)',
				'KEY `procedure_id` (`proc_id`)',
				'KEY `operation_procedure_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `operation_procedure_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `operation_fk` FOREIGN KEY (`operation_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `operation_procedure_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `operation_procedure_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `operation_procedure_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('element_diagnosis', array(
				'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
				'event_id' => "int(10) unsigned NOT NULL",
				'disorder_id' => "int(10) unsigned NOT NULL",
				'eye_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `event_id` (`event_id`)',
				'KEY `disorder_id` (`disorder_id`)',
				'KEY `element_diagnosis_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_diagnosis_created_user_id_fk` (`created_user_id`)',
				'KEY `element_diagnosis_eye_id_fk` (`eye_id`)',
				'CONSTRAINT `element_diagnosis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_diagnosis_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
				'CONSTRAINT `element_diagnosis_fk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_diagnosis_fk_2` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`)',
				'CONSTRAINT `element_diagnosis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('element_type',array('name' => 'Diagnosis', 'class_name' => 'ElementDiagnosis', 'event_type_id' => 25, 'display_order' => 1, 'default' => 1));
		$this->insert('element_type',array('name' => 'Operation', 'class_name' => 'ElementOperation', 'event_type_id' => 25, 'display_order' => 2, 'default' => 1));

		$element_operation = ElementType::model()->find('event_type_id=? and class_name=?',array(25,'ElementOperation'));
		$element_diagnosis = ElementType::model()->find('event_type_id=? and class_name=?',array(25,'ElementDiagnosis'));

		for ($i=1;$i<=5;$i++) {
			$this->insert('element_type_anaesthetic_type',array('element_type_id' => $element_operation->id, 'anaesthetic_type_id' => $i, 'display_order' => $i));
		}

		$this->insert('element_type_eye',array('element_type_id' => $element_operation->id, 'eye_id' => 1, 'display_order' => 3));
		$this->insert('element_type_eye',array('element_type_id' => $element_operation->id, 'eye_id' => 2, 'display_order' => 1));
		$this->insert('element_type_eye',array('element_type_id' => $element_operation->id, 'eye_id' => 3, 'display_order' => 2));

		$this->insert('element_type_eye',array('element_type_id' => $element_diagnosis->id, 'eye_id' => 1, 'display_order' => 3));
		$this->insert('element_type_eye',array('element_type_id' => $element_diagnosis->id, 'eye_id' => 2, 'display_order' => 1));
		$this->insert('element_type_eye',array('element_type_id' => $element_diagnosis->id, 'eye_id' => 3, 'display_order' => 2));

		$this->insert('element_type_priority',array('element_type_id' => $element_operation->id, 'priority_id' => 1, 'display_order' => 1));
		$this->insert('element_type_priority',array('element_type_id' => $element_operation->id, 'priority_id' => 2, 'display_order' => 2));

		$this->createTable('transport_list', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'item_table' => 'varchar(40) CHARACTER SET utf8 NOT NULL',
				'item_id' => 'int(10) unsigned NOT NULL',
				'status' => 'int(1) unsigned NOT NULL',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `transport_list_created_user_id_fk` (`created_user_id`)',
				'KEY `transport_list_last_modified_user_id_fk` (`last_modified_user_id`)',
				'CONSTRAINT `transport_list_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `transport_list_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}
}
