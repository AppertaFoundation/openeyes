<?php

class m180905_125530_add_system_setting_disable_golden_patient extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 25,
            'field_type_id' => 3,
            'key' => 'op_booking_disable_golden_patient',
            'name' => 'Disable "Suitable as Golden Patient"',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off'
        ));
        $this->insert('setting_installation', array(
            'key' => 'op_booking_disable_golden_patient',
            'value' => 'off'
        ));
    }


    public function down()
    {
        $this->delete('setting_installation', '`key`="op_booking_disable_golden_patient"');
        $this->delete('setting_metadata', '`key`="op_booking_disable_golden_patient"');
    }
}
