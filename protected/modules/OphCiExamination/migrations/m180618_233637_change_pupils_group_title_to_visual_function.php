<?php

class m180618_233637_change_pupils_group_title_to_visual_function extends OEMigration
{
    public function safeUp()
    {
        $this->update('element_type', array('group_title' => 'Visual Function'), 'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PupillaryAbnormalities'));
    }

    public function safeDown()
    {
        $this->update('element_type', array('group_title' => 'Pupils'), 'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PupillaryAbnormalities'));
    }
}