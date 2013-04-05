<?php

class m130325_112131_patient_medications extends CDbMigration
{
	public function up()
	{
		$this->createTable('medication',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'medication' => 'varchar(1024) COLLATE utf8_bin NOT NULL',
				'route_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(1024) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `medication_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `medication_created_user_id_fk` (`created_user_id`)',
				'KEY `medication_patient_id_fk` (`patient_id`)',
				'KEY `medication_route_id_fk` (`route_id`)',
				'CONSTRAINT `medication_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `medication_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `medication_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `medication_route_id_fk` FOREIGN KEY (`route_id`) REFERENCES `drug_route` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('medication');
	}
}
