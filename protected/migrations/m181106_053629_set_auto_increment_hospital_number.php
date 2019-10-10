<?php

class m181106_053629_set_auto_increment_hospital_number extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 0,
            'field_type_id' => 3, // Radio Buttons
            'key' => 'set_auto_increment',
            'name' => 'Hospital Number Auto Increment',
            'data' => serialize(array('on' => 'On', 'off' => 'Off')),
            'default_value' => 'off',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'set_auto_increment\'');
    }

}