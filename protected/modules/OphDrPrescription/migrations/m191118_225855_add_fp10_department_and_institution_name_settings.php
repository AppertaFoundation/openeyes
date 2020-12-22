<?php

class m191118_225855_add_fp10_department_and_institution_name_settings extends OEMigration
{
    public function safeUp()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $id['id'],
            'key' => 'fp10_department_name',
            'name' => 'FP10/WP10 Department Name',
        ));

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $id['id'],
            'key' => 'fp10_institution_name',
            'name' => 'FP10/WP10 Institution Name',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "fp10_department_name"');
        $this->delete('setting_metadata', '`key` = "fp10_institution_name"');
    }
}
