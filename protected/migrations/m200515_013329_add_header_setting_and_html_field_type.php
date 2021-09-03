<?php

class m200515_013329_add_header_setting_and_html_field_type extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_field_type', array('name' => 'HTML'));

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 0,
            'key' => 'letter_header',
            'name' => 'Letter Header',
            'default_value' => '',
            'field_type_id' => $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name = "HTML"')->queryScalar(),
            'data' => '',
        ));

        $this->insert('setting_installation', array(
            'element_type_id' => null,
            'key' => 'letter_header',
            'value' => '',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_installation', array('key=\'letter_header\''));
        $this->delete('setting_metadata', array('key=\'letter_header\''));
        $this->delete('setting_field_type', array('name = \'HTML\''));
    }
}
