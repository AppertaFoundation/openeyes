<?php

use OE\factories\ModelFactory;

class LetterRecipientFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word()
        ];
    }

    public function forPatient(): self
    {
        return $this->useExisting(['name' => 'Patient']);
    }
}
