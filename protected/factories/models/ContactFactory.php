<?php

namespace OE\factories\models;

use OE\factories\ModelFactory;
use Address;
use ContactLabel;

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

    public function ofType($label)
    {
        return $this->state(function ($attributes) use ($label) {
            return [
                'contact_label_id' => ContactLabel::factory()->useExisting(['name' => $label])
            ];
        });
    }

    public function withCorrespondAddress() {
        return $this->afterCreating(function ($instance) {
            ModelFactory::factoryFor(Address::class)->create([
                'contact_id' => $instance->id,
                'address_type_id' => 3 // Correspondence address type
            ]);
        });
    }
}
