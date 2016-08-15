<?php

class m140219_143400_update_elements_required_prop extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphCoTherapyapplication_Therapydiagnosis'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphCoTherapyapplication_PatientSuitability'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphCoTherapyapplication_RelativeContraindications'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphCoTherapyapplication_MrServiceInformation'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphCoTherapyapplication_ExceptionalCircumstances'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCoTherapyapplication_Therapydiagnosis'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCoTherapyapplication_PatientSuitability'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCoTherapyapplication_RelativeContraindications'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCoTherapyapplication_MrServiceInformation'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCoTherapyapplication_ExceptionalCircumstances'");
    }
}
