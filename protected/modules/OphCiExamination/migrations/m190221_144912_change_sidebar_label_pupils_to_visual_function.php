<?php

class m190221_144912_change_sidebar_label_pupils_to_visual_function extends CDbMigration
{

    function get_event_type_id(){
        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name = :class_name', [':class_name' => 'OphCiExamination'])
            ->queryScalar();
    }

    public function safeUp()
    {
        $event_type_id = $this->get_event_type_id();

        $this->update('element_group', array('name' => 'Visual Function', 'display_order' => '30'), "name = 'Pupils' AND event_type_id =".$event_type_id);

        $this->update('element_group', array('display_order' => '20'), "name = 'Observations' AND event_type_id =".$event_type_id);
    }

    public function safeDown()
    {
        $event_type_id = $this->get_event_type_id();

        $this->update('element_group', array('name' => 'Pupils', 'display_order' => '20'), "name = 'Visual Function' AND event_type_id =".$event_type_id);

        $this->update('element_group', array('display_order' => '30'), "name = 'Observations' AND event_type_id =".$event_type_id);
    }
}
