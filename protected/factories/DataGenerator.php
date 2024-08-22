<?php

namespace OE\factories;

use CApplicationComponent;
use Faker\Factory;
use Faker\Generator;

/**
 * Simple abstraction to support singleton Faker definition in application context
 */
class DataGenerator extends CApplicationComponent
{
    protected static array $fakers = [];

    /**
     * Get a faker instance for data generation.
     */
    public function faker($locale = "en_GB"): Generator
    {
        static::$fakers[$locale] ??= Factory::create($locale);

        return static::$fakers[$locale];
    }
}
