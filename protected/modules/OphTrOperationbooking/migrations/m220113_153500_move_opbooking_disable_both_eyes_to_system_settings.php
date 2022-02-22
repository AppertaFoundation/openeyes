<?php

class m220113_153500_move_opbooking_disable_both_eyes_to_system_settings extends OEMigration
{
    private $subs = array('Oculoplastics', 'Strabismus', 'Adnexal');

    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 3,
            'key' => 'opbooking_disable_both_eyes',
            'name' => 'Disable Both Eyes option for Operation Booking',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on',
            'lowest_setting_level' => 'INSTALLATION',
        ));

        foreach ($this->subs as $sub) {
            $this->execute("INSERT IGNORE INTO setting_subspecialty(`key`, `value`, `subspecialty_id`)
            SELECT 'opbooking_disable_both_eyes', 'on', (SELECT id FROM subspecialty WHERE `name` = :sub_name);", [':sub_name' => $sub]);
        }
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', 'key=:key', [':key' => 'opbooking_disable_both_eyes']);
        foreach ($this->subs as $sub) {
            $this->execute("DELETE FROM setting_subspecialty 
            WHERE `key` = 'opbooking_disable_both_eyes' 
                AND subspecialty_id = (SELECT id FROM subspecialty WHERE `name` = :sub_name);", [':sub_name' => $sub]);
        }
    }
}
