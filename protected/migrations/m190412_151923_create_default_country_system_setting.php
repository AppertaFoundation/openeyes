<?php

class m190412_151923_create_default_country_system_setting extends CDbMigration
{
    public function up()
    {
        $field_type = $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name = "Dropdown list"')
            ->queryScalar();
        $this->insert('setting_metadata', array(
            'key' => 'default_country',
            'name' => 'Default Country',
            'data' => '',
            'field_type_id' => $field_type,
            'default_value' => 'United Kingdom',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key`="default_country"');
    }
}
