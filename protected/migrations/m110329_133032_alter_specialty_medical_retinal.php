<?php

class m110329_133032_alter_specialty_medical_retinal extends CDbMigration
{
	public function up()
	{
		$this->update('specialty', array('name'=>'Medical Retinal'), "name='Medical Retina'");
	}

	public function down()
	{
		$this->update('specialty', array('name'=>'Medical Retina'), "name='Medical Retinal'");
	}
}
