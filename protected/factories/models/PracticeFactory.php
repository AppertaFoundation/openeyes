<?php

namespace OE\factories\models;

use Contact;
use OE\factories\ModelFactory;

class PracticeFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->asciify('####'),
            'phone' => $this->faker->phoneNumber(),
            'contact_id' => Contact::factory()
        ];
    }
}
