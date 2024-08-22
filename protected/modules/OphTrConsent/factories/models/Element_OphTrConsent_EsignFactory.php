<?php


use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;

class Element_OphTrConsent_EsignFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphTrConsent'),
        ];
    }
}
