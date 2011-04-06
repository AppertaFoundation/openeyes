<?php

class m110329_144911_create_site_element_types_for_element_iop extends CDbMigration
{
	public function up()
	{
		// extract relevant entries
		$specialty = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->where('name=:name', 
				array(':name'=>'Medical Retinal'))
			->queryRow();
		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:eventType AND 
				element_type_id=:elementType',
				array(':eventType'=>1,':elementType'=>13))
			->queryRow();		

		// create site element type entries
		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElementType['id'],
			'specialty_id' => $specialty['id'],
			'view_number' => 1,
			'required' => 1,
			'first_in_episode' => 0
		));
		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElementType['id'],
			'specialty_id' => $specialty['id'],
			'view_number' => 1,
			'required' => 1,
			'first_in_episode' => 1
		));
	}

	public function down()
	{
		$specialty = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->where('name=:name', 
				array(':name'=>'Medical Retinal'))
			->queryRow();
		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:eventType AND 
				element_type_id=:elementType',
				array(':eventType'=>1,':elementType'=>13))
			->queryRow();

		// remove site_element_type entries
		$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id and specialty_id = :specialty_id',
			array(':possible_element_type_id' => $possibleElementType['id'], ':specialty_id' => $specialty['id'])
		);

	}
}
