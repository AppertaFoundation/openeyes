<?php

class m130306_105300_add_ethnic_group_to_patient extends OEMigration {
	public function up() {
		$this->createTable('ethnic_group', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) NOT NULL',
				'code' => 'char(1) NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ethnic_group_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `ethnic_group_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `ethnic_group_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ethnic_group_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
                $migrations_path = dirname(__FILE__);
                $this->initialiseData($migrations_path);
		$this->addColumn('patient','ethnic_group_id','int(10) unsigned');
		$this->addForeignKey('patient_ethnic_group_id_fk', 'patient', 'ethnic_group_id', 'ethnic_group', 'id');
	}

	public function down() {
		$this->dropForeignKey('patient_ethnic_group_id_fk', 'patient');
		$this->dropColumn('patient','ethnic_group_id');
		$this->dropTable('ethnic_group');
	}
	
}
