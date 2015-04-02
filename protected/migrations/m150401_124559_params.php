<?php

class m150401_124559_params extends CDbMigration
{
	public function up()
	{
		$this->createTable(
			'param',
			array(
				'id INT PRIMARY KEY NOT NULL AUTO_INCREMENT',
				'param_key VARCHAR(255) NOT NULL',
				'param_value VARCHAR(255) NOT NULL'
			),
			'engine=innodb charset=utf8 collate=utf8_unicode_ci'
		);
		$this->createIndex('param_key_unq', 'param', 'param_key', true);
	}

	public function down()
	{
		$this->dropTable('param');
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