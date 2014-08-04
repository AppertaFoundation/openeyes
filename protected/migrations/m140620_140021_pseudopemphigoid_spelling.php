<?php

class m140620_140021_pseudopemphigoid_spelling extends CDbMigration
{
	public function up()
	{
		$this->dbConnection->createCommand('update medication_stop_reason set name =\'Pseudopemphigoid\' where name=\'Pseudophembhygoid\'')->query();
	}

	public function down()
	{
		$this->dbConnection->createCommand('update medication_stop_reason set name =\'Pseudophembhygoid\' where name=\'Pseudopemphigoid\'')->query();
	}
}