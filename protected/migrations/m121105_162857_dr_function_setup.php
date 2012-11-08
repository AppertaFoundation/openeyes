<?php

class m121105_162857_dr_function_setup extends CDbMigration
{
	public function up()
	{
		$specialty = $this->dbConnection->createCommand()->select('id')->from('specialty')->where('name=:name',array(':name'=>"Ophthalmology"))->queryRow();
		$this->insert('subspecialty', array('name'=>"Diabetic Retinopathy", 'ref_spec'=>'DR', 'specialty_id' => $specialty['id']) );
	}

	public function down()
	{
		
		$this->delete('subspecialty', "ref_spec = 'DR' AND name = 'Diabetic Retinopathy'" );
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