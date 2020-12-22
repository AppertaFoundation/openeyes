<?php

class m200630_080422_create_operationchecklists_element_set_item_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophtroperationchecklists_element_set_item', array(
            'id' => 'pk',
            'set_id' => 'int(11)',
            'element_type_id' => 'int(10) unsigned',
            'is_hidden' => 'tinyint default 0',
            'is_mandatory' => 'tinyint default 0',
            'display_order' => 'int(10)',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophtroperationchecklists_element_set_item_sid_fk',
            'ophtroperationchecklists_element_set_item',
            'set_id',
            'ophtroperationchecklists_element_set',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_element_set_item_etid_fk',
            'ophtroperationchecklists_element_set_item',
            'element_type_id',
            'element_type',
            'id'
        );

        // insert data
        $element_set_items_array = [];
        $display_order = 1;

        $element_set_items_array[] = $this->createElementSetArray(['Procedure List', 'Admission', 'Notes'], 1, $display_order)[0];

        // Repeat this step 3 times but increment the set_id each time.
        for ($i=2; $i<5; $i++) {
            $element_set_items_array[] = $this->createElementSetArray(['Documentation', 'Clinical Assessment', 'Nursing / Practitioner Assessment', 'Pressure Ulcer prevention and Management', 'DVT', 'Patient Support'], $i, $display_order)[0];
        }

        $element_set_items_array[] =$this->createElementSetArray(['Discharge'], 5, $display_order)[0];

        foreach ($element_set_items_array as $element_set_items) {
            foreach ($element_set_items as $element_set_item) {
                $this->insert('ophtroperationchecklists_element_set_item', $element_set_item);
            }
        }
    }

    private function createElementSetArray($element_names, $set_id, &$display_order)
    {
        $element_set_items = [];

        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name="OphTrOperationchecklists"')
            ->queryScalar();

        $element_types = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where(array('in', 'name', $element_names))
            ->andWhere('event_type_id=' . $event_type_id)
            ->queryAll();

        $element_set_items_set = [];

        foreach ($element_types as $element_type) {
            $element_type['set_id'] = $set_id;
            $element_type['element_type_id'] = $element_type['id'];
            $element_type['display_order'] = $display_order++;
            unset($element_type['id']);
            $element_set_items_set[] = $element_type;
        }

        $element_set_items[] = $element_set_items_set;

        return $element_set_items;
    }

    public function down()
    {
        $this->dropOETable('ophtroperationchecklists_element_set_item', true);
    }
}
