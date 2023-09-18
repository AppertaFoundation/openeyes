<?php

namespace OEModule\OphTrConsent\models\factories;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;

class Element_OphTrConsent_ProcedureFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphTrConsent'),
        ];
    }
}
