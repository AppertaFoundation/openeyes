<?php

class m120711_122546_repopulate_event_datetime_from_created extends CDbMigration
{
	public function up()
	{
		$this->getDbConnection()->createCommand("update event set datetime=created_date")->execute();
	}

	public function down()
	{
		echo "m120711_122546_repopulate_event_datetime_from_created does not support migration down.\n";
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
