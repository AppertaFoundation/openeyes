<?php

class m140822_101606_reorder_elements extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('display_order' => 10), "class_name = 'Element_OphInBiometry_LensType'");
        $this->update('element_type', array('display_order' => 20), "class_name = 'Element_OphInBiometry_Selection'");
        $this->update('element_type', array('display_order' => 30), "class_name = 'Element_OphInBiometry_BiometryData'");
        $this->update('element_type', array('display_order' => 40), "class_name = 'Element_OphInBiometry_Calculation'");
    }

    public function down()
    {
        $this->update('element_type', array('display_order' => 1), "class_name = 'Element_OphInBiometry_LensType'");
        $this->update('element_type', array('display_order' => 1), "class_name = 'Element_OphInBiometry_Selection'");
        $this->update('element_type', array('display_order' => 1), "class_name = 'Element_OphInBiometry_BiometryData'");
        $this->update('element_type', array('display_order' => 1), "class_name = 'Element_OphInBiometry_Calculation'");
    }
}
