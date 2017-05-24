<?php

class m140729_092047_event_version_table_parent_id extends OEMigration
{
	public function up()
	{
		$this->addColumn('event_version','parent_id','int(10) unsigned NULL');
		$this->addColumn('event_type_version','parent_id','int(10) unsigned NULL');
	}

	public function down()
	{
		$this->dropColumn('event_version','parent_id');
		$this->dropColumn('event_type_version','parent_id');
	}
}