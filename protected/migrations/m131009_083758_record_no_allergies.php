<?php

class m131009_083758_record_no_allergies extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient', 'no_allergies_date', 'datetime');
	}

	public function down()
	{
		$this->dropColumn('patient', 'no_allergies_date');
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
