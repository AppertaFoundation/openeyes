<?php

class m120302_000000_add_patient_date_of_death extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient','date_of_death','date DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('patient','date_of_death');
	}

	public function safeUp()
	{
		$this->up();
	}

	public function safeDown()
	{
		$this->down();
	}

}
