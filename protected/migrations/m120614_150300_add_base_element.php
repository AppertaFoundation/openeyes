<?php

class m120614_150300_add_base_element extends CDbMigration {

	public function up() {

		// This migration makes change not only to the base code but to any installed modules too. Not ideal, but
		// can't see a way around it that doesn't involve lots of extra hoops.

		// First we need to create the new base_element table
		$this->createTable('base_element',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_class' => "varchar(255) NOT NULL DEFAULT ''",
				'event_id' => "int(10) unsigned DEFAULT NULL",
				'PRIMARY KEY (`id`)',
				'CONSTRAINT `base_element_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		// Every element_type needs a foreign key to this table
		$element_types = ElementType::model()->findAll();
		foreach($element_types as $element_type) {
			$element_class = $element_type->class_name;
			$element_table = $element_class::model()->tableName();
			$this->addColumn($element_table, 'base_id', 'int(10) unsigned DEFAULT NULL');
		}

		// Next we need to populate the base_id
		$events = Event::model()->findAll();
		foreach($events as $event) {
			$elements_types = ElementType::model()->findAll('event_type_id = ?', array($event->event_type_id));
			foreach ($elements_types as $element_type) {
				$element_class = $element_type->class_name;
				if($element = $element_class::model()->find('event_id = ?',array($event->id))) {

					// Create base_element for element
					$base_element = new BaseElement();
					$base_element->event_id = $event->id;
					$base_element->element_class = $element_class;
					$base_element->save();

					// Update element to refer to base_element
					$element->base_id = $base_element->id;
					$element->save();
				}
			}
		}

		// Finally we can remove the redundant event_id and add in a constraint
		$element_types = ElementType::model()->findAll();
		foreach($element_types as $element_type) {
			$element_class = $element_type->class_name;
			$element_table = $element_class::model()->tableName();
			$this->dropForeignKey($element_table.'_event_id_fk', $element_table);
			$this->dropColumn($element_table, 'event_id');
			$this->addForeignKey($element_table.'_base_id_fk', $element_table, 'base_id', 'base_element', 'id');
		}

	}

	public function down() {
		echo "Cannot migrate down\n";
	}

}
