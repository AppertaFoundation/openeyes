<?php

class m180125_034319_add_display_theme_setting extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'display_theme',
            'name' => 'Display Theme',
            'data' => serialize(array('light' => 'Light', 'dark' => 'Dark')),
            'default_value' => 'light',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key`="display_theme"');
    }
}
