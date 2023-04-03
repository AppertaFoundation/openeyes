<?php
namespace OEModule\CypressHelper\components;

use OE\concerns\InteractsWithApp;

abstract class BaseSeeder
{
    use InteractsWithApp;

    /**
     * The Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    protected $context;
    
    public function __construct(\DataContext $context)
    {
        $this->context = $context;
        $this->faker = $this->getApp()->dataGenerator->faker();
    }
}