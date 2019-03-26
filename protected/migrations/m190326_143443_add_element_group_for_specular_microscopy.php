<?php

class m190326_143443_add_element_group_for_specular_microscopy extends CDbMigration
{
    public function up()
    {
        $element_group = ElementGroup::model()->findByAttributes(['name' => 'Anterior Segment']);
        $this->update('element_type', ['element_group_id' => $element_group->id],
            'class_name = :class_name', ['class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Specular_Microscopy']
        );

    }

    public function down()
    {
        $this->update('element_type', ['element_group_id' => null],
            'class_name = :class_name', ['class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Specular_Microscopy']
        );
    }
}