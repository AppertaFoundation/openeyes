<?php

class m110902_145020_add_ward_code extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ward', 'code', 'varchar(10) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('ward', 'code');
	}
}
