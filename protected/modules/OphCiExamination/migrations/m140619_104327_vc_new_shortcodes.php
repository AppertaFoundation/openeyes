<?php

class m140619_104327_vc_new_shortcodes extends OEMigration
{
    private $shortcodes = array(
            array('default_code' => 'ccl', 'code' => 'ccl', 'method' => 'getCCTLeft', 'description' => 'Examination CCT value for left eye'),
            array('default_code' => 'ccr', 'code' => 'ccr', 'method' => 'getCCTRight', 'description' => 'Examination CCT value for right eye'),
            array('default_code' => 'glr', 'code' => 'glr', 'method' => 'getGlaucomaRisk', 'description' => 'Get the most recent Glaucoma Risk value for the patient'),
    );

    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphCiExamination'))->queryRow();
        foreach ($this->shortcodes as $shortcode) {
            $shortcode = array_merge($shortcode, array('event_type_id' => $event_type['id']));
            $this->insert('patient_shortcode', $shortcode);
        }
    }

    public function down()
    {
        foreach ($this->shortcodes as $shortcode) {
            $this->delete('patient_shortcode', 'method = ?', array($shortcode['method']));
        }
    }
}
