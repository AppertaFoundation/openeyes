<?php

class m130520_095926_new_user_fields extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user','is_clinical','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('user','is_consultant','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('user','is_surgeon','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('user','is_clinical');
		$this->dropColumn('user','is_consultant');
		$this->dropColumn('user','is_surgeon');
	}
}
