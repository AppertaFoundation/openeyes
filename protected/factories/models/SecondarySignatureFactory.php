<?php

namespace OE\factories\models;
use OE\factories\ModelFactory;

class SecondarySignatureFactory extends ModelFactory
{

    public const SECONDARY_SIGNATORY_NAMES = [
        'Assessed by', 'Administered by', 'Verified by',
        'Instructed by', 'Evaluated by', 'Implemented by',
        'Confirmed by', 'Guided by', 'Supervised by',
        'Coordinated by', 'Overseen by', 'Facilitated by'
    ];

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(self::SECONDARY_SIGNATORY_NAMES),
            'institution_id' => null,
            'active' => 1,
        ];
    }

    public function withInstitution($institution): self
    {
        return $this->state([
            'institution_id' => $institution
        ]);
    }
}
