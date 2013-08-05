<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Consolidates all migrations up to 23/02/2012 into a single step
 *
 * This migration will work on a blank database, or a database that has already been migrated up to
 * or beyond 23/02/2012 (m120222_115209_new_general_anaesthetic_field_for_sessions_and_sequences).
 * Databases not migrated up to this point are no longer supported
 */
class m120223_000000_consolidation extends CDbMigration
{
	public function up()
	{
		// Check for existing migrations
		$existing_migrations = $this->getDbConnection()->createCommand("SELECT count(version) FROM `tbl_migration`")->queryScalar();
		if ($existing_migrations == 1) {
			// This is the first migration, so we can safely initialise the database
			$this->execute("SET foreign_key_checks = 0");
			echo "Creating tables...";
			$this->createTables();
			echo "Initialising data...";
			$this->initialiseData();
			$this->execute("SET foreign_key_checks = 1");
		} else {
			// Database has existing migrations, so check that last migration step to be consolidated was applied
			$previous_migration = $this->getDbConnection()->createCommand("SELECT * FROM `tbl_migration` WHERE version = 'm120222_115209_new_general_anaesthetic_field_for_sessions_and_sequences'")->execute();
			if ($previous_migration) {
				// Previous migration was applied, safe to consolidate
				echo "Consolidating old migration data";
				$this->execute("DELETE FROM `tbl_migration` WHERE version < 'm120223_000000_consolidation'");
			} else {
				// Database is not migrated up to the consolidation point, cannot migrate
				echo "Previous migrations missing or incomplete, migration not possible\n";
				return false;
			}
		}
	}

	/**
	 * Initialise tables with default data
	 */
	protected function initialiseData()
	{
		$path = Yii::app()->basePath . '/migrations/data/';
		foreach (glob($path."*.csv") as $file_path) {
			$table = substr(basename($file_path), 0, -4);
			echo "Importing $table data...";
			$fh = fopen($file_path, 'r');
			$columns = fgetcsv($fh);
			$row_count = 0;
			$block_size = 1000;
			$values = array();
			while (($record = fgetcsv($fh)) !== false) {
				$row_count++;
				$values[] = $record;
				if (!($row_count % $block_size)) {
					// Insert values in blocks to better handle very large tables
					$this->insertBlock($table, $columns, $values);
					$values = array();
				}
			}
			fclose($fh);
			if (!empty($values)) {
				// Insert remaining values
				$this->insertBlock($table, $columns, $values);
			}
			echo "$row_count records, done.\n";
		}
	}

	/**
	 * Insert a block of records into a table
	 * @param string $table
	 * @param array $columns
	 * @param array $records
	 */
	protected function insertBlock($table, $columns, $records)
	{
		$db = $this->getDbConnection();
		foreach ($columns as &$column) {
			$column = $db->quoteColumnName($column);
		}
		$insert = array();
		foreach ($records as $record) {
			foreach ($record as &$field) {
				$field = $db->quoteValue($field);
			}
			$insert[] = '('.implode(',', $record).')';
		}
		$query = "INSERT INTO ".$db->quoteTableName($table)." (".implode(',',$columns).") VALUES ".implode(',', $insert);
		$this->getDbConnection()->createCommand($query)->execute();
	}

