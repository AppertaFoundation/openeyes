<?php

class m111013_163653_add_status_to_sessions extends CDbMigration
{
	public function up()
	{
		$this->addColumn('session', 'status', 'int(10) unsigned NOT NULL DEFAULT "0"');
	}

	public function down()
	{
		$this->dropColumn('session', 'status');
	}
}