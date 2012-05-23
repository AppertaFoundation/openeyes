<?php

class m120523_154923_deleted_field_on_event_table extends CDbMigration
{
	public function up()
	{
		$this->renameColumn('event','hidden','deleted');
	}

	public function down()
	{
		$this->renameColumn('event','deleted','hidden');
	}
}
