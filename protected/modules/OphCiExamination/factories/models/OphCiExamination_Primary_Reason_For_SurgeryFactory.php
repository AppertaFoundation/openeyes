<?php

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;

class OphCiExamination_Primary_Reason_For_SurgeryFactory extends ModelFactory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(10, true),
            'active' => true,
        ];
    }
}
