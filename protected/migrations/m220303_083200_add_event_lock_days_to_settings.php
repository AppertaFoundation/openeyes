<?php

class m220303_083200_add_event_lock_days_to_settings extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', ['key' => 'event_lock_days', 'field_type_id' => 4, 'name' => 'Number of full days events can be edited for', 'default_value' => 1, 'lowest_setting_level' => 'INSTALLATION']);
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = ?', ['event_lock_days']);
    }
}
