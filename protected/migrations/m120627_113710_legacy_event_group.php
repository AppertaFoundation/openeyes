<?php

class m120627_113710_legacy_event_group extends CDbMigration
{
	public function up()
	{
		$this->insert('event_group', array('name' => 'Legacy data', 'code' => 'Le'));
	}

	public function down()
	{
		echo "m120627_113710_legacy_event_group does not support migration down.\n";
		return false;
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
