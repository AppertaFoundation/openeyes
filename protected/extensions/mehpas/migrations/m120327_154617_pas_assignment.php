<?php

class m120327_154617_pas_assignment extends CDbMigration {
	public function up() {

		// Create new PAS mapping table to hold foreign keys
		$this->createTable('pas_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'internal_id' => 'int(10) unsigned NOT NULL',
				'external_id' => 'int(10) unsigned NOT NULL',
				'internal_type' => 'varchar(40) NOT NULL',
				'external_type' => 'varchar(40) NOT NULL',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `internal_key` (`internal_id`, `internal_type`)',
				'UNIQUE KEY `external_key` (`external_id`, `external_type`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

	}

	public function down() {
		$this->dropTable('pas_assignment');
	}

	public function safeUp() {
		$this->up();
	}

	public function safeDown() {
		$this->down();
	}

}
