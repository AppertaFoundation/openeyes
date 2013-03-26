<?php

class m130320_141259_user_access_level_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user','access_level','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->update('user',array('access_level'=>4),"username='admin'");
	}

	public function down()
	{
		$this->dropColumn('user','access_level');
	}
}
