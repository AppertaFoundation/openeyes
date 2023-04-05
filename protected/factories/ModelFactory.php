<?php

/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories;

use CActiveRecord;
use CApplication;
use CDbCriteria;
use Faker\Generator;
use OE\factories\exceptions\CannotSaveModelException;
use OE\factories\exceptions\FactoryNotFoundException;
use OE\factories\exceptions\CannotMakeModelException;
use OE\factories\traits\MapsModelsToFormData;
use OELog;
use Yii;

abstract class ModelFactory
{
    use MapsModelsToFormData;

    public static $defaultModelNamespace = 'OE\\factories\\models\\';
    protected CApplication $app;
    protected Generator $faker;
    protected ?int $count = null;
    protected array $states = [];
    protected array $afterMaking = [];
    protected array $afterCreating = [];
    protected ?array $findOrCreateAttributes = null;

    final public function __construct($app = null)
    {
        if (is_null($app)) {
            $app = Yii::app();
        }
        $this->app = $app;
        $this->faker = $this->app->dataGenerator->faker();
    }

    abstract public function definition(): array;

    public static function new($attributes = [])
    {
        return (new static())->state($attributes)->configure();
    }

    public static function factoryFor(string $modelName)
    {
        $factoryClass = static::resolveFactoryName($modelName);

        return $factoryClass::new();
    }

    public static function resolveFactoryName(string $modelName)
    {
        if (method_exists($modelName, 'importNonNamespacedFactories')) {
            $modelName::importNonNamespacedFactories();
        }

        if (method_exists($modelName, 'factoryName')) {
            return $modelName::factoryName();
        }

        $possibleFactories = [
            static::$defaultModelNamespace . $modelName . 'Factory',
            $modelName . 'Factory',
            static::buildModuleFactoryName($modelName)
        ];

        foreach ($possibleFactories as $possibleFactoryClass) {
            if (class_exists($possibleFactoryClass)) {
                return $possibleFactoryClass;
            }
        };

        throw new FactoryNotFoundException("No Factory found for {$modelName}, tried:\n" . print_r($possibleFactories, true));
    }

    public static function buildModuleFactoryName($modelName)
    {
        if (preg_match('/^OEModule\\\\(\w+)\\\\models\\\\(\w+)$/', $modelName, $matches)) {
            return "OEModule\\{$matches[1]}\\factories\\models\\{$matches[2]}Factory";
        }
        return $modelName . "Factory";
    }

    /**
     * Placeholder method to allow factory implementations to define additional
     * default configuration (e.g. after* hooks)
     *
     * @return $this
     */
    public function configure()
    {
        return $this;
    }

    public function count(int $count)
    {
        $this->count = $count;

        return $this;
    }

    public function state($state)
    {
        $this->states[] = is_callable($state)
            ? $state
            : function () use ($state) {
                return $state;
            };

        return $this;
    }

    public function applyStates(array $states = [])
    {
        foreach ($states as $stateDefinition) {
            $this->applyStateDefinition($stateDefinition);
        }
        return $this;
    }

    protected function applyStateDefinition($definition)
    {
        if (!is_array($definition)) {
            $definition = [$definition];
        }

        if (is_string(array_keys($definition)[0])) {
            // looks like an attribute array
            return $this->state($definition);
        }

        $state = array_shift($definition);
        if (is_callable($state)) {
            return $this->state($state);
        }

        if (count($definition)) {
            return $this->$state(...$definition);
        }
        return $this->$state();
    }

    /**
     * Define attributes for the factory that should be used to check for an existing instance
     * otherwise setting defaults
     *
     * @param array $attributes
     * @return $this
     */
    public function useExisting($attributes = [])
    {
        $this->findOrCreateAttributes = array_merge($this->findOrCreateAttributes ?? [], $attributes);

        // apply as state so if no object is found the attributes are used in making
        return $this->state($attributes);
    }

    public function modelName()
    {
        // strip Factory from the class name
        $baseClass = substr(get_class($this), 0, -7);
        if (str_contains($baseClass, 'OEModule')) {
            return str_replace('factories\\', '', $baseClass);
        } else {
            return str_replace(static::$defaultModelNamespace, '', $baseClass);
        }
    }

    /**
     * Make model instance(s) based on the current factory state
     *
     * @param array $attributes
     * @param bool $canCreate
     * @return array|mixed
     */
    public function make(array $attributes = [], bool $canCreate = false)
    {
        if (!empty($attributes)) {
            return $this->state($attributes)->make([]);
        }

        if ($this->findOrCreateAttributes !== null) {
            $existing = $this->getExisting($this->getExpandedAttributes($this->findOrCreateAttributes));

            if (count($existing)) {
                if ($this->count === null) {
                    return $existing[array_rand($existing)];
                }
                if ($this->count === 1) {
                    // count should always return array
                    return [$existing[array_rand($existing)]];
                }

                return array_map(function ($key) use ($existing) {
                    return $existing[$key];
                }, array_rand($existing, $this->count));
            }
        }

        if ($this->count === null) {
            $instance = $this->newModel($this->getExpandedAttributes($this->getUnresolvedAttributes(), $canCreate));
            $this->callAfterMaking([$instance]);
            return $instance;
        }

        $instances = array_map(function () use ($canCreate) {
            return $this->newModel($this->getExpandedAttributes($this->getUnresolvedAttributes(), $canCreate));
        }, range(1, $this->count));

        $this->callAfterMaking($instances);

        return $instances;
    }

