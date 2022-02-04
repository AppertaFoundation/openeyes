<?php

class m220113_153500_move_opbooking_disable_both_eyes_to_system_settings extends OEMigration
{
    private $subs = array('Oculoplastics', 'Strabismus', 'Adnexal');

    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 1,
            'key' => 'opbooking_disable_both_eyes',
            'name' => 'Disable Both Eyes option for Operation Booking',
            'data' => '',
            'default_value' => 1,
            'lowest_setting_level' => 'INSTALLATION',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', 'key=:key', [':key' => 'opbooking_disable_both_eyes']);
    }
}
