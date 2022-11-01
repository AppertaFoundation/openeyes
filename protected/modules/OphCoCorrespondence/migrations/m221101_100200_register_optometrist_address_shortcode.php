<?php

class m221101_100200_register_optometrist_address_shortcode extends OEMigration
{
    protected const SHORTCODE = 'pod';

    public function safeUp()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName('OphCoCorrespondence');
        $this->registerShortcode($event_type_id, self::SHORTCODE, 'getOptometristAddress', 'Optometrist\'s Address');
    }

    public function safeDown()
    {
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => self::SHORTCODE));
    }
}
