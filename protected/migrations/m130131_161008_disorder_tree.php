<?php

class m130131_161008_disorder_tree extends CDbMigration
{
	public function up()
	{
		$this->createTable('disorder_tree', array(
				'id' => 'int(10) unsigned NOT NULL',
				'lft' => 'int(10) unsigned NOT NULL',
				'rght' => 'int(10) unsigned NOT NULL',
				'INDEX (id)',
				'INDEX (lft)',
				'INDEX (rght)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('disorder_tree');
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