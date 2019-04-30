<?php

class m190429_124800_change_pcr_risk_element_type_to_the_right_element_group extends CDbMigration
{
    public function up()
    {
        $examination_event_type = EventType::model()->find('name = ?', ['Examination']);
        $pcr_function_element_group = ElementGroup::model()->find('name = :name AND event_type_id = :event_type',
            [':name' => 'PCR Risk', ':event_type' => $examination_event_type->id]);
        $this->update('element_type',
            ['element_group_id' => $pcr_function_element_group->id],
            'class_name = :class_name', [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PcrRisk']);
    }

    public function down()
    {
        echo "m190429_124800_change_pcr_risk_element_type_to_the_right_element_group does not support migration down.\n";
        return false;
    }
}
