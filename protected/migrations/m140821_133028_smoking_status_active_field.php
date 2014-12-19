<?php

class m140821_133028_smoking_status_active_field extends OEMigration
{
	public function safeUp()
	{
		$this->addColumn('socialhistory_smoking_status', 'active', 'boolean not null default true');
		$this->addColumn('socialhistory_smoking_status_version', 'active', 'boolean not null default true');
	}

	public function safeDown()
	{
		$this->dropColumn('socialhistory_smoking_status', 'active');
		$this->dropColumn('socialhistory_smoking_status_version', 'active');
	}
}