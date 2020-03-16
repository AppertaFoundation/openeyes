<?php

use Faker\Factory;

trait WithFaker
{
    /**
     * The Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    public function setUpFaker()
    {
        $this->faker = Factory::create();
    }

}