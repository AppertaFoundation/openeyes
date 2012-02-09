<?php

class m120209_165900_create_tables_audit_trail extends CDbMigration {

	/**
	 * Creates initial version of the audit trail table
	 */
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
				'user_id' => 'varchar(255)',
				'model_id' => 'varchar(255) NOT NULL',
			)
		);
		$this->createIndex('idx_audit_trail_user_id', 'tbl_audit_trail', 'user_id');
		$this->createIndex('idx_audit_trail_model_id', 'tbl_audit_trail', 'model_id');
		$this->createIndex('idx_audit_trail_model', 'tbl_audit_trail', 'model');
		$this->createIndex('idx_audit_trail_field', 'tbl_audit_trail', 'field');
		$this->createIndex('idx_audit_trail_action', 'tbl_audit_trail', 'action');
	}

	/**
	 * Drops the audit trail table
	 */
	public function down() {
		$this->dropTable('tbl_audit_trail');
	}

	/**
	 * Creates initial version of the audit trail table in a transaction-safe way.
	 * Uses $this->up to not duplicate code.
	 */
	public function safeUp() {
		$this->up();
	}

	/**
	 * Drops the audit trail table in a transaction-safe way.
	 * Uses $this->down to not duplicate code.
	 */
	public function safeDown() {
		$this->down();
	}
	
}