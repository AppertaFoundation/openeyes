<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class m110314_165211_create_audit_trail_table extends CDbMigration {

	public function up() {
		// Check that table not already created by earlier migration before rename
		$exists = $this->getDbConnection()->createCommand("SHOW TABLES LIKE 'tbl_audit_trail'")->execute();
		if(!$exists) {
			$this->createTable('tbl_audit_trail',
				array(
					'id' => 'pk',
					'old_value' => 'text',
					'new_value' => 'text',
					'action' => 'varchar(255) NOT NULL',
					'model' => 'varchar(255) NOT NULL',
					'field' => 'varchar(255) NOT NULL',
					'stamp' => 'datetime NOT NULL',
					'user_id' => 'int(10)',
					'model_id' => 'int(10) NOT NULL',
				)
			);
			$this->createIndex('idx_audit_trail_user_id', 'tbl_audit_trail', 'user_id');
			$this->createIndex('idx_audit_trail_model_id', 'tbl_audit_trail', 'model_id');
			$this->createIndex('idx_audit_trail_model', 'tbl_audit_trail', 'model');
			$this->createIndex('idx_audit_trail_field', 'tbl_audit_trail', 'field');
			$this->createIndex('idx_audit_trail_action', 'tbl_audit_trail', 'action');
		}
	}

	public function down() {
		$this->dropTable('tbl_audit_trail');
	}

	public function safeUp() {
		$this->up();
	}

	public function safeDown() {
		$this->down();
	}
	
}
