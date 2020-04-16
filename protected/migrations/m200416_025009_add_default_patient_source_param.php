<?php

class m200416_025009_add_default_patient_source_param extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', [
            'field_type_id' => SettingFieldType::model()->find('name = ?', ["Dropdown list"])->id,
            'key' => 'default_patient_source',
            'name' => 'Default Patient Source',
            'data' => serialize(['0' => 'Other', '1' => 'Referral', '2' => 'Self-Registration']),
            'default_value' => 0,
        ]);

        $this->insert('setting_installation', [
            'key' => 'default_patient_source',
            'value' => 0,
        ]);
    }

    public function safeDown()
    {
        $this->delete('setting_installation', '`key`="default_patient_source"');
        $this->delete('setting_metadata', '`key`="default_patient_source"');
    }
}
