<?php

use OE\factories\ModelFactory;

class OphInBiometry_Calculation_FormulaFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name()
        ];
    }
}
