<?php

class m130114_152007_procedure_additional extends CDbMigration
{
	public function up()
	{
		$this->createTable('procedure_additional',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'additional_proc_id' => 'int(10) unsigned NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `procedure_additional_proc_id_fk` (`proc_id`)',
				'KEY `procedure_additional_additional_proc_id_fk` (`additional_proc_id`)',
				'KEY `procedure_additional_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `procedure_additional_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `procedure_additional_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `procedure_additional_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `procedure_additional_additional_proc_id_fk` FOREIGN KEY (`additional_proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `procedure_additional_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('procedure_additional');
	}
}
