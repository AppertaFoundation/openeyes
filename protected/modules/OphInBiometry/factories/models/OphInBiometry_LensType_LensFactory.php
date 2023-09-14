<?php

use OE\factories\ModelFactory;

class OphInBiometry_LensType_LensFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name()
        ];
    }
}
