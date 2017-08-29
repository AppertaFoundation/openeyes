<?php

class m170829_120523_LidsMedicalSFTB extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('et_ophciexamination_medical_lids', 'right_stfb');
		$this->dropColumn('et_ophciexamination_medical_lids', 'left_stfb');
	}

	public function down()
	{
		echo "m170829_120523_LidsMedicalSFTB does not support migration down.\n";
		return false;
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
