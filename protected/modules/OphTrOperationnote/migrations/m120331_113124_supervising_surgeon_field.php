<?php

class m120331_113124_supervising_surgeon_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationnote_procedurelist','supervising_surgeon_id','integer(10) unsigned NULL');
	}

	public function down()
	{
		$this->dropColumn('et_ophtroperationnote_procedurelist','supervising_surgeon_id');
	}
}
