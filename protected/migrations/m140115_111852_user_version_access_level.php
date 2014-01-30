<?php

class m140115_111852_user_version_access_level extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('user_version','access_level');
	}

	public function down()
	{
		$this->addColumn('user_version','access_level','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}
}
