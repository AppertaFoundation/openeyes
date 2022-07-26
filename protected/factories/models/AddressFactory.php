<?php

namespace OE\factories\models;
use OE\factories\ModelFactory;
use Country;

class AddressFactory extends ModelFactory
{

    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'address1' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'postcode' => $this->faker->postcode,
            'country_id' => ModelFactory::factoryFor(Country::class)->useExisting(['code' => 'GB'])
        ];
    }
}
