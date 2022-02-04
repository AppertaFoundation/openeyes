<?php

class m210723_140612_update_elementType_consent_other extends OEMigration
{
    public function safeUp()
    {
        $this->update(
            'element_type',
            array('required' => 0, 'default' => 0),
            "class_name = 'Element_OphTrConsent_Other'"
        );
    }

    public function safeDown()
    {
        $this->update(
            'element_type',
            array('required' => 1, 'default' => 1),
            "class_name = 'Element_OphTrConsent_Other'"
        );
    }
}
