<?php

class m120427_102856_institution_table_and_contact_mapping extends CDbMigration
{
	public function up()
	{
		$this->createTable('institution',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'code' => 'varchar(5) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `institution_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `institution_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `institution_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `institution_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('institution_consultant_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'institution_id' => 'int(10) unsigned NOT NULL',
				'consultant_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `institution_consultant_assignment_institution_id_fk` (`institution_id`)',
				'KEY `institution_consultant_assignment_consultant_id_fk` (`consultant_id`)',
				'KEY `institution_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `institution_consultant_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `institution_consultant_assignment_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
				'CONSTRAINT `institution_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`)',
				'CONSTRAINT `institution_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `institution_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('patient_consultant_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'consultant_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `patient_consultant_assignment_patient_id_fk` (`patient_id`)',
				'KEY `patient_consultant_assignment_consultant_id_fk` (`consultant_id`)',
				'KEY `patient_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `patient_consultant_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `patient_consultant_assignment_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `patient_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`)',
				'CONSTRAINT `patient_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `patient_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('patient_consultant_assignment');
		$this->dropTable('institution_consultant_assignment');
		$this->dropTable('institution');
	}
}
