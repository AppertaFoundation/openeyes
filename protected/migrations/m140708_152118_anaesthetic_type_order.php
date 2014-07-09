<?php

class m140708_152118_anaesthetic_type_order extends OEMigration
{
	public function up()
	{
		$this->dbConnection->createCommand("update anaesthetic_type set display_order = 2 where name = 'LA'")->query();
		$this->dbConnection->createCommand("update anaesthetic_type set display_order = 3 where name = 'LAC'")->query();
	}

	public function down()
	{
		$this->dbConnection->createCommand("update anaesthetic_type set display_order = 3 where name = 'LA'")->query();
		$this->dbConnection->createCommand("update anaesthetic_type set display_order = 2 where name = 'LAC'")->query();
	}


}