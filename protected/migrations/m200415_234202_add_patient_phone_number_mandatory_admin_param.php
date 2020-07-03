<?php

class m200415_234202_add_patient_phone_number_mandatory_admin_param extends CDbMigration
{
    public function safeUp()
    {
        $radio_button_field_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where('name = :name', array(':name' => 'Radio buttons'))
            ->queryScalar();

        $this->insert('setting_metadata', [
            'field_type_id' => $radio_button_field_type_id,
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
