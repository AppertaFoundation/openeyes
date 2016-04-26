<?php

class m160426_222411_unique_codes extends CDbMigration
{
	public function up()
	{
            $this->createTable('unique_codes', array(
                'id' => 'int unsigned NOT NULL AUTO_INCREMENT',
                'code' => 'varchar(6) NOT NULL',
                'status' => 'int(1) unsigned NOT NULL default 1',
                'PRIMARY KEY (`id`)',
                'UNIQUE KEY `code` (`code`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
	}

	public function down()
	{
		$this->dropTable('unique_codes');
	}

}