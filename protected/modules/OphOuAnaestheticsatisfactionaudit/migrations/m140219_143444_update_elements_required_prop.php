<?php

class m140219_143444_update_elements_required_prop extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphOuAnaestheticsatisfactionaudit_Anaesthetist'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphOuAnaestheticsatisfactionaudit_Satisfaction'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphOuAnaestheticsatisfactionaudit_VitalSigns'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphOuAnaestheticsatisfactionaudit_Notes'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphOuAnaestheticsatisfactionaudit_Anaesthetist'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphOuAnaestheticsatisfactionaudit_Satisfaction'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphOuAnaestheticsatisfactionaudit_VitalSigns'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphOuAnaestheticsatisfactionaudit_Notes'");
    }
}
