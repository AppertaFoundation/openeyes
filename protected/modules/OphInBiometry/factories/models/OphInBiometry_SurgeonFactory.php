<?php


use OE\factories\ModelFactory;

class OphInBiometry_SurgeonFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name()
        ];
    }
}
