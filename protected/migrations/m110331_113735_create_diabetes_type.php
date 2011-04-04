<?php

class m110331_113735_create_diabetes_type extends CDbMigration
{
    public function up()
    {
		$this->createTable('element_diabetes_type', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'type' => "tinyint(1) unsigned NOT NULL DEFAULT '1'",
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('element_type', array(
			'name' => 'Diabetes type',
			'class_name' => 'ElementDiabetesType'
		));
		$elementType = ElementType::model()->findByAttributes(array(
			'name' => 'Diabetes type',
			'class_name' => 'ElementDiabetesType'
		));

		$this->insert('possible_element_type', array(
			'event_type_id' => 1,
			'element_type_id' => $elementType->id,
			'num_views' => 1,
			'order' => 12
		));

		$possibleElement = PossibleElementType::model()->findByAttributes(array(
			'event_type_id' => 1,
			'element_type_id' => $elementType->id,
			'num_views' => 1,
			'order' => 12
		));

		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElement->id,
			'specialty_id' => 8, // Medical retina
			'view_number' => 1,
			'required' => 1,
			'first_in_episode' => 1
		));
		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElement->id,
			'specialty_id' => 8, // Medical retina
			'view_number' => 1,
			'required' => 1,
			'first_in_episode' => 0
		));
    }

    public function down()
    {
		$elementType = ElementType::model()->findByAttributes(array(
			'name' => 'Diabetes type',
			'class_name' => 'ElementDiabetesType'
		));

		if (!empty($elementType)) {
			$possibleElement = PossibleElementType::model()->findByAttributes(array(
				'event_type_id' => 1,
				'element_type_id' => $elementType->id,
				'num_views' => 1,
				'order' => 12
			));
			$this->delete('site_element_type', 'possible_element_type_id = :id',
				array(':id' => $possibleElement->id)
			);

			$this->delete('possible_element_type', 'element_type_id = :id',
				array(':id' => $elementType->id)
			);
		}

		$this->delete('element_type', 'class_name = :class',
			array(':class' => 'ElementDiabetesType')
		);

		$this->dropTable('element_diabetes_type');
    }
}