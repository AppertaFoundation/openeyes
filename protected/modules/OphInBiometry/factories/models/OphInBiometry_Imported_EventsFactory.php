<?php

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;

class OphInBiometry_Imported_EventsFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphInBiometry'),
            'patient_id' => ModelFactory::factoryFor(Patient::class)
        ];
    }
}
