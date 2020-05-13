<?php

class m180907_024108_change_phaco_short_term extends OEMigration
{
    public function safeUp()
    {
        $this->update(
            'proc',
            array('short_format' => 'Phaco + IOL'),
            'snomed_code = :snomed_code',
            array(':snomed_code' => '415089008')
        );
    }

    public function safeDown()
    {
        $this->update(
            'proc',
            array('short_format' => 'Phaco/IOL'),
            'snomed_code = :snomed_code',
            array(':snomed_code' => '415089008')
        );
    }
}
