<?php

class m141007_114602_specific_eye_diagnosis_shortcode extends OEMigration
{
    public function safeUp()
    {
        $this->insert('patient_shortcode', array('default_code' => 'edl', 'code' => 'edl', 'description' => 'Left eye episode diagnosis'));
        $this->insert('patient_shortcode', array('default_code' => 'edr', 'code' => 'edr', 'description' => 'Right eye episode diagnosis'));
    }

    public function safeDown()
    {
        $this->delete('patient_shortcode', 'code = ?', array('edl'));
        $this->delete('patient_shortcode', 'code = ?', array('edr'));
    }
}
