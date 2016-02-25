<?php

class m140821_135546_fix_cct_display_order extends OEMigration
{
    public function safeUp()
    {
        $this->update('element_type', array('display_order' => 51), 'class_name = :class', array(':class' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_AnteriorSegment_CCT'));
    }

    public function safeDown()
    {
        $this->update('element_type', array('display_order' => 49), 'class_name = :class', array(':class' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_AnteriorSegment_CCT'));
    }
}
