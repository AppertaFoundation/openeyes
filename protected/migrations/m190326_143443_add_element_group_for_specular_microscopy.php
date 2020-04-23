<?php

class m190326_143443_add_element_group_for_specular_microscopy extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand("SELECT * FROM event_type WHERE class_name = 'OphCiExamination'")
            ->queryAll();
        $element_group = $this->dbConnection->createCommand('SELECT * FROM element_type')
            ->where("name = 'Anterior Segment'")
            ->andWhere('event_type_id = :event_type_id', array('event_type_id' => $event_type['id']))
            ->queryRow();

        $this->update(
            'element_type',
            ['element_group_id' => $element_group['id']],
            'class_name = :class_name',
            ['class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Specular_Microscopy']
        );
    }

    public function down()
    {
        $this->update(
            'element_type',
            ['element_group_id' => null],
            'class_name = :class_name',
            ['class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Specular_Microscopy']
        );
    }
}
