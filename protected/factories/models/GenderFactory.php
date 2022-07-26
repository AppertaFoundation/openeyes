<?php

namespace OE\factories\models;

use OE\factories\ModelFactory;

class GenderFactory extends ModelFactory
{

    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word
        ];
    }
}
