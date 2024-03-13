<?php

namespace OE\factories;

use CApplicationComponent;
use Faker\Factory;
use Faker\Generator;
use RuntimeException;

/**
 * Simple abstraction to support singleton Faker definition in application context
 */
class DataGenerator extends CApplicationComponent
{
    public const DEFAULT_LOCALE = "en_GB";

    protected static array $fakers = [];
    protected static array $seeds = [];

    /**
     * Get a faker instance for data generation.
     */
    public function faker(string $locale = self::DEFAULT_LOCALE): Generator
    {
        static::$fakers[$locale] ??= $this->createAndSeed($locale);

        return static::$fakers[$locale];
    }

    public function getSeed(string $locale = self::DEFAULT_LOCALE): int
    {
        if (!array_key_exists($locale, static::$seeds)) {
            throw new RuntimeException("no faker instance used for $locale");
        }

        return static::$seeds[$locale];
    }

    protected function createAndSeed(string $locale): Generator
    {
        $generator = Factory::create($locale);
        static::$seeds[$locale] ??= mt_rand();
        //static::$seeds[$locale] ??= 1230601109;
        $generator->seed(static::$seeds[$locale]);

        return $generator;
    }
}
