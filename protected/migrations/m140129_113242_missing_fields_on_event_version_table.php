<?php

class m140129_113242_missing_fields_on_event_version_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event_version','delete_pending','tinyint(1) unsigned not null');
		$this->addColumn('event_version','delete_reason','varchar(4096) null');
	}

	public function down()
	{
		$this->dropColumn('event_version','delete_pending');
		$this->dropColumn('event_version','delete_reason');
	}
}
