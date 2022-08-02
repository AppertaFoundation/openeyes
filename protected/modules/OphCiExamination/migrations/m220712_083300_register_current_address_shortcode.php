<?php

class m220712_083300_register_current_address_shortcode extends OEMigration
{
    protected const SHORTCODE = 'pad';

    public function safeUp()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName('OphCoCorrespondence');
        $this->registerShortcode($event_type_id, self::SHORTCODE, 'getCurrentPatientAddress', 'Patient’s Home Address');
    }

    public function safeDown()
    {
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => self::SHORTCODE));
    }
}
