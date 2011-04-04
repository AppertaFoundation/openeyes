<?php

class m110329_144911_create_site_element_types_for_element_iop extends CDbMigration
{
	public function up()
	{
		// extract relevant entries
		$specialty = Specialty::model()->find('name=:name',array(':name'=>'Medical Retinal'));
		$possibleElementType = PossibleElementType::model()->find('event_type_id=:event_type_id and element_type_id=:element_type_id',array(':event_type_id'=>1,':element_type_id'=>13));

		// create site element type entries
		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElementType->id,
			'specialty_id' => $specialty->id,
			'view_number' => 1,
			'required' => 1,
			'first_in_episode' => 0
		));
		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElementType->id,
			'specialty_id' => $specialty->id,
			'view_number' => 1,
			'required' => 1,
			'first_in_episode' => 1
		));
	}

	public function down()
	{
		$specialty = Specialty::model()->find('name=:name',array(':name'=>'Medical Retinal'));
		$possibleElementType = PossibleElementType::model()->find('event_type_id=:event_type_id and element_type_id=:element_type_id',array(':event_type_id'=>1,':element_type_id'=>13));

		// remove site_element_type entries
		$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id and specialty_id = :specialty_id',
			array(':possible_element_type_id' => $possibleElementType->id, ':specialty_id' => $specialty->id)
		);

	}
}
