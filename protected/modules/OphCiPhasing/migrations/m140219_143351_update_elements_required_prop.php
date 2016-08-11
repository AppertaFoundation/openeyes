<?php

class m140219_143351_update_elements_required_prop extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphCiPhasing_IntraocularPressure'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiPhasing_IntraocularPressure'");
    }
}
