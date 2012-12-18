<?php

class m121217_150806_add_period_lookup extends CDbMigration
{
	public function up()
	{
		// to be used initially OphCiExamination outcome element
		$this->createTable('period', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `period_lmui_fk` (`last_modified_user_id`)',
				'KEY `period_cui_fk` (`created_user_id`)',
				'CONSTRAINT `period_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `period_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->insert('period', array('name'=>'days', 'display_order' => '1'));
		$this->insert('period', array('name'=>'weeks', 'display_order' => '2'));
		$this->insert('period', array('name'=>'months', 'display_order' => '3'));
		$this->insert('period', array('name'=>'years', 'display_order' => '4'));
	}

	public function down()
	{
		$this->dropTable('period');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}