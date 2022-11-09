<?php

class m220921_015401_add_setting_to_toggle_break_glass_behaviour extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 22,
            'field_type_id' => 3,
            'key' => 'break_glass_patient_institution_field',
            'name' => 'Field for break-glass patient institution',
            'data' => serialize(array('primary_institution' => 'Primary Institution', 'county' => 'County')),
            'default_value' => 'primary_institution'
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key`="break_glass_patient_institution_field"');
    }
}
