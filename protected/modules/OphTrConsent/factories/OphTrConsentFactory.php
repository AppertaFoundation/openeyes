<?php
namespace OEModule\OphTrConsent\factories;

use OE\factories\models\EventFactory;

class OphTrConsentFactory extends EventFactory
{
    public function definition(): array
    {
        return array_merge(
            parent::definition(),
            [
                'event_type_id' => $this->getEventTypeByName('Consent form')
            ]
        );
    }
}
