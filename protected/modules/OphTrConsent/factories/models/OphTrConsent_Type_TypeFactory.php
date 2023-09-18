<?php


use OE\factories\ModelFactory;

class OphTrConsent_Type_TypeFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
        ];
    }
}
