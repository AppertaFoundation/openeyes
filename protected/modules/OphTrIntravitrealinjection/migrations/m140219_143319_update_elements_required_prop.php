<?php

class m140219_143319_update_elements_required_prop extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrIntravitrealinjection_Site'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrIntravitrealinjection_Anaesthetic'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrIntravitrealinjection_Treatment'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrIntravitrealinjection_AnteriorSegment'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrIntravitrealinjection_PostInjectionExamination'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrIntravitrealinjection_Complications'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrIntravitrealinjection_Site'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrIntravitrealinjection_Anaesthetic'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrIntravitrealinjection_Treatment'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrIntravitrealinjection_AnteriorSegment'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrIntravitrealinjection_PostInjectionExamination'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrIntravitrealinjection_Complications'");
    }
}
