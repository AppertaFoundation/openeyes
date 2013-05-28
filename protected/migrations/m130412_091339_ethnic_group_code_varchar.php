<?php

class m130412_091339_ethnic_group_code_varchar extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('ethnic_group','code','varchar(1) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('ethnic_group','code','char(1) COLLATE utf8_bin NOT NULL');
	}
}
