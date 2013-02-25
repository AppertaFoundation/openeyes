<?php

class m130114_101503_procedure_complications extends CDbMigration
{
	public function up()
	{
		$this->createTable('complication',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `complication_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `complication_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `complication_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `complication_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('benefit',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `benefit_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `benefit_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `benefit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `benefit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('procedure_complication',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'complication_id' => 'int(10) unsigned NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `procedure_complication_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `procedure_complication_created_user_id_fk` (`created_user_id`)',
				'KEY `procedure_complication_proc_id_fk` (`proc_id`)',
				'KEY `procedure_complication_complication_id_fk` (`complication_id`)',
				'CONSTRAINT `procedure_complication_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `procedure_complication_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `procedure_complication_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `procedure_complication_complication_id_fk` FOREIGN KEY (`complication_id`) REFERENCES `complication` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('procedure_benefit',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'benefit_id' => 'int(10) unsigned NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `procedure_benefit_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `procedure_benefit_created_user_id_fk` (`created_user_id`)',
				'KEY `procedure_benefit_proc_id_fk` (`proc_id`)',
				'KEY `procedure_benefit_benefit_id_fk` (`benefit_id`)',
				'CONSTRAINT `procedure_benefit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `procedure_benefit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `procedure_benefit_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `procedure_benefit_benefit_id_fk` FOREIGN KEY (`benefit_id`) REFERENCES `benefit` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('procedure_benefit');
		$this->dropTable('procedure_complication');
		$this->dropTable('benefit');
		$this->dropTable('complication');
	}
}