    /**
     * Create and persist the factory definition
     *
     * @param array $attributes
     * @return mixed|void
     */
    public function create(array $attributes = [])
    {
        if (!empty($attributes)) {
            return $this->state($attributes)->create([]);
        }

        $results = $this->make($attributes, true);

        if ($results instanceof \CModel) {
            $this->persist([$results]);
            $this->callAfterCreating([$results]);
        } else {
            $this->persist($results);
            $this->callAfterCreating($results);
        }

        return $results;
    }

    public function afterMaking($callback)
    {
        $this->afterMaking[] = $callback;

        return $this;
    }

    /**
     * Add a callback for generating related data after the model is created.
     *
     * @param $callback
     * @return $this
     */
    public function afterCreating($callback)
    {
        $this->afterCreating[] = $callback;

        return $this;
    }

    /**
     * Resolves the given definition of attributes to values that can be saved for the Model
     * being constructed
     *
     * @param array $definition
     * @param bool $canCreate
     * @return array
     */
    protected function getExpandedAttributes(array $definition, $canCreate = true): array
    {
        $expandAttributes = function ($value) use ($canCreate) {
            if ($value instanceof self) {
                if ($canCreate) {
                    return $value->create()->getPrimaryKey();
                }
                $modelName = $value->modelName();

                $value = $value->make()->getPrimaryKey();
                if (!$value) {
                    throw new CannotMakeModelException("Need to create {$modelName} in " . $this->modelName() . " factory");
                }
            } elseif ($value instanceof CActiveRecord) {
                $value = $value->getPrimaryKey();
            }

            return $value;
        };
        // first do any generic expansion
        $definition = array_map($expandAttributes, $definition);

        // then callbacks that may need to refer to the expanded
        // definition values
        foreach ($definition as $attribute => $value) {
            if (is_callable($value)) {
                $value = $value($definition);
            }
            // expand again in case any callback returns something
            // that needs to be expanded
            $definition[$attribute] = $expandAttributes($value);
        }

        return $definition;
    }

    /**
     * Work through all the states of the factory instance to provide a set of attribute
     * values to be resolved for model construction
     *
     * @return array
     */
    protected function getUnresolvedAttributes()
    {
        return array_reduce(
            $this->states,
            function ($carry, $state) {
                return array_merge($carry, $state($carry));
            },
            $this->definition()
        );
    }

    protected function newModel($attributes = [])
    {
        $modelName = $this->modelName();

        $instance = new $modelName();
        foreach ($attributes as $k => $v) {
            $instance->$k = $v;
        }
        return $instance;
    }

    protected function getExisting($attributes = [])
    {
        $modelName = $this->modelName();
        $criteria = new CDbCriteria();
        $criteria->order = 'RAND()';
        $criteria->limit = $this->count ?? 1;
        $criteria->addColumnCondition($attributes);

        return $modelName::model()->findAll($criteria);
    }

    /**
     * Save the given model instances to the db
     *
     * @param array $instances
     */
    protected function persist(array $instances): void
    {
        foreach ($instances as $instance) {
            if ($instance->isNewRecord && !$this->persistInstance($instance)) {
                throw new CannotSaveModelException($instance->getErrors(), $instance->getAttributes());
            }
        }
    }

    protected function persistInstance($instance): bool
    {
        // as a lower level interaction with the models, we assume that the eventual
        // set of data being created will be valid. Therefore we don't perform validation
        // during the model saves. We abstract this though so that specific factories can
        // validate.
        return $instance->save(false);
    }

    protected function callAfterMaking(array $instances): void
    {
        foreach ($instances as $instance) {
            foreach ($this->afterMaking as $callback) {
                $callback($instance);
            }
        }

        if (method_exists($this, 'mapDisplayOrderAttributes')) {
            // @see OE\factories\models\traits\MapsDisplayOrderForFactory
            $this->mapDisplayOrderAttributes($instances);
        }

        $this->castAttributes($instances);
    }

    protected function callAfterCreating(array $instances): void
    {
        foreach ($instances as $instance) {
            foreach ($this->afterCreating as $callback) {
                $callback($instance);
            }
        }
    }

    /**
     * This casts attributes that may be recorded directly in the db to
     * string so that they are consistent with how they will be set when
     * retrieved, and when instantiated through POST requests in the
     * application framework.
     *
     * @param array $instance
     * @return void
     */
    protected function castAttributes(array $instances): void
    {
        foreach ($instances as $instance) {
            foreach ($instance->getAttributes() as $attr => $value) {
                if (is_bool($value)) {
                    $instance->$attr = $value ? '1' : '0';
                } elseif (is_integer($value) || is_float($value)) {
                    $instance->$attr = (string) $value;
                }
            }
        }
    }
}
