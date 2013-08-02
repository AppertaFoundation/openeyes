<?php

class m121004_110700_practices extends CDbMigration
{
	public function up()
	{
		$this->createTable('practice',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'code' => 'varchar(64) NOT NULL',
				'phone' => 'varchar(64) NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `practice_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `practice_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `practice_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `practice_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->addColumn('patient', 'practice_id', 'int(10) unsigned');
		$this->addForeignKey('patient_practice_id_fk', 'patient', 'practice_id', 'practice', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('patient_practice_id_fk', 'patient');
		$this->dropColumn('patient', 'practice_id');
		$this->dropTable('practice');
	}
}
