<?php

namespace OE\factories\models;

use OE\factories\ModelFactory;

class FirmFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word()
        ];
    }
}
