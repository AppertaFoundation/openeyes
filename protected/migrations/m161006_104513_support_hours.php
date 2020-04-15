<?php

class m161006_104513_support_hours extends CDbMigration
{
    public function up()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $id['id'],
            'key' => 'helpdesk_hours',
            'name' => 'Helpdesk Hours',
            'data' => '',
            'default_value' => '8:00am to 6:00pm',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key` = "helpdesk_hours"');
    }
}
