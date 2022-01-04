<?php

class m200300_133000_add_hos_num_label extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => 4,
            'key' => 'hos_num_label',
            'name' => 'Hospital Number label',
            'default_value' => 'ID'
        ));
        $this->insert('setting_installation', array('key' => 'hos_num_label', 'value' => 'ID'));
    }
    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'hos_num_label\'');
        $this->delete('setting_installation', '`key` = \'hos_num_label\'');
    }
}
