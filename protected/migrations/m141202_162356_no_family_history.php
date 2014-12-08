<?php

class m141202_162356_no_family_history extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient', 'no_family_history_date', 'datetime');
		$this->addColumn('patient_version', 'no_family_history_date', 'datetime');
	}

	public function down()
	{
		$this->dropColumn('patient', 'no_family_history_date');
		$this->dropColumn('patient_version', 'no_family_history_date');
	}

}