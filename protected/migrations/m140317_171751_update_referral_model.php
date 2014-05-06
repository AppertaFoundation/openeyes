<?php

class m140317_171751_update_referral_model extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('referral_gp_id_fk', 'referral');
		$this->dropColumn('referral','gp_id');
		$this->addColumn('referral','clock_start', 'datetime');
	}

	public function down()
	{
		$this->dropColumn('referral','clock_start');
		$this->addColumn('referral', 'gp_id', 'int(10) unsigned');
		$this->addForeignKey('referral_gp_id_fk','referral','gp_id','gp','id');
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
