<?php

class m191128_000218_add_enable_prescription_overprinting_setting extends CDbMigration
{
    public function safeUp()
    {
        $this->insert(
            'setting_metadata',
            array(
                'field_type_id' => 3,
                'key' => 'enable_prescription_overprint',
                'name' => 'Enable FP10/WP10 printing',
                'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
                'default_value' => 'off',
            )
        );
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "enable_prescription_overprint"');
    }
}
