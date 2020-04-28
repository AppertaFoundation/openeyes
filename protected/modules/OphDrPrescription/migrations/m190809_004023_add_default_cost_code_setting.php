<?php

class m190809_004023_add_default_cost_code_setting extends OEMigration
{
    public function safeUp()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $id['id'],
            'key' => 'default_prescription_code_code',
            'name' => 'Default Prescription Cost Code',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "default_prescription_code_code"');
    }
}
