<?php

class m200515_160612_update_elementType_lab_details_table_to_required extends OEMigration
{
    public function safeUp()
    {
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphInLabResults_Details'");
    }

    public function safeDown()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphInLabResults_Details'");
    }
}
