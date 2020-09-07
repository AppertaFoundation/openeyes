<?php

class m200507_055507_add_patient_short_codes extends OEMigration
{
    public function safeUp()
    {
        $this->insert('patient_shortcode', array(
            'event_type_id' => null,
            'default_code' => 'nhs',
            'code' => 'nhs',
            'method' => '',
            'description' => 'Patient NHS number',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => null,
            'default_code' => 'hos',
            'code' => 'hos',
            'method' => '',
            'description' => 'Patient Hospital number',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => null,
            'default_code' => 'fni',
            'code' => 'fni',
            'method' => '',
            'description' => 'Patient First Name Initial',
            'last_modified_user_id' => '1',
        ));
    }

    public function safeDown()
    {
        $this->delete('patient_shortcode', 'default_code = :default_code', array(':default_code' => 'nhs'));
        $this->delete('patient_shortcode', 'default_code = :default_code', array(':default_code' => 'hos'));
        $this->delete('patient_shortcode', 'default_code = :default_code', array(':default_code' => 'fni'));
    }
}
