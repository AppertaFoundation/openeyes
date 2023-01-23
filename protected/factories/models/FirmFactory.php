<?php

namespace OE\factories\models;

use Institution;
use OE\factories\ModelFactory;
use Subspecialty;

class FirmFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'subspecialty_id' => Subspecialty::factory()->useExisting(),
            'institution_id' => Institution::factory()->useExisting()
        ];
    }
}
