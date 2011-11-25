<?php

class m111125_093130_alter_referral_table extends CDbMigration
{
	public function up()
	{
		$this->truncateTable('referral');
		$this->dropColumn('referral', 'service_id');
		$this->addColumn('referral', 'service_specialty_assignment_id', 'int(10) unsigned NOT NULL');
		$this->addForeignKey('referral_ibfk_1','referral','service_specialty_assignment_id','service_specialty_assignment','id');
	}

	public function down()
	{
		$this->dropForeignKey('referral_ibfk_1', 'referral');
		$this->dropColumn('referral', 'service_specialty_assignment_id');
                $this->addColumn('referral', 'service_id', 'int(10) unsigned NOT NULL');
	}
}
