<?php

class m140619_134752_nullable_fuzzy_dates extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('previous_operation','date',	'VARCHAR(10)  NULL DEFAULT NULL COLLATE \'utf8_unicode_ci\'');
		$this->dbConnection->createCommand('update previous_operation set date = null where date =\'0000-00-00\'')->query();
	}

	public function down()
	{
		$this->dbConnection->createCommand('update previous_operation set date = \'0000-00-00\' where date is null')->query();
		$this->alterColumn('previous_operation','date',	'VARCHAR(10) NOT NULL COLLATE \'utf8_unicode_ci\'');
	}
}