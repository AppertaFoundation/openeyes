<?php
use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;

class FactoryForOperationnoteElement extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphTrOperationnote')
        ];
    }
}
