<?php

use OE\concerns\InteractsWithApp;

trait WithFaker
{
    use InteractsWithApp;

    /**
     * The Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    public function setUpWithFaker()
    {
        $this->faker = $this->getApp()->dataGenerator->faker();

        $this->tearDownCallbacks(function () { 
            $this->faker->unique(true);
        });
    }

    protected function getRandomNumberOfUniqueElements($elements, $min = 1)
    {
        return $this->faker->randomElements($elements, rand($min, count($elements)));
    }

    /**
     * Returns a fake refraction string
     * @return string
     */
    protected function fakeRefraction()
    {
        return $this->faker->regexify('[\+-]\d\.\d\d \/ [\+-]\d\.\d\d x \d\d');
    }
}
