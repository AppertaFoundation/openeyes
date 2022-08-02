<?php

namespace OE\factories\models;

use Contact;
use OE\factories\ModelFactory;

class PatientFactory extends ModelFactory
{
    protected array $states = [];
    protected static array $identifiers = [];

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['M', 'F', 'U']);
        $contactFactory = ModelFactory::factoryFor(Contact::class);
        if ($gender === 'F') {
            $contactFactory->female();
        }
        if ($gender === 'M') {
            $contactFactory->male();
        }

        return [
            'dob' => $this->faker->dateTimeBetween('-100 years')->format('Y-m-d'),
            'gender' => $gender,
            'contact_id' => $contactFactory
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($instance) {
            $instance->identifiers = [
                ModelFactory::factoryFor(\PatientIdentifier::class)
                    ->nhsNumber()
                    ->create([
                        'patient_id' => $instance->id
                    ]),
                ModelFactory::factoryFor(\PatientIdentifier::class)
                    ->localId()
                    ->create([
                        'patient_id' => $instance->id
                    ])
            ];
        });
    }

    public function dead()
    {
        return $this->state(function ($attributes = []) {
            return [
                'date_of_death' => $this->faker->dateTimeBetween($attributes['dob'])->format('Y-m-d'),
                'is_deceased' => true
            ];
        });
    }

    public function female()
    {
        return $this->state(function () {
            return [
                'gender' => 'F',
                'contact_id' => ModelFactory::factoryFor(Contact::class)->female()
            ];
        });
    }

    public function male()
    {
        return $this->state(function () {
            return [
                'gender' => 'M',
                'contact_id' => ModelFactory::factoryFor(Contact::class)->male()
            ];
        });
    }

    public function usable()
    {
        return $this->afterCreating(function (\Patient $patient) {
            ModelFactory::factoryFor(\PatientIdentifier::class)
                ->nhsNumber()
                ->create([
                    'patient_id' => $patient->getPrimaryKey()
                ]);
            ModelFactory::factoryFor(\PatientIdentifier::class)
                ->localId()
                ->create([
                    'patient_id' => $patient->getPrimaryKey()
                ]);
        });
    }
}
