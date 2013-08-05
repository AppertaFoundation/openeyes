<?php

class m130514_140800_user_firm_preference extends CDbMigration
{
	public function up()
	{
		$this->createTable('user_firm_preference',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'user_id' => 'int(10) unsigned NOT NULL',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'position' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `user_firm_preference_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `user_firm_preference_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `user_firm_preference_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_firm_preference_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('user_firm_preference');
	}

}
