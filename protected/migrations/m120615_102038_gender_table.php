<?php

class m120615_102038_gender_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('gender',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(16) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `gender_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `gender_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `gender_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `gender_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('gender',array('name'=>'Male'));
		$this->insert('gender',array('name'=>'Female'));
	}

	public function down()
	{
		$this->dropTable('gender');
	}
}