	/**
	 * Create all tables, keys and constraints
	 */
	protected function createTables()
	{
		$this->createTable('address', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'address1' => 'varchar(255) CHARACTER SET utf8 DEFAULT NULL',
				'address2' => 'varchar(255) CHARACTER SET utf8 DEFAULT NULL',
				'city' => 'varchar(255) CHARACTER SET utf8 DEFAULT NULL',
				'postcode' => 'varchar(10) COLLATE utf8_bin DEFAULT NULL',
				'county' => 'varchar(255) CHARACTER SET utf8 DEFAULT NULL',
				'country_id' => 'int(10) unsigned NOT NULL',
				'email' => 'varchar(255) CHARACTER SET utf8 DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'parent_class' => 'varchar(40) COLLATE utf8_bin NOT NULL',
				'parent_id' => 'int(10) unsigned NOT NULL',
				'type' => 'char(1) COLLATE utf8_bin NOT NULL',
				'date_start' => 'datetime DEFAULT NULL',
				'date_end' => 'datetime DEFAULT NULL',
				'PRIMARY KEY (`id`)',
				'KEY `address_country_id_fk` (`country_id`)',
				'KEY `address_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `address_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `address_country_id_fk` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`)',
				'CONSTRAINT `address_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `address_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('authassignment', array(
				'itemname' => 'varchar(64) COLLATE utf8_bin NOT NULL DEFAULT \'\'',
				'userid' => 'varchar(64) COLLATE utf8_bin NOT NULL DEFAULT \'\'',
				'bizrule' => 'text COLLATE utf8_bin',
				'data' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`itemname`,`userid`)',
				'KEY `authassignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `authassignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `authassignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `authassignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('authitem', array(
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'type' => 'int(11) NOT NULL',
				'description' => 'text COLLATE utf8_bin',
				'bizrule' => 'text COLLATE utf8_bin',
				'data' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`name`)',
				'KEY `authitem_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `authitem_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `authitem_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `authitem_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('authitemchild', array(
				'parent' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'child' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`parent`,`child`)',
				'KEY `child` (`child`)',
				'KEY `authitemchild_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `authitemchild_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `authitemchild_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `authitemchild_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('booking', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_operation_id' => 'int(10) unsigned NOT NULL',
				'session_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'int(10) NOT NULL',
				'ward_id' => 'int(11) unsigned DEFAULT \'0\'',
				'admission_time' => 'time NOT NULL',
				'confirmed' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_operation_id` (`element_operation_id`)',
				'KEY `session_id` (`session_id`)',
				'KEY `booking_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `booking_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `appointment_1` FOREIGN KEY (`element_operation_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `appointment_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`)',
				'CONSTRAINT `booking_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `booking_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('cancellation_reason', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'text' => 'varchar(255) COLLATE utf8_bin NOT NULL DEFAULT \'\'',
				'parent_id' => 'int(10) unsigned DEFAULT NULL',
				'list_no' => 'tinyint(2) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `cancellation_reason_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `cancellation_reason_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `cancellation_reason_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `cancellation_reason_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('cancelled_booking', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_operation_id' => 'int(10) unsigned NOT NULL',
				'date' => 'date NOT NULL',
				'start_time' => 'time NOT NULL',
				'end_time' => 'time NOT NULL',
				'theatre_id' => 'int(10) unsigned NOT NULL',
				'cancelled_date' => 'datetime DEFAULT NULL',
				'created_user_id' => 'int(10) unsigned NOT NULL',
				'cancelled_reason_id' => 'int(10) unsigned NOT NULL',
				'cancellation_comment' => 'varchar(200) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_operation_id` (`element_operation_id`)',
				'KEY `cancelled_reason_id` (`cancelled_reason_id`)',
				'KEY `cancelled_booking_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `cancelled_booking_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `booking_1` FOREIGN KEY (`cancelled_reason_id`) REFERENCES `cancellation_reason` (`id`)',
				'CONSTRAINT `cancelled_booking_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `cancelled_booking_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('cancelled_operation', array(
				'id' => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
				'element_operation_id' => 'int(10) unsigned NOT NULL',
				'cancelled_date' => 'datetime DEFAULT NULL',
				'created_user_id' => 'int(10) unsigned NOT NULL',
				'cancelled_reason_id' => 'int(10) unsigned NOT NULL',
				'cancellation_comment' => 'varchar(200) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `cancelled_reason_id` (`cancelled_reason_id`)',
				'KEY `operation_2` (`element_operation_id`)',
				'KEY `cancelled_operation_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `cancelled_operation_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `cancelled_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `cancelled_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `operation_1` FOREIGN KEY (`cancelled_reason_id`) REFERENCES `cancellation_reason` (`id`)',
				'CONSTRAINT `operation_2` FOREIGN KEY (`element_operation_id`) REFERENCES `element_operation` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('common_ophthalmic_disorder', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'disorder_id' => 'int(10) unsigned NOT NULL',
				'specialty_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `disorder_id` (`disorder_id`)',
				'KEY `specialty_id` (`specialty_id`)',
				'KEY `common_ophthalmic_disorder_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `common_ophthalmic_disorder_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `common_ophthalmic_disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `common_ophthalmic_disorder_ibfk_1` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`)',
				'CONSTRAINT `common_ophthalmic_disorder_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)',
				'CONSTRAINT `common_ophthalmic_disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createTable('common_systemic_disorder', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'disorder_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `disorder_id` (`disorder_id`)',
				'KEY `common_systemic_disorder_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `common_systemic_disorder_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `common_systemic_disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `common_systemic_disorder_ibfk_1` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`)',
				'CONSTRAINT `common_systemic_disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createTable('consultant', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'obj_prof' => 'varchar(20) COLLATE utf8_bin NOT NULL',
				'nat_id' => 'varchar(20) COLLATE utf8_bin NOT NULL',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'pas_code' => 'char(4) COLLATE utf8_bin DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `consultant_contact_id_fk_1` (`contact_id`)',
				'KEY `consultant_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `consultant_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `consultant_contact_id_fk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
				'CONSTRAINT `consultant_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `consultant_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('contact', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'nick_name' => 'varchar(80) COLLATE utf8_bin DEFAULT NULL',
				'primary_phone' => 'varchar(20) COLLATE utf8_bin DEFAULT NULL',
				'title' => 'varchar(20) COLLATE utf8_bin NOT NULL',
				'first_name' => 'varchar(100) COLLATE utf8_bin NOT NULL',
				'last_name' => 'varchar(100) COLLATE utf8_bin NOT NULL',
				'qualifications' => 'varchar(200) COLLATE utf8_bin DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `contact_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `contact_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `contact_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `contact_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('contact_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
				'letter_template_only' => 'tinyint(4) NOT NULL DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `name` (`name`)',
				'KEY `contact_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `contact_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `contact_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `contact_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('country', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'code' => 'char(2) COLLATE utf8_bin DEFAULT NULL',
				'name' => 'varchar(50) COLLATE utf8_bin DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `code` (`code`)',
				'UNIQUE KEY `name` (`name`)',
				'KEY `country_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `country_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `country_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `country_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('date_letter_sent', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_operation_id' => 'int(10) unsigned NOT NULL',
				'date_invitation_letter_sent' => 'datetime DEFAULT NULL',
				'date_1st_reminder_letter_sent' => 'datetime DEFAULT NULL',
				'date_2nd_reminder_letter_sent' => 'datetime DEFAULT NULL',
				'date_gp_letter_sent' => 'datetime DEFAULT NULL',
				'date_scheduling_letter_sent' => 'datetime DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_operation_id` (`element_operation_id`)',
				'KEY `date_letter_sent_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `date_letter_sent_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `date_letter_sent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `date_letter_sent_element_operation_fk` FOREIGN KEY (`element_operation_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `date_letter_sent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('disorder', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'fully_specified_name' => 'char(255) CHARACTER SET utf8 NOT NULL',
				'term' => 'char(255) CHARACTER SET utf8 NOT NULL',
				'systemic' => 'tinyint(1) unsigned DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `term` (`term`)',
				'KEY `disorder_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `disorder_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_allergies', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'value' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_allergies_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_allergies_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_allergies_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_allergies_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_allergies_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_anterior_segment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'description_left' => 'text COLLATE utf8_bin',
				'description_right' => 'text COLLATE utf8_bin',
				'image_string_left' => 'text COLLATE utf8_bin',
				'image_string_right' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_anterior_segment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_anterior_segment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_anterior_segment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_anterior_segment_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_anterior_segment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_conclusion', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'value' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_conclusion_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_conclusion_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_conclusion_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_conclusion_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_conclusion_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_cranial_nerves', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_cranial_nerves_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_cranial_nerves_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_cranial_nerves_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_cranial_nerves_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_cranial_nerves_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_diabetes_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'type' => 'tinyint(1) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_diabetes_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_diabetes_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_diabetes_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_diabetes_type_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_diabetes_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_diagnosis', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'disorder_id' => 'int(10) unsigned NOT NULL',
				'eye' => 'tinyint(1) unsigned DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `event_id` (`event_id`)',
				'KEY `disorder_id` (`disorder_id`)',
				'KEY `element_diagnosis_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_diagnosis_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_diagnosis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_diagnosis_fk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_diagnosis_fk_2` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`)',
				'CONSTRAINT `element_diagnosis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_extraocular_movements', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_extraocular_movements_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_extraocular_movements_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_extraocular_movements_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_extraocular_movements_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_extraocular_movements_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_foh', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'value' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_foh_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_foh_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_foh_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_foh_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_foh_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_gonioscopy', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_gonioscopy_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_gonioscopy_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_gonioscopy_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_gonioscopy_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_gonioscopy_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_history', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'description' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_history_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_history_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_history_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_history_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_hpc', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'value' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_hpc_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_hpc_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_hpc_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_hpc_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_hpc_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_intraocular_pressure', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'right_iop' => 'tinyint(4) DEFAULT NULL',
				'left_iop' => 'tinyint(4) DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_intraocular_pressure_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_intraocular_pressure_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_intraocular_pressure_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_intraocular_pressure_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_intraocular_pressure_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_letterout', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'from_address' => 'text COLLATE utf8_bin',
				'date' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				'dear' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				're' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				'value' => 'text COLLATE utf8_bin',
				'to_address' => 'text COLLATE utf8_bin',
				'cc' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_letterout_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_letterout_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_letterout_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_letterout_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_medication', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'value' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_medication_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_medication_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_medication_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_medication_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_medication_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_mini_refraction', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_mini_refraction_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_mini_refraction_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_mini_refraction_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_mini_refraction_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_mini_refraction_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_nsc_grade', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'retinopathy_grade_id' => 'int(10) unsigned NOT NULL',
				'maculopathy_grade_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_nsc_grade_retinopathy_grade_id_fk` (`retinopathy_grade_id`)',
				'KEY `element_nsc_grade_maculopathy_grade_id_fk` (`maculopathy_grade_id`)',
				'KEY `element_nsc_grade_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_nsc_grade_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_nsc_grade_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_nsc_grade_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_nsc_grade_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_nsc_grade_maculopathy_grade_id_fk` FOREIGN KEY (`maculopathy_grade_id`) REFERENCES `nsc_grade` (`id`)',
				'CONSTRAINT `element_nsc_grade_retinopathy_grade_id_fk` FOREIGN KEY (`retinopathy_grade_id`) REFERENCES `nsc_grade` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_operation', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'eye' => 'tinyint(1) unsigned DEFAULT \'0\'',
				'comments' => 'text COLLATE utf8_bin',
				'total_duration' => 'smallint(5) unsigned NOT NULL',
				'consultant_required' => 'tinyint(1) unsigned DEFAULT \'0\'',
				'anaesthetist_required' => 'tinyint(1) unsigned DEFAULT \'0\'',
				'anaesthetic_type' => 'tinyint(1) unsigned DEFAULT \'0\'',
				'overnight_stay' => 'tinyint(1) unsigned DEFAULT \'0\'',
				'schedule_timeframe' => 'tinyint(1) unsigned DEFAULT \'0\'',
				'status' => 'int(10) unsigned NOT NULL',
				'decision_date' => 'date NOT NULL',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'urgent' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'site_id' => 'int(10) unsigned NOT NULL DEFAULT \'0\'',
				'PRIMARY KEY (`id`)',
				'KEY `event_id` (`event_id`)',
				'KEY `element_operation_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_operation_created_user_id_fk` (`created_user_id`)',
				'KEY `element_operation_site_id_fk` (`site_id`)',
				'CONSTRAINT `element_operation_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `element_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_operation_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_orbital_examination', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_orbital_examination_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_orbital_examination_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_orbital_examination_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_orbital_examination_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_orbital_examination_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_past_history', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_past_history_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_past_history_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_past_history_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_past_history_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_past_history_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_pmh', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'value' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_pmh_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_pmh_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_pmh_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_pmh_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_pmh_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_poh', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'value' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_poh_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_poh_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_poh_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_poh_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_poh_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_posterior_segment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'description_left' => 'text COLLATE utf8_bin',
				'description_right' => 'text COLLATE utf8_bin',
				'image_string_left' => 'text COLLATE utf8_bin',
				'image_string_right' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_posterior_segment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_posterior_segment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_posterior_segment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_posterior_segment_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_posterior_segment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_referred_from_screening', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'referred' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_referred_from_screening_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_referred_from_screening_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_referred_from_screening_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_referred_from_screening_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_referred_from_screening_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_registered_blind', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'status' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_registered_blind_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_registered_blind_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_registered_blind_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_registered_blind_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_registered_blind_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_social_history', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'value' => 'text COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_social_history_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_social_history_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_social_history_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_social_history_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_social_history_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'class_name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `name` (`name`)',
				'KEY `element_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_visual_acuity', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'rva_ua' => 'tinyint(4) DEFAULT \'0\'',
				'lva_ua' => 'tinyint(4) DEFAULT \'0\'',
				'rva_cr' => 'tinyint(4) DEFAULT \'0\'',
				'lva_cr' => 'tinyint(4) DEFAULT \'0\'',
				'rva_ph' => 'tinyint(4) DEFAULT \'0\'',
				'lva_ph' => 'tinyint(4) DEFAULT \'0\'',
				'aid' => 'tinyint(1) DEFAULT \'0\'',
				'format' => 'tinyint(1) DEFAULT \'0\'',
				'distance' => 'int(11) DEFAULT \'0\'',
				'type' => 'tinyint(1) DEFAULT \'0\'',
				'right_aid' => 'tinyint(1) unsigned DEFAULT NULL',
				'left_aid' => 'tinyint(1) unsigned DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_visual_acuity_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_visual_acuity_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_visual_acuity_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_visual_acuity_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_visual_acuity_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_visual_fields', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_visual_fields_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_visual_fields_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_visual_fields_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_visual_fields_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_visual_fields_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('element_visual_function', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `event_id` (`event_id`)',
				'KEY `element_visual_function_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_visual_function_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `element_visual_function_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_visual_function_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `element_visual_function_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('episode', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'firm_id' => 'int(10) unsigned DEFAULT NULL',
				'start_date' => 'datetime NOT NULL',
				'end_date' => 'datetime DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `episode_1` (`patient_id`)',
				'KEY `episode_2` (`firm_id`)',
				'KEY `episode_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `episode_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `episode_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `episode_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `episode_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `episode_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('event', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'episode_id' => 'int(10) unsigned NOT NULL',
				'created_user_id' => 'int(10) unsigned NOT NULL',
				'event_type_id' => 'int(10) unsigned NOT NULL',
				'datetime' => 'datetime NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `event_1` (`episode_id`)',
				'KEY `event_2` (`created_user_id`)',
				'KEY `event_3` (`event_type_id`)',
				'KEY `event_last_modified_user_id_fk` (`last_modified_user_id`)',
				'CONSTRAINT `event_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`)',
				'CONSTRAINT `event_3` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`)',
				'CONSTRAINT `event_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `event_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createTable('event_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
				'first_in_episode_possible' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `name` (`name`)',
				'KEY `event_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `event_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `event_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `event_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('firm', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'service_specialty_assignment_id' => 'int(10) unsigned NOT NULL',
				'pas_code' => 'char(4) COLLATE utf8_bin DEFAULT NULL',
				'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `service_specialty_assignment_id` (`service_specialty_assignment_id`)',
				'KEY `firm_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `firm_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `service_specialty_assignment_id` FOREIGN KEY (`service_specialty_assignment_id`) REFERENCES `service_specialty_assignment` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('firm_user_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'user_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `firm_id` (`firm_id`)',
				'KEY `user_id` (`user_id`)',
				'KEY `firm_user_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `firm_user_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `firm_id` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `firm_user_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `firm_user_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createTable('gp', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'obj_prof' => 'varchar(20) COLLATE utf8_bin NOT NULL',
				'nat_id' => 'varchar(20) COLLATE utf8_bin NOT NULL',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `gp_contact_id_fk_1` (`contact_id`)',
				'KEY `gp_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `gp_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `gp_contact_id_fk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
				'CONSTRAINT `gp_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `gp_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('letter_template', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'specialty_id' => 'int(10) unsigned NOT NULL',
				'name' => 'varchar(64) COLLATE utf8_bin DEFAULT NULL',
				'cc' => 'int(10) unsigned NOT NULL',
				'phrase' => 'text COLLATE utf8_bin NOT NULL',
				'send_to' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `specialty_id` (`specialty_id`)',
				'KEY `letter_template_ibfk_3` (`cc`)',
				'KEY `letter_template_ibfk_2` (`send_to`)',
				'KEY `letter_template_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `letter_template_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `letter_template_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `letter_template_ibfk_1` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)',
				'CONSTRAINT `letter_template_ibfk_2` FOREIGN KEY (`send_to`) REFERENCES `contact_type` (`id`)',
				'CONSTRAINT `letter_template_ibfk_3` FOREIGN KEY (`cc`) REFERENCES `contact_type` (`id`)',
				'CONSTRAINT `letter_template_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('manual_contact', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'contact_type_id' => 'int(10) unsigned NOT NULL',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `manual_contact_contact_id_fk_1` (`contact_id`)',
				'KEY `manual_contact_contact_type_id_fk_2` (`contact_type_id`)',
				'KEY `manual_contact_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `manual_contact_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `manual_contact_contact_id_fk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
				'CONSTRAINT `manual_contact_contact_type_id_fk_2` FOREIGN KEY (`contact_type_id`) REFERENCES `contact_type` (`id`)',
				'CONSTRAINT `manual_contact_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `manual_contact_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('nsc_grade', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'char(3) COLLATE utf8_bin NOT NULL',
				'type' => 'tinyint(1) DEFAULT \'0\'',
				'medical_phrase' => 'varchar(5000) COLLATE utf8_bin NOT NULL',
				'layman_phrase' => 'varchar(1000) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `name` (`name`)',
				'KEY `nsc_grade_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `nsc_grade_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `nsc_grade_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `nsc_grade_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('opcs_code', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
				'description' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `opcs_code_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `opcs_code_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `opcs_code_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `opcs_code_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('operation_procedure_assignment', array(
				'operation_id' => 'int(10) unsigned NOT NULL',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`operation_id`,`proc_id`)',
				'KEY `operation_id` (`operation_id`)',
				'KEY `procedure_id` (`proc_id`)',
				'KEY `operation_procedure_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `operation_procedure_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `operation_fk` FOREIGN KEY (`operation_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `operation_procedure_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `operation_procedure_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `operation_procedure_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('patient', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'pas_key' => 'int(10) unsigned DEFAULT NULL',
				'title' => 'varchar(8) COLLATE utf8_bin DEFAULT NULL',
				'first_name' => 'varchar(40) CHARACTER SET utf8 NOT NULL',
				'last_name' => 'varchar(40) CHARACTER SET utf8 NOT NULL',
				'dob' => 'date DEFAULT NULL',
				'gender' => 'char(1) CHARACTER SET utf8 DEFAULT NULL',
				'hos_num' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
				'nhs_num' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
				'primary_phone' => 'varchar(20) COLLATE utf8_bin DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'gp_id' => 'int(10) unsigned DEFAULT NULL',
				'PRIMARY KEY (`id`)',
				'KEY `patient_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `patient_created_user_id_fk` (`created_user_id`)',
				'KEY `patient_gp_id_fk` (`gp_id`)',
				'CONSTRAINT `patient_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `patient_gp_id_fk` FOREIGN KEY (`gp_id`) REFERENCES `gp` (`id`)',
				'CONSTRAINT `patient_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('patient_contact_assignment', array(
				'patient_id' => 'int(10) unsigned NOT NULL',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`patient_id`,`contact_id`)',
				'KEY `patient_id` (`patient_id`)',
				'KEY `contact_id` (`contact_id`)',
				'KEY `patient_contact_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `patient_contact_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `patient_contact_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `patient_contact_assignment_fk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `patient_contact_assignment_fk_2` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
				'CONSTRAINT `patient_contact_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('phrase', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'phrase' => 'text COLLATE utf8_bin',
				'section_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'int(10) unsigned DEFAULT NULL',
				'phrase_name_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `phrase_phrase_name_id_fk` (`phrase_name_id`)',
				'KEY `phrase_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `phrase_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `phrase_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `phrase_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `phrase_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('phrase_by_firm', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'phrase' => 'text COLLATE utf8_bin',
				'section_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'int(10) unsigned DEFAULT NULL',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'phrase_name_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `phrase_by_firm_section_fk` (`section_id`)',
				'KEY `phrase_by_firm_firm_fk` (`firm_id`)',
				'KEY `phrase_by_firm_phrase_name_id_fk` (`phrase_name_id`)',
				'KEY `phrase_by_firm_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `phrase_by_firm_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `phrase_by_firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `phrase_by_firm_firm_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `phrase_by_firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `phrase_by_firm_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`)',
				'CONSTRAINT `phrase_by_firm_section_fk` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('phrase_by_specialty', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'phrase' => 'text COLLATE utf8_bin',
				'section_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'int(10) unsigned DEFAULT NULL',
				'specialty_id' => 'int(10) unsigned NOT NULL',
				'phrase_name_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `phrase_by_specialty_section_fk` (`section_id`)',
				'KEY `phrase_by_specialty_specialty_fk` (`specialty_id`)',
				'KEY `phrase_by_specialty_phrase_name_id_fk` (`phrase_name_id`)',
				'KEY `phrase_by_specialty_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `phrase_by_specialty_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `phrase_by_specialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `phrase_by_specialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `phrase_by_specialty_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`)',
				'CONSTRAINT `phrase_by_specialty_section_fk` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`)',
				'CONSTRAINT `phrase_by_specialty_specialty_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('phrase_name', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `phrase_name_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `phrase_name_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `phrase_name_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `phrase_name_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('possible_element_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_type_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'num_views' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'display_order' => 'int(10) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `event_type_id` (`event_type_id`)',
				'KEY `element_type_id` (`element_type_id`)',
				'KEY `possible_element_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `possible_element_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `possible_element_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `possible_element_type_ibfk_1` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`)',
				'CONSTRAINT `possible_element_type_ibfk_2` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `possible_element_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createTable('proc', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'term' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
				'short_format' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
				'default_duration' => 'smallint(5) unsigned NOT NULL',
				'snomed_code' => 'int(10) unsigned NOT NULL DEFAULT \'0\'',
				'snomed_term' => 'varchar(255) COLLATE utf8_bin NOT NULL DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `proc_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `proc_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `proc_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `proc_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('proc_opcs_assignment', array(
				'proc_id' => 'int(10) unsigned NOT NULL',
				'opcs_code_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`proc_id`,`opcs_code_id`)',
				'KEY `opcs_code_id` (`opcs_code_id`)',
				'KEY `procedure_id` (`proc_id`)',
				'KEY `proc_opcs_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `proc_opcs_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `opcs_code_fk` FOREIGN KEY (`opcs_code_id`) REFERENCES `opcs_code` (`id`)',
				'CONSTRAINT `proc_opcs_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `proc_opcs_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `proc_opcs_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('proc_specialty_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'specialty_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `proc_id` (`proc_id`)',
				'KEY `specialty_id` (`specialty_id`)',
				'KEY `proc_specialty_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `proc_specialty_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `proc_specialty_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `proc_specialty_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `proc_specialty_assignment_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)',
				'CONSTRAINT `proc_specialty_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('proc_specialty_subsection_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'specialty_subsection_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `proc_id` (`proc_id`)',
				'KEY `specialty_subsection_id` (`specialty_subsection_id`)',
				'KEY `proc_specialty_subsection_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `proc_specialty_subsection_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `proc_specialty_subsection_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `proc_specialty_subsection_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `proc_specialty_subsection_assignment_ibfk_2` FOREIGN KEY (`specialty_subsection_id`) REFERENCES `specialty_subsection` (`id`)',
				'CONSTRAINT `proc_specialty_subsection_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('referral', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'refno' => 'int(10) unsigned NOT NULL',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'closed' => 'tinyint(1) DEFAULT \'0\'',
				'service_specialty_assignment_id' => 'int(10) unsigned NOT NULL',
				'firm_id' => 'int(10) unsigned DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `referral_ibfk_1` (`service_specialty_assignment_id`)',
				'KEY `firm_fk` (`firm_id`)',
				'KEY `referral_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `referral_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `firm_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `referral_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `referral_ibfk_1` FOREIGN KEY (`service_specialty_assignment_id`) REFERENCES `service_specialty_assignment` (`id`)',
				'CONSTRAINT `referral_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('referral_episode_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'referral_id' => 'int(10) unsigned NOT NULL',
				'episode_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `referral_episode_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `referral_episode_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `referral_episode_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `referral_episode_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('section', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				'section_type_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `section_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `section_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `section_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `section_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('section_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `section_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `section_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `section_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `section_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('sequence', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'theatre_id' => 'int(10) unsigned NOT NULL',
				'start_date' => 'date NOT NULL',
				'start_time' => 'time NOT NULL',
				'end_time' => 'time NOT NULL',
				'end_date' => 'date DEFAULT NULL',
				'repeat_interval' => 'tinyint(1) unsigned NOT NULL',
				'weekday' => 'tinyint(1) DEFAULT NULL',
				'week_selection' => 'tinyint(1) DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'consultant' => 'tinyint(1) unsigned NOT NULL DEFAULT \'1\'',
				'paediatric' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'anaesthetist' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'general_anaesthetic' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'PRIMARY KEY (`id`)',
				'KEY `theatre_id` (`theatre_id`)',
				'KEY `sequence_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `sequence_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `sequence_1` FOREIGN KEY (`theatre_id`) REFERENCES `theatre` (`id`)',
				'CONSTRAINT `sequence_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `sequence_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('sequence_firm_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'sequence_id' => 'int(10) unsigned NOT NULL',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `firm_id` (`firm_id`)',
				'KEY `sequence_firm_assignment_1` (`sequence_id`)',
				'KEY `sequence_firm_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `sequence_firm_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `sequence_firm_assignment_1` FOREIGN KEY (`sequence_id`) REFERENCES `sequence` (`id`)',
				'CONSTRAINT `sequence_firm_assignment_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `sequence_firm_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `sequence_firm_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('service', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `service_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `service_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `service_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `service_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('service_specialty_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'service_id' => 'int(10) unsigned NOT NULL',
				'specialty_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `service_id` (`service_id`)',
				'KEY `specialty_id` (`specialty_id`)',
				'KEY `service_specialty_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `service_specialty_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `service_specialty_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `service_specialty_assignment_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`)',
				'CONSTRAINT `service_specialty_assignment_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)',
				'CONSTRAINT `service_specialty_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createTable('session', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'sequence_id' => 'int(10) unsigned NOT NULL',
				'date' => 'date NOT NULL',
				'start_time' => 'time NOT NULL',
				'end_time' => 'time NOT NULL',
				'comments' => 'text COLLATE utf8_bin',
				'status' => 'int(10) unsigned NOT NULL DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'consultant' => 'tinyint(1) unsigned NOT NULL DEFAULT \'1\'',
				'paediatric' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'anaesthetist' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'general_anaesthetic' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `session_idx1` (`sequence_id`,`date`,`start_time`,`end_time`)',
				'KEY `sequence_id` (`sequence_id`)',
				'KEY `session_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `session_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `session_1` FOREIGN KEY (`sequence_id`) REFERENCES `sequence` (`id`)',
				'CONSTRAINT `session_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `session_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('site', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'code' => 'char(2) COLLATE utf8_bin NOT NULL',
				'short_name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'address1' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'address2' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'address3' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'postcode' => 'varchar(10) COLLATE utf8_bin NOT NULL',
				'fax' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'telephone' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `site_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `site_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('site_element_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'possible_element_type_id' => 'int(10) unsigned NOT NULL',
				'specialty_id' => 'int(10) unsigned NOT NULL',
				'view_number' => 'int(10) unsigned NOT NULL',
				'required' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'first_in_episode' => 'tinyint(1) unsigned DEFAULT \'1\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `possible_element_type_id` (`possible_element_type_id`)',
				'KEY `specialty_id` (`specialty_id`)',
				'KEY `site_element_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `site_element_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `site_element_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_element_type_ibfk_1` FOREIGN KEY (`possible_element_type_id`) REFERENCES `possible_element_type` (`id`)',
				'CONSTRAINT `site_element_type_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)',
				'CONSTRAINT `site_element_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createTable('specialty', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
				'ref_spec' => 'char(3) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `specialty_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `specialty_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `specialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `specialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('specialty_subsection', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'specialty_id' => 'int(10) unsigned NOT NULL',
				'name' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `service_id` (`specialty_id`)',
				'KEY `specialty_subsection_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `specialty_subsection_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `specialty_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)',
				'CONSTRAINT `specialty_subsection_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `specialty_subsection_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('tbl_audit_trail', array(
				'id' => 'int(11) NOT NULL AUTO_INCREMENT',
				'old_value' => 'text',
				'new_value' => 'text',
				'action' => 'varchar(255) NOT NULL',
				'model' => 'varchar(255) NOT NULL',
				'field' => 'varchar(255) NOT NULL',
				'stamp' => 'datetime NOT NULL',
				'user_id' => 'int(10) DEFAULT NULL',
				'model_id' => 'int(10) NOT NULL',
				'PRIMARY KEY (`id`)',
				'KEY `idx_audit_trail_user_id` (`user_id`)',
				'KEY `idx_audit_trail_model_id` (`model_id`)',
				'KEY `idx_audit_trail_model` (`model`)',
				'KEY `idx_audit_trail_field` (`field`)',
				'KEY `idx_audit_trail_action` (`action`)'
			), 'ENGINE=MyISAM DEFAULT CHARSET=latin1'
		);
		$this->createTable('theatre', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				'site_id' => 'int(10) unsigned NOT NULL',
				'code' => 'varchar(4) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_id` (`site_id`)',
				'KEY `theatre_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `theatre_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `theatre_1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `theatre_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `theatre_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('theatre_ward_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'theatre_id' => 'int(10) unsigned NOT NULL',
				'ward_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `theatre_id` (`theatre_id`)',
				'KEY `ward_id` (`ward_id`)',
				'KEY `theatre_ward_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `theatre_ward_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `theatre_ward_assignment_1` FOREIGN KEY (`theatre_id`) REFERENCES `theatre` (`id`)',
				'CONSTRAINT `theatre_ward_assignment_2` FOREIGN KEY (`ward_id`) REFERENCES `ward` (`id`)',
				'CONSTRAINT `theatre_ward_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `theatre_ward_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
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
				'KEY `transport_list_last_modified_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `transport_list_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `transport_list_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('user', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'username' => 'varchar(40) CHARACTER SET utf8 NOT NULL',
				'first_name' => 'varchar(40) CHARACTER SET utf8 NOT NULL',
				'last_name' => 'varchar(40) CHARACTER SET utf8 NOT NULL',
				'email' => 'varchar(80) CHARACTER SET utf8 NOT NULL',
				'active' => 'tinyint(1) NOT NULL',
				'global_firm_rights' => 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'',
				'title' => 'varchar(40) COLLATE utf8_bin NOT NULL',
				'qualifications' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'role' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'code' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				'password' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
				'salt' => 'varchar(10) COLLATE utf8_bin DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_firm_id' => 'int(11) unsigned DEFAULT NULL',
				'PRIMARY KEY (`id`)',
				'KEY `user_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `user_created_user_id_fk` (`created_user_id`)',
				'KEY `user_last_firm_id_fk` (`last_firm_id`)',
				'CONSTRAINT `user_last_firm_id_fk` FOREIGN KEY (`last_firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `user_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('user_contact_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'user_id' => 'int(10) unsigned NOT NULL',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `user_id` (`user_id`)',
				'UNIQUE KEY `contact_id` (`contact_id`)',
				'KEY `user_contact_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `user_contact_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `user_contact_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_contact_assignment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_contact_assignment_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
				'CONSTRAINT `user_contact_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createTable('user_firm_rights', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'user_id' => 'int(10) unsigned NOT NULL',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `user_firm_rights_fk_1` (`user_id`)',
				'KEY `user_firm_rights_fk_2` (`firm_id`)',
				'KEY `user_firm_rights_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `user_firm_rights_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `user_firm_rights_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_firm_rights_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_firm_rights_fk_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `user_firm_rights_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('user_service_rights', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'user_id' => 'int(10) unsigned NOT NULL',
				'service_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `user_service_rights_fk_1` (`user_id`)',
				'KEY `user_service_rights_fk_2` (`service_id`)',
				'KEY `user_service_rights_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `user_service_rights_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `user_service_rights_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_service_rights_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_service_rights_fk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`)',
				'CONSTRAINT `user_service_rights_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('user_session', array(
				'id' => 'char(32) NOT NULL',
				'expire' => 'int(11) DEFAULT NULL',
				'data' => 'text',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `user_session_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `user_session_created_user_id_fk` (`created_user_id`)'
			), 'ENGINE=MyISAM DEFAULT CHARSET=latin1'
		);
		$this->createTable('ward', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'restriction' => 'tinyint(1) DEFAULT NULL',
				'code' => 'varchar(10) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_id` (`site_id`)',
				'KEY `ward_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `ward_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `ward_1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `ward_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ward_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		echo "m120223_000000_consolidation does not support migration down.\n";
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

}
