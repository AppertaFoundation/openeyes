<?php

class m140506_152147_rtt_versioning extends OEMigration
{
	public function up()
	{
		$this->addColumn('referral_version','clock_start', 'datetime');
		$this->dropColumn('referral_version','gp_id');
		$this->versionExistingTable('rtt');
	}

	public function down()
	{
		$this->dropTable('rtt_version');
		$this->dropColumn('referral_version','clock_start');
		$this->addColumn('referral_version', 'gp_id', 'integer');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}