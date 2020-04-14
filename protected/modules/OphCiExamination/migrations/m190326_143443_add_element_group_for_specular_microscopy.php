<?php

class m190326_143443_add_element_group_for_specular_microscopy extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand('SELECT id FROM event_type WHERE class_name = "OphCiExamination"')
            ->queryScalar();
        $element_group = $this->dbConnection->createCommand('SELECT id FROM element_group WHERE name = :name AND event_type_id = :event_type')
            ->bindValues(array(':name' => 'Anterior Segment', 'event_type' => $event_type))
            ->queryScalar();
        $this->update(
            'element_type',
            ['element_group_id' => $element_group],
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
