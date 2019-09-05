<?php

class m190424_151328_remake_labresults_inr_element_to_be_lab_results_entry_element extends CDbMigration
{
    public function up()
    {
        $this->update('element_type',
            ['name' => 'Lab Results Entry', 'class_name' => 'Element_OphInLabResults_Entry'],
            'class_name = :class', [':class' => 'Element_OphInLabResults_Inr']);
    }

    public function down()
    {
        $this->update('element_type',
            ['name' => 'INR Result', 'class_name' => 'Element_OphInLabResults_Inr'],
            'class_name = :class', [':class' => 'Element_OphInLabResults_Entry']);
    }
}