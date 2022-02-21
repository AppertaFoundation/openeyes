<?php

class m200325_114637_create_patient_identifiers_system_settings extends OEMigration
{
    public function safeUp()
    {
        $text_field_id = $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name="Text Field"')->queryScalar();
        $dropdown_field_id = $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name="Dropdown list"')->queryScalar();
        $data = ['LOCAL' => 'LOCAL', 'GLOBAL' => 'GLOBAL'];
        $this->insert('setting_metadata', [
            'field_type_id' => $dropdown_field_id,
            'key' => 'display_primary_number_usage_code',
            'name' => 'Display Primary Number Usage Code',
            'data' => serialize($data),
            'default_value' => 'LOCAL']);
        $this->insert('setting_metadata', [
            'field_type_id' => $dropdown_field_id,
            'key' => 'display_secondary_number_usage_code',
            'name' => 'Display Secondary Number Usage Code',
            'data' => serialize($data),
            'default_value' => 'GLOBAL']);
        $this->insert('setting_metadata', [
            'field_type_id' => $text_field_id,
            'key' => 'global_institution_remote_id',
            'name' => 'Global Institution Remote Id',
            'default_value' => 'NHS']);

        $this->insert('setting_installation', array(
            'key' => 'global_institution_remote_id',
            'value' => 'NHS'
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', 'setting_metadata.key="display_primary_number_usage_code"');
        $this->delete('setting_metadata', 'setting_metadata.key="display_secondary_number_usage_code"');
        $this->delete('setting_metadata', 'setting_metadata.key="global_institution_remote_id"');
        $this->delete('setting_installation', 'setting_installation.key="global_institution_remote_id"');
    }
}
