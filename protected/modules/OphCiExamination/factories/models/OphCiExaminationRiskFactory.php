<?php

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;

class OphCiExaminationRiskFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
