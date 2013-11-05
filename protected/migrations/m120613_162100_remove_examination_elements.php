<?php

class m120613_162100_remove_examination_elements extends CDbMigration
{
	//const EVENT_TYPE_ID = 1;

	public function up()
	{
		$db = $this->getDbConnection();
		$event_type_id = $db->createCommand("select id from event_type where `name` =:name;")
			->bindValues(array( ':name' => 'Examination'))->queryScalar();

		// Find all the element types for Examination
		$et_sql = "select el_t.id, el_t.name, el_t.class_name, el_t.last_modified_user_id,	el_t.last_modified_date, el_t.created_user_id, el_t.created_date, el_t.event_type_id, el_t.display_order, el_t.default
					from element_type el_t left join event_type ev_t on el_t.`event_type_id` = ev_t.`id` where ev_t.`name` =:name;";
		$elements = $db->createCommand($et_sql)
			->bindValues(array( ':name' => 'Examination'))->queryAll();

		$exceptions = array(
				'element_letter_out' => 'element_letterout'
		);
		foreach ($elements as $element) {
			$element_table_name = strtolower(implode('_', preg_split('/(?<=[a-z])(?=[A-Z])/',
					preg_replace('/(?<=[A-Z])([A-Z])(?![a-z])/e','strtolower("$1")', $element['class_name']))));
			if (isset($exceptions[$element_table_name])) {
				$element_table_name = $exceptions[$element_table_name];
			}

			// Remove the element type table
			$this->dropTable($element_table_name);
		}

		// Remove the Examination element type records
		$this->delete('element_type','event_type_id = ' . $event_type_id);

		// Remove the Examination event type
		$this->delete('event_type','id = ' . $event_type_id);

	}

	public function down()
	{
		echo "Cannot migrate down\n";
	}

}
