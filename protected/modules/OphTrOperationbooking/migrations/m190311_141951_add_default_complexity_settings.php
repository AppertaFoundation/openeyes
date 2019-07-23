<?php

class m190311_141951_add_default_complexity_settings extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => 4,
            'display_order' => 15,
            'key' => 'op_booking_inc_time_high_complexity',
            'name' => 'Increase estimated Op Booking time for complex cases (int %)',
            'default_value' => '20'
        ));

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 15,
            'field_type_id' => 4,
            'key' => 'op_booking_decrease_time_low_complexity',
            'name' => 'Decrease estimated Op Booking time for complex cases (int %)',
            'default_value' => '10'
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "op_booking_high_complexity_add"');
        $this->delete('setting_metadata', '`key` = "op_booking_high_complexity_add"');
    }
}