<?php

class m180109_110900_add_system_setting_disable_prescription_patient_copy extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'disable_prescription_patient_copy',
            'name' => 'PRESCRIPTION: Disable additional copy for patient when printing',
            'data' => serialize(array('on'=>'On', 'off'=>'Off')),
            'default_value' => 'on'
        ));
        $this->insert('setting_installation', array(
            'key' => 'disable_prescription_patient_copy',
            'value' => 'on'
        ));

    }

    public function down()
    {
        $this->delete('setting_installation', '`key`="disable_prescription_patient_copy"');
        $this->delete('setting_metadata', '`key`="disable_prescription_patient_copy"');

    }
}
