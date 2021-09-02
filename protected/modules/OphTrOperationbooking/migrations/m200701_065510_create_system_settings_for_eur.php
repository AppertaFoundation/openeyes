<?php

class m200701_065510_create_system_settings_for_eur extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->insert(
            'setting_metadata',
            array(
                'field_type_id' => 3,
                'key' => 'cataract_eur_switch',
                'name' => 'Enable EUR form for Cataract Operation Booking',
                'data' => serialize(array('on'=>'On', 'off'=>'Off')),
                'default_value' => 'off',
            )
        );
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "cataract_eur_switch"');
    }
}
