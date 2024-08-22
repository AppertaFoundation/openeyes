<?php

namespace OE\factories\models;

use OE\factories\ModelFactory;
use Patient;

class PatientIdentifierFactory extends ModelFactory
{
    public static ?array $identifierTypes = null;

    public function definition(): array
    {
        return [
            'patient_id' => ModelFactory::factoryFor(Patient::class)
        ];
    }

    public function nhsNumber()
    {
        return $this->state([
            'patient_identifier_type_id' => $this->getIdentifierTypeByShortTitle('NHS'),
            'value' => $this->faker->unique()->numerify('### ### ####')
        ]);
    }

    public function localId()
    {
        return $this->state([
            'patient_identifier_type_id' => $this->getIdentifierTypeByShortTitle('ID'),
            'value' => $this->faker->unique()->numerify('#########')
        ]);
    }

    protected function getIdentifierTypeByShortTitle(string $title)
    {
        if (static::$identifierTypes === null) {
            $this->cacheIdentifierTypes();
        }

        return static::$identifierTypes[$title];
    }

    protected function cacheIdentifierTypes()
    {
        $cache = [];
        foreach (\PatientIdentifierType::model()->findAll() as $patientIdentifierType) {
            $cache[$patientIdentifierType->short_title] = $patientIdentifierType;
        }
        static::$identifierTypes = $cache;
    }
}
