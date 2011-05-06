<?php

class m110420_152542_alter_operation_procedure_assignment extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('operation_procedure_assignment', 'duration');
		
		$eventType = $this->dbConnection->createCommand()
			->select('id')
			->from('event_type')
			->where('name=:name', array(':name'=>'operation'))
			->queryRow();
		$specialties = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->queryAll();
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name',
				array(':name'=>'Appointment'))
			->queryRow();
		
		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:event_type_id and element_type_id=:element_type_id',
				array(':event_type_id'=>$eventType['id'],':element_type_id'=>$elementType['id']))
			->queryRow();

		// remove site_element_type entries
		foreach ($specialties as $specialty) {
			$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id and specialty_id = :specialty_id',
				array(':possible_element_type_id' => $possibleElementType['id'], ':specialty_id' => $specialty['id'])
			);
		}

		// remove possible_element_type entries
		$this->delete('possible_element_type', 'id = :id',
			array(':id' => $possibleElementType['id'])
		);
	}

	public function down()
	{
		$this->addColumn('operation_procedure_assignment', 'duration', 'smallint(5) unsigned NOT NULL');
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