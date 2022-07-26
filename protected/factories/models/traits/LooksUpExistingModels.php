<?php

namespace OE\factories\models\traits;
use OE\factories\ModelFactory;

trait LooksUpExistingModels
{
    protected function mapToFactoryOrId($cls, $definition, $defaultDefinitionKey = 'name')
    {
        if (get_class($definition) === $cls) {
            return $definition->id;
        }

        if (is_string($definition)) {
            // map to default key
            $definition = [$defaultDefinitionKey => $definition];
        }

        if (is_null($definition) || is_array($definition)) {
            // use definition to constrain existing lookup - this will auto create if none found
            return ModelFactory::factoryFor($cls)->useExisting($definition);
        }

        throw new \InvalidArgumentException('unrecognised definition for factory mapping');
    }
}
