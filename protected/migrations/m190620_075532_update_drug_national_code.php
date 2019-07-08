<?php

class m190620_075532_update_drug_national_code extends CDbMigration
{
	public function up()
	{
		if (($handle = fopen(Yii::app()->basePath."/migrations/data/m190620_075532_update_drug_national_code/01_Old2NewDrugmapping.csv", "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$this->update('drug', ['national_code' => $data[1]], 'name = "'.$data[0].'" AND (national_code="" OR national_code IS NULL)'); 
			}
			fclose($handle);
		}
	}

	public function down()
	{
		echo "m190708_075532_update_drug_national_code does not support migration down.\n";
		return true;
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