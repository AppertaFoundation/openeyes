<?php

class m140214_084926_anaesthetic_type_display_order extends CDbMigration
{
	public function up()
	{
		$this->addColumn('anaesthetic_type','display_order','tinyint(1) unsigned not null');

		$this->dbConnection->createCommand("update anaesthetic_type set display_order = id")->query();
	}

	public function down()
	{
		$this->dropColumn('anaesthetic_type','display_order');
	}
}
