<?php

class m111114_162445_create_user_session_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('user_session', array(
    	'id' => 'CHAR(32) PRIMARY KEY',
			'expire' => 'integer',
			'data' => 'text'
		));
	}

	public function down()
	{
		$this->dropTable('user_session');
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
