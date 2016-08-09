<?php

class m140604_143200_more_substitution_strings extends OEMigration
{
    private $shortcodes = array(
        array('default_code' => 'cct', 'code' => 'cct', 'method' => 'getPrincipalCCT', 'description' => 'Examination CCT values for principal eye'),
        array('default_code' => 'vhp', 'code' => 'vhp', 'method' => 'getPrincipalVanHerick',
            'description' => 'Examination Van Herick grading value for principal eye', ),
        array('default_code' => 'opd', 'code' => 'opd', 'method' => 'getPrincipalOpticDiscDescription',
            'description' => 'Examination Optic Disc description field value for principal eye', ),
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
