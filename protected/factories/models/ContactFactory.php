<?php

namespace OE\factories\models;

use OE\factories\ModelFactory;
use Address;

class ContactFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->title(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email()
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($instance) {
            ModelFactory::factoryFor(Address::class)->create([
                'contact_id' => $instance->id
            ]);
        });
    }

    public function female()
    {
        return $this->state(function ($attributes) {
            return [
            'title' => $this->faker->title('female'),
            'first_name' => $this->faker->firstNameFemale()
            ];
        });
    }

    public function male()
    {
        return $this->state(function ($attributes) {
            return [
                'title' => $this->faker->title('male'),
                'first_name' => $this->faker->firstNameMale()
            ];
        });
    }
}
