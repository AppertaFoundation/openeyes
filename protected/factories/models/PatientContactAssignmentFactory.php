<?php

namespace OE\factories\models;

use OE\factories\ModelFactory;
use \Patient;
use \Contact;

class PatientContactAssignmentFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory()->create(),
            'contact_id' => Contact::factory()->create(),
            'comment' => rand(0,1) ? $this->faker->words(rand(3,9), true) : null
        ];
    }
}
