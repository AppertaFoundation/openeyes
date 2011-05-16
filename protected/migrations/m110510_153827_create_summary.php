<?php

class m110510_153827_create_summary extends CDbMigration
{
	public function up()
	{
		$this->createTable('summary', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `name` (`name`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('summary_specialty_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'summary_id' => 'int(10) unsigned NOT NULL',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'FOREIGN KEY (summary_id) REFERENCES summary(id)',
			'FOREIGN KEY (specialty_id) REFERENCES specialty(id)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('summary_specialty_assignment');
		$this->dropTable('summary');
	}
}