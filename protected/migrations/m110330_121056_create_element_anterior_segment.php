<?php

class m110330_121056_create_element_anterior_segment extends CDbMigration
{
	public function up()
	{
		// create element table
		$this->dropTable('element_anterior_segment');
		$this->createTable('element_anterior_segment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'description_left' => 'text',
			'description_right' => 'text',
			'image_string_left' => 'text',
			'image_string_right' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		// extract the relevant entries
		$eventType = EventType::model()->find('name=:name',array(':name'=>'examination'));
		$specialties = Specialty::model()->findAll('name != :name',array(':name'=>'Adnexal'));

		// create element type
		/*
		$this->insert('element_type', array(
			'name' => 'Anterior segment',
			'class_name' => 'ElementAnteriorSegment'
		));
		*/

		// extract element type
		$elementType = ElementType::model()->find('name=:name',array(':name'=>'Anterior segment'));

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
		foreach ($specialties as $specialty) {
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
	}

	public function down()
	{
		$this->dropTable('element_anterior_segment');

		// extract the relevant entries
		$elementType = ElementType::model()->find('name=:name',array(':name'=>'Anterior segment'));
		$specialties = Specialty::model()->findAll('name != :name',array(':name'=>'Adnexal'));
		$eventType = EventType::model()->find('name=:name',array(':name'=>'examination'));
		$possibleElementType = PossibleElementType::model()->find(
			'event_type_id=:event_type_id and element_type_id=:element_type_id',
			array(':event_type_id'=>$eventType->id,':element_type_id'=>$elementType->id)
		);

		// remove site_element_type entries
		foreach ($specialties as $specialty) {
			$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id and specialty_id = :specialty_id',
				array(':possible_element_type_id' => $possibleElementType->id, ':specialty_id' => $specialty->id)
			);
		}

		// remove possible_element_type entries
		$this->delete('possible_element_type', 'id = :id',
			array(':id' => $possibleElementType->id)
		);

		// remove element_type
		/*
		$this->delete('element_type', 'id = :id',
			array(':id' => $elementType->id)
		);
		*/
	}
}
