<?php

class m210831_071100_add_consent_show_patient_questions extends OEMigration
{
    public function safeUp()
    {
        $exists_meta_data = $this->dbConnection->createCommand()->select('id')->from('setting_metadata')->where('`key` = :setting_key', array(':setting_key' => 'consent_show_patient_questions'))->queryScalar();

        if (!$exists_meta_data) {
            $this->insert('setting_metadata', array(
                'element_type_id' => null,
                'display_order' => 22,
                'field_type_id' => 3,
                'key' => 'consent_show_patient_questions',
                'name' => 'Consent show patient questions',
                'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
                'default_value' => 'off'
            ));
            $this->insert('setting_installation', [
                'key' => 'consent_show_patient_questions',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = ?', array('consent_show_patient_questions'));
        $this->delete('setting_installation', '`key` = ?', array('consent_show_patient_questions'));
    }
}
