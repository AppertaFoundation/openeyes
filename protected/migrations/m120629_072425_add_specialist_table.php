<?php

class m120629_072425_add_specialist_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('specialist',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'gmc_number' => 'varchar(7) COLLATE utf8_bin DEFAULT NULL',
			'practitioner_code' => 'varchar(8) COLLATE utf8_bin DEFAULT NULL',
			'gender' => 'char(1) CHARACTER SET utf8 DEFAULT NULL',
			'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
			'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
			'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'PRIMARY KEY (`id`)',
			'CONSTRAINT `specialist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `specialist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('institution_specialist_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'institution_id' => 'int(10) unsigned NOT NULL',
				'specialist_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `institution_specialist_assignment_institution_id_fk` (`institution_id`)',
				'KEY `institution_specialist_assignment_specialist_id_fk` (`specialist_id`)',
				'KEY `institution_specialist_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `institution_specialist_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `institution_specialist_assignment_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
				'CONSTRAINT `institution_specialist_assignment_specialist_id_fk` FOREIGN KEY (`specialist_id`) REFERENCES `specialist` (`id`)',
				'CONSTRAINT `institution_specialist_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `institution_specialist_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('institution_specialist_assignment');
		$this->dropTable('specialist');
	}
}
