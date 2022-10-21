<?php

class m220905_030136_add_pin_requirement_settings extends OEMigration
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
            'key' => 'require_pin_for_consent',
            'name' => 'Require PIN for Consent signing',
            'data' => serialize(['1' => 'Yes', '0' => 'No']),
            'default_value' => 1,
        ]);

        $this->insert('setting_installation', [
            'key' => 'require_pin_for_consent',
            'value' => 'Yes',
        ]);

        $this->insert('setting_metadata', [
            'field_type_id' => $radio_button_field_type_id,
            'key' => 'require_pin_for_correspondence',
            'name' => 'Require PIN for Correspondence signing',
            'data' => serialize(['1' => 'Yes', '0' => 'No']),
            'default_value' => 1,
        ]);

        $this->insert('setting_installation', [
            'key' => 'require_pin_for_correspondence',
            'value' => 'Yes',
        ]);

        $this->insert('setting_metadata', [
            'field_type_id' => $radio_button_field_type_id,
            'key' => 'require_pin_for_prescription',
            'name' => 'Require PIN for Prescription signing',
            'data' => serialize(['1' => 'Yes', '0' => 'No']),
            'default_value' => 1,
        ]);

        $this->insert('setting_installation', [
            'key' => 'require_pin_for_prescription',
            'value' => 'Yes',
        ]);

        $this->insert('setting_metadata', [
            'field_type_id' => $radio_button_field_type_id,
            'key' => 'require_pin_for_cvi',
            'name' => 'Require PIN for CVI signing',
            'data' => serialize(['1' => 'Yes', '0' => 'No']),
            'default_value' => 1,
        ]);

        $this->insert('setting_installation', [
            'key' => 'require_pin_for_cvi',
            'value' => 'Yes',
        ]);
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key`=`require_pin_for_consent_signing`');
        $this->delete('setting_metadata', '`key`=`require_pin_for_correspondence_signing`');
        $this->delete('setting_metadata', '`key`=`require_pin_for_prescription_signing`');
        $this->delete('setting_metadata', '`key`=`require_pin_for_cci_signing`');
    }
}
