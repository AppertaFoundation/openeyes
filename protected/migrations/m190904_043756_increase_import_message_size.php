<?php

class m190904_043756_increase_import_message_size extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('import', 'message', 'varchar(4096) NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('import', 'message', 'string NOT NULL');
	}
}