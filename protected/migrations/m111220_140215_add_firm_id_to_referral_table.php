<?php

class m111220_140215_add_firm_id_to_referral_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('referral','firm_id','int(10) unsigned NULL DEFAULT NULL');
		$this->addForeignKey('firm_fk','referral','firm_id','firm','id');
	}

	public function down()
	{
		$this->dropForeignKey('firm_fk','referral');
		$this->dropColumn('referral','firm_id');
	}
}
