<?php

class m131220_144918_adjust_element_order extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->update('element_type', array('display_order' => 45), "event_type_id = {$event_type['id']} and class_name = 'Element_OphCiExamination_PupillaryAbnormalities'");
    }

    public function down()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->update('element_type', array('display_order' => 63), "event_type_id = {$event_type['id']} and class_name = 'Element_OphCiExamination_PupillaryAbnormalities'");
    }
}
