<?php

class m200622_140544_add_training_mode_setting extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array('display_order' => 0, 'field_type_id' => 3, 'key' => 'training_mode_enabled', 'name' => 'Display OE in training mode', 'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}', 'default_value' => 'off'));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key`="training_mode_enabled"');
    }
}
