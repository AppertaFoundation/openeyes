<?php

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;

class Element_OphTrConsent_TypeFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphTrConsent'),
            'type_id' => OphTrConsent_Type_Type::factory()->useExisting(),
            'draft' => 0,
            'print' => 0,
        ];
    }
}
