<?php

class m140219_143252_update_elements_required_prop extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrOperationbooking_Diagnosis'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrOperationbooking_Operation'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrOperationbooking_ScheduleOperation'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrOperationbooking_Diagnosis'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrOperationbooking_Operation'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrOperationbooking_ScheduleOperation'");
    }
}
