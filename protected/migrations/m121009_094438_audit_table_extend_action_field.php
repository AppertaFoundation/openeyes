<?php

class m121009_094438_audit_table_extend_action_field extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('audit','action',"varchar(32) COLLATE utf8_bin NOT NULL DEFAULT ''");
	}

	public function down()
	{
		$this->alterColumn('audit','action',"varchar(20) COLLATE utf8_bin NOT NULL DEFAULT ''");
	}
}
