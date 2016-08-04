<?php

class m160121_174927_disable_manual_biometry extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array('display_order' => 0, 'field_type_id' => 3, 'key' => 'disable_manual_biometry', 'name' => 'Enable manual biometry measurement entry', 'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}', 'default_value' => 'off', 'last_modified_user_id' => 1));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key`="disable_manual_biometry"');
    }
}
