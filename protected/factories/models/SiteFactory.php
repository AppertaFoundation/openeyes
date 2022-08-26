<?php

namespace OE\factories\models;

use OE\factories\ModelFactory;

class SiteFactory extends ModelFactory
{

    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'remote_id' => $this->faker->regexify('\w\w\w\d'),
            'short_name' => $this->faker->word(),
            'fax' => $this->faker->phoneNumber(),
            'telephone' => $this->faker->phoneNumber()
        ];
    }
}
