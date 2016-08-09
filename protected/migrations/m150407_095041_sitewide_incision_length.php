<?php

class m150407_095041_sitewide_incision_length extends CDbMigration
{
    public function up()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        if ($id) {
            $this->insert('setting_metadata', array(
                'key' => 'default_incision_length',
                'name' => 'Default Incision Length',
                'data' => '',
                'field_type_id' => $id['id'],
                'default_value' => '2.8',
            ));
        }
    }

    public function down()
    {
        $this->delete('setting_metadata', 'name = "Default Incision Length"');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
