<?php

class m210601_060238_add_ae_red_flags_exam_element_mt extends OEMTMigration
{
    protected function getLevelStructuredTables(): array
    {
        return array(
            'user' => array(),
            'firm' => array('ophciexamination_ae_red_flags_option'),
            'site' => array('ophciexamination_ae_red_flags_option'),
            'subspecialty' => array('ophciexamination_ae_red_flags_option'),
            'specialty' => array('ophciexamination_ae_red_flags_option'),
            'institution' => array('ophciexamination_ae_red_flags_option'),
        );
    }
}
