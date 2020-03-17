<?php

class m191001_024153_set_default_country extends CDbMigration
{
    public function safeUp()
    {
        $countries = $this->dbConnection->createCommand('SELECT name FROM country ORDER BY id')->queryColumn();
//      CERA-572   Combining the countries array into a key value pair where key=value, this makes it easier to set and handle default countries as we can reference the keys by the country name itself
        $countries = array_combine($countries, $countries);
        $this->alterColumn('setting_metadata', 'data', 'varchar(16384)');
        $this->delete('setting_metadata', '`key` = \'default_country\'');
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 21,
            'field_type_id' => 2,
            'key' => 'default_country',
            'name' => 'Default Country',
            'data' => serialize($countries),
            'default_value' => 'United Kingdom'
        ));
    }
    public function safeDown()
    {
        $this->delete('setting_installation', '`key` = \'default_country\'');
        $this->delete('setting_metadata', '`key` = \'default_country\'');
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 22,
            'field_type_id' => 2,
            'key' => 'default_country',
            'name' => 'Default Country',
            'data' => '',
            'default_value' => 'United Kingdom'
        ));
    }
}