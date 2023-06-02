<?php

class m221003_135834_patient_identifiers_tooltip_setting extends CDbMigration
{
    public function safeUp()
    {
        $checkbox_field_type_id = $this->dbConnection
            ->createCommand('SELECT `id` FROM setting_field_type WHERE `name`="Radio buttons"')
            ->queryScalar();

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $checkbox_field_type_id,
            'key' => 'enable_patient_identifier_tooltip',
            'name' => 'Enable Patient Identifier tooltip',
            'description' => 'Enable Patient Identifier tooltip',
            'data' => serialize(['on' => 'On', 'off' => 'Off']),
            'default_value' => 'off',
            'group_id' => 15,
        ));
    }

    public function safeDown()
    {
        $this->delete(
            'setting_metadata',
            '`key` = :key',
            ['key' => 'enable_patient_identifier_tooltip']
        );
    }
}
