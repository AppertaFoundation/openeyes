<?php

class m191014_125600_add_a_setting_to_shorten_page_titles extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 3,
            'key' => 'use_short_page_titles',
            'name' => 'Use short page titles (no patient name or - OE)',
            'default_value' => 'off',
            'data' => serialize(array('on'=>'On', 'off'=>'Off'))
        ));

        $this->insert('setting_installation', array(
            'key' => 'use_short_page_titles',
            'value' => 'off',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', array('key = \'use_short_page_titles\''));
        $this->delete('setting_installation', array('key=\'use_short_page_titles\''));
    }
}
