<?php

class m220830_144700_remove_consent_show_patient_questions_setting extends OEMigration
{
    public function safeUp()
    {
        $this->deleteSetting('consent_show_patient_questions');
    }

    public function SafeDown()
    {
        $field_type = $this->dbConnection->createCommand("SELECT id FROM setting_field_type WHERE `name` LIKE 'Radio buttons")->queryScalar();
        $this->insert('setting_metadata', [
            'key' => 'consent_show_patient_questions',
            'field_type_id' => $field_type,
            'name' => 'Consent Show Patient Questions',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',
            'lowest_setting_level' => 'INSTALLATION'
        ]);
    }
}
