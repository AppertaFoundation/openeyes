<?php

class m190412_151923_create_default_country_system_setting extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array('display_order' => 0, 'field_type_id' => 3, 'key' => 'default_country', 'name' => 'Require exam before booking', 'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}', 'default_value' => 'off'));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key`="require_exam_before_booking"');
    }
}