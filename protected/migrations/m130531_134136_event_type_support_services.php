<?php

class m130531_134136_event_type_support_services extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event_type','support_services','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('event_type','support_services');
	}
}
