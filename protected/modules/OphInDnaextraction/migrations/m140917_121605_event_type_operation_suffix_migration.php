<?php

class m140917_121605_event_type_operation_suffix_migration extends CDbMigration
{
	public function up()
	{
		$event_type_id=$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphInDnaextraction'))->queryScalar();
		$this->update('event_type',array('rbac_operation_suffix'=>'Genetics'),"id = $event_type_id");
	}

	public function down()
	{
		$event_type_id=$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphInDnaextraction'))->queryScalar();
		$this->update('event_type',array('rbac_operation_suffix'=>NULL),"id = $event_type_id");
	}
}