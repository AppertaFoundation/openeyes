<?php

class m110329_133935_create_site_element_types_for_element_referred_from_screening extends CDbMigration
{
	public function up()
	{
		// extract the relevant entries
		$eventType = EventType::model()->find('name=:name',array(':name'=>'examination'));
		$specialty = Specialty::model()->find('name=:name',array(':name'=>'Medical Retinal'));

		// create element type
		$this->insert('element_type', array(
			'name' => 'Referred from screening',
			'class_name' => 'ElementReferredFromScreening'
		));

		// extract element type
		$elementType = ElementType::model()->find('name=:name',array(':name'=>'Referred from screening'));

		// create possible element type
		$this->insert('possible_element_type', 
			array(
				'event_type_id' => $eventType->id, 
				'element_type_id' => $elementType->id,
				'num_views' => 1,
				'order' => 1
			)
		);

		// extract possible element type
		$possibleElementType = PossibleElementType::model()->find(
			'event_type_id=:event_type_id and element_type_id=:element_type_id',
			array(':event_type_id'=>$eventType->id,':element_type_id'=>$elementType->id)
		);

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
		// extract the relevant entries
		$elementType = ElementType::model()->find('name=:name',array(':name'=>'Referred from screening'));	
		$specialty = Specialty::model()->find('name=:name',array(':name'=>'Medical Retinal'));
		$eventType = EventType::model()->find('name=:name',array(':name'=>'examination'));
                $possibleElementType = PossibleElementType::model()->find(
                        'event_type_id=:event_type_id and element_type_id=:element_type_id',
                        array(':event_type_id'=>$eventType->id,':element_type_id'=>$elementType->id)
                );
	
		// remove site_element_type entries
		$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id and specialty_id = :specialty_id',
			array(':possible_element_type_id' => $possibleElementType->id, ':specialty_id' => $specialty->id)
		);

		// remove possible_element_type entries
		$this->delete('possible_element_type', 'id = :id',
			array(':id' => $possibleElementType->id)
		);

		// remove element_type
		$this->delete('element_type', 'id = :id',
			array(':id' => $elementType->id)
		);
	}
}
