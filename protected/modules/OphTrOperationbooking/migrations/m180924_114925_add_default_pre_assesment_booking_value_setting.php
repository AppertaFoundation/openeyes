<?php

class m180924_114925_add_default_pre_assesment_booking_value_setting extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 25 ,
            'field_type_id' => 3,
            'key' => 'pre_assessment_booking_default_value',
            'name' => 'Pre-assessment booking required default value',
            'data' => serialize(array('1' => 'Yes', '0' => 'No')),
            'default_value' => '0'
        ));
        $this->insert('setting_installation', array(
            'key' => 'pre_assessment_booking_default_value',
            'value' => '0'
        ));
    }


    public function down()
    {
        $this->delete('setting_installation', '`key`="pre_assessment_booking_default_value"');
        $this->delete('setting_metadata', '`key`="pre_assessment_booking_default_value"');

    }
}
