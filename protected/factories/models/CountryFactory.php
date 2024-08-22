<?php

namespace OE\factories\models;
use OE\factories\ModelFactory;

class CountryFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->regexify('\w\w'),
            'name' => $this->faker->words(2, true)
        ];
    }
}
