<?php

namespace OE\factories\models;

use Contact;
use OE\factories\ModelFactory;
use Patient;
use PatientIdentifier;

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

    public function create($attributes = [])
    {
        // ensure this is the last after creating call so behaviour only invoked if
        // no state has been applied to make it unnecessary
        $this->afterCreating(function (Patient $instance) {
            if (!$instance->identifiers) {
                $this->generateDefaultIdentifiersFor($instance);
            }
        });

        return parent::create($attributes);
    }

    public function withIdentifierType($patient_identifier_type, $value = null): self
    {
        return $this->afterCreating(function (Patient $patient) use ($patient_identifier_type, $value) {
            $patient->identifiers = [
                PatientIdentifier::factory()
                    ->create([
                        'patient_identifier_type_id' => $patient_identifier_type,
                        'value' => $value ?? $this->faker->numerify('#######')
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

    protected function generateDefaultIdentifiersFor(Patient $patient): void
    {
        $patient->identifiers = [
            PatientIdentifier::factory()
                ->nhsNumber()
                ->create([
                    'patient_id' => $patient
                ]),
                PatientIdentifier::factory()
                ->localId()
                ->create([
                    'patient_id' => $patient
                ])
        ];
    }
}
