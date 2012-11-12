<?php

class m121031_085020_remove_hyphens_from_nhs_numbers extends CDbMigration
{
	public function up()
	{
		foreach (Patient::model()->findAll("nhs_num like '%-%'") as $patient) {
			Yii::app()->db->createCommand("update patient set nhs_num = '".str_replace('-','',$patient->nhs_num)."' where id = $patient->id")->query();
		}
	}

	public function down()
	{
	}
}
