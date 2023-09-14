<?php

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;

class Element_OphInBiometry_IolRefValuesFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphInBiometry'),
            'eye_id' =>  ModelFactory::factoryFor(Eye::class)->useExisting(),
            // Need to use hardcoded name here for now until we can use useExisting with relations
            // otherwise sometimes we will get a lens which is not assigned to current institution
            'lens_id' => OphInBiometry_LensType_Lens::factory()->useExisting(['active' => 1, 'deleted' => 0,
                'name' => 'OptA119.1MA60AC']),
            'formula_id' => OphInBiometry_Calculation_Formula::factory()->useExisting(['deleted' => 0]),
            'surgeon_id' => OphInBiometry_Surgeon::factory()->useExisting(),
        ];
    }

    public function rightSideOnly()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::RIGHT
            ];
        });
    }

    public function leftSideOnly()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::LEFT
            ];
        });
    }

    public function bothSided()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::BOTH
            ];
        });
    }
}
