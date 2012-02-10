<?php

class m120209_165900_create_audit_trail_table extends CDbMigration {

	public function up() {
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