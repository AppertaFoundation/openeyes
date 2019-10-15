<?php

class m180405_153729_signature_system_settings extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 10,
            'field_type_id' => 3,
            'key' => 'require_prescription_signature',
            'name' => 'PRESCRIPTION: Require signature on printed prescriptions',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', "element_type_id is null and `key` = 'require_prescription_signature'");
    }
}
