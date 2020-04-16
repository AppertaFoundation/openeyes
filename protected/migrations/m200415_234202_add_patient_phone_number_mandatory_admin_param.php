<?php

class m200415_234202_add_patient_phone_number_mandatory_admin_param extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', [
            'field_type_id' => SettingFieldType::model()->find('name = ?', ["Radio buttons"])->id,
            'key' => 'patient_phone_number_mandatory',
            'name' => 'Patient Phone Number Mandatory',
            'data' => serialize(['1' => 'Yes', '0' => 'No']),
            'default_value' => 1,
        ]);

        $this->insert('setting_installation', [
            'key' => 'patient_phone_number_mandatory',
            'value' => 1,
        ]);
    }

    public function safeDown()
    {
        $this->delete('setting_installation', '`key`="patient_phone_number_mandatory"');
        $this->delete('setting_metadata', '`key`="patient_phone_number_mandatory"');
    }
}