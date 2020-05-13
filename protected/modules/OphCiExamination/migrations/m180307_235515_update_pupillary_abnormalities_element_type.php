<?php

class m180307_235515_update_pupillary_abnormalities_element_type extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->update(
            'element_type',
            array(
                'name' => 'Pupils',
                'display_order' => 135,
            ),
            "class_name='OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_PupillaryAbnormalities'"
        );
    }

    public function safeDown()
    {
        $this->update(
            'element_type',
            array('name' => 'Pupillary Abnormalities', 'element_group_id' => null, 'display_order' => 180),
            "class_name='OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_PupillaryAbnormalities'"
        );
    }
}
