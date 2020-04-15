<?php

class m190726_003640_add_prescription_form_format_setting extends CDbMigration
{
    public function safeUp()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Dropdown list"')->queryRow();
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $id['id'],
            'key' => 'prescription_form_format',
            'name' => 'Prescription Form Format',
            'data' => serialize(array(
                'FP10' => 'FP10',
                'WP10' => 'WP10'
            )),
            'default_value' => 'FP10',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "prescription_form_format"');
    }
}
