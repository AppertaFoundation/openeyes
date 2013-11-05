<?php

class m121031_085020_remove_hyphens_from_nhs_numbers extends CDbMigration
{
	public function up()
	{
		$dash_nsh_patients = "select * from patient where nhs_num like '%-%'";
		//Patient::model()->findAll("nhs_num like '%-%'")
		foreach ($this->getDbConnection()->createCommand($dash_nsh_patients)->queryAll() as $patient) {
			$this->getDbConnection()->createCommand("update patient set nhs_num = '".str_replace('-','',$patient->nhs_num)."' where id = $patient->id")->query();
		}
	}

	public function down()
	{
	}
}
