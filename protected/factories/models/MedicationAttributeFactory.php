<?php

namespace OE\factories\models;
use OE\factories\ModelFactory;

class MedicationAttributeFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->asciify('****_*******')
        ];
    }
}
