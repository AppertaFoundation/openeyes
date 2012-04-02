<?php

class m120402_093653_add_hidden_flag_to_event_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event','hidden','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('event','hidden');
	}
}
