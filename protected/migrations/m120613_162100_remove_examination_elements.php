<?php

class m120613_162100_remove_examination_elements extends CDbMigration
{
	const EVENT_TYPE_ID = 1;

	public function up()
	{
		// Find all the element types for Examination
		$elements = ElementType::model()->findAll('event_type_id = ' . self::EVENT_TYPE_ID);
		$exceptions = array(
				'element_letter_out' => 'element_letterout'
		);
		foreach ($elements as $element) {
			$element_table_name = strtolower(implode('_', preg_split('/(?<=[a-z])(?=[A-Z])/',
					preg_replace('/(?<=[A-Z])([A-Z])(?![a-z])/e','strtolower("$1")', $element->class_name))));
			if (isset($exceptions[$element_table_name])) {
				$element_table_name = $exceptions[$element_table_name];
			}

			// Remove the element type table
			$this->dropTable($element_table_name);
		}

		// Remove the Examination element type records
		$this->delete('element_type','event_type_id = ' . self::EVENT_TYPE_ID);

		// Remove the Examination event type
		$this->delete('event_type','id = ' . self::EVENT_TYPE_ID);

	}

	public function down()
	{
		echo "Cannot migrate down\n";
	}

}
