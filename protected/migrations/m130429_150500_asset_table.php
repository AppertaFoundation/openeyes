<?php

class m130429_150500_asset_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('asset',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'uid' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'title' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'description' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'mimetype' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'size' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `asset_uid` (`uid`)',
				'KEY `asset_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `asset_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `asset_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `asset_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('asset');
	}

}
