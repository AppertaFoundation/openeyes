<?php

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;

class Element_OphInBiometry_SelectionFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphInBiometry'),
            'eye_id' =>  ModelFactory::factoryFor(Eye::class)->useExisting(),
        ];
    }

    public function forSidedPredictedRefraction($eye_id, $predicted_refraction): self
    {
        $side = strtolower(Eye::methodPostFix($eye_id));

        return $this->state([
            "predicted_refraction_" . $side  => $predicted_refraction,
        ]);
    }
}
