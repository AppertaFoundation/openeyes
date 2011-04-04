<?php

class m110328_150704_create_element_referred_from_screening extends CDbMigration
{
	public function up()
	{
		$this->createTable('element_referred_from_screening', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'referred' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('element_referred_from_screening');
	}
}
