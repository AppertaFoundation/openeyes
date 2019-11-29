<?php

class m181107_000226_set_hos_num_auto_increment_starting_number extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array('element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'hos_num_start',
                'name' => 'Auto Increment Start Number',
                'default_value' => '1'
            )
        );
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'hos_num_start\'');
    }

}