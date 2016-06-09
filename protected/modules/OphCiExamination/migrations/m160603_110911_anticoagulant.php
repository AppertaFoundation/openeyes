<?php

class m160603_110911_anticoagulant extends OEMigration
{
    
    /**
	 * @param array $element_types
	 * @param int $event_type_id
	 * @return array - list of the element_types ids inserted
	 */
	protected function insertOEElementType(array $element_types, $event_type_id)
	{
		$display_order = 1;
		$element_type_ids = array();
		foreach ($element_types as $element_type_class => $element_type_data) {
			$default = isset($element_type_data['default']) ? $element_type_data['default'] : 1;
			$confirmedDisplayOrder = isset($element_type_data['display_order']) ?
				$element_type_data['display_order'] : $display_order * 10;
			//this is needed to se the parent id for those elements set as children elements of another element type
			$thisParentId = isset($element_type_data['parent_element_type_id']) ?
				$this->getIdOfElementTypeByClassName($element_type_data['parent_element_type_id']) : null;
			$required = isset($element_type_data['required']) ? $element_type_data['required'] : null;
			$this->insert(
				'element_type',
				array(
					'name' => $element_type_data['name'],
					'class_name' => $element_type_class,
					'event_type_id' => $event_type_id,
					'display_order' => $confirmedDisplayOrder,
					'default' => $default,
					'parent_element_type_id' => $thisParentId,
					'required' => $required
				)
			);

			// Insert element type id into element type array
			$element_type_ids[] = $this->dbConnection->createCommand()
				->select('id')
				->from('element_type')
				->where('class_name=:class_name', array(':class_name' => $element_type_class))
				->queryScalar();

			$display_order++;
		}
		return $element_type_ids;
	}
        
        
	public function up()
	{
            $event_type_id = $this->insertOEEventType('Examination', 'OphCiExamination', 'Ci');
            // Insert element types (in order of display)
            $element_types = array(
                'OEModule\OphCiExamination\models\Element_OphCiExamination_HistoryRisk' => array('name' => 'Risk', 'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History', 'display_order' => 30, 'default' => 0),
                );
            $this->insertOEElementType($element_types, $event_type_id);
            $this->createOETable('et_ophciexamination_anticoagulant', array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned'
            ), true);
            $this->addColumn('et_ophciexamination_history', 'anticoagulant', 'int(1) default 0');
            $this->addColumn('et_ophciexamination_history_version', 'anticoagulant', 'int(1) default 0');
	}
        
        public function down()
	{
            $this->dropOETable('et_ophciexamination_anticoagulant', true);
            $this->dropColumn('et_ophciexamination_history', 'anticoagulant');
            $this->dropColumn('et_ophciexamination_history_version', 'anticoagulant');
	}
}