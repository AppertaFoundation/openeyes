<?php

class m120621_102952_fix_procedure_name extends CDbMigration
{
	public function up()
	{
		$this->update('proc',array('term'=>'Injection into anterior chamber of eye','snomed_term'=>'Injection into anterior chamber of eye'),"snomed_code='4143006'");
	}

	public function down()
	{
		$this->update('proc',array('term'=>'Injection of anterior chamber of eye','snomed_term'=>'Injection of anterior chamber of eye'),"snomed_code='4143006'");
	}
}
