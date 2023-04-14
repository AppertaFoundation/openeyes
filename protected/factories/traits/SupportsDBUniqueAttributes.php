<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\traits;

use OE\factories\exceptions\CannotMakeModelException;

/**
 * This trait encapsulates behaviour for factories to ensure that a property is unique in the database
 * for the model being generated. When operating within transactions, this problem can typically be
 * relied upon to be handled by the unique() method on calls to faker.
 *
 * However, when using factories in non transactional generation (across front end testing with cypress for example)
 * the risk of collision with models that should have unique values is much higher. In those contexts, using
 * the helper states provided in this trait is recommended.
 *
 * $model = ModelClass::factory()
 *      ->withUniquePostfix($randomUniqueString)
 *      ->withDBUniqueAttribute('name')
 *      ->create();
 *
 * Note that any attribute must have a base value specification in the ModelFactory::definition() method
 * TODO: support length constraints for unique columns
 */
trait SupportsDBUniqueAttributes
{
    protected ?string $unique_postfix = null;
    protected static array $existing_values = [];

    public function withUniquePostfix($postfix)
    {
        $this->unique_postfix = $postfix;

        return $this;
    }
    public function withDBUniqueAttribute(string $attribute, $params = [])
    {
        return $this->state([
            $attribute => function () use ($attribute, $params) {
                return self::generateDBUniqueAttribute($attribute, $this->unique_postfix ?? '', $params, $this);
            }
        ]);
    }

    public static function generateDBUniqueAttribute(string $attribute, string $postfix = '', $params = [], $factory_instance = null)
    {
        $existing = self::getExistingValuesForAttribute($attribute);
        $factory_instance ??= self::new();

        $candidate_generator = $factory_instance->uniqueAttributeGenerator($attribute, array_merge(['postfix' => $postfix], $params));

        $value = $candidate_generator();

        while (in_array(strtolower($value), $existing)) {
            $new_value = $candidate_generator();
            if ($new_value === $value) {
                throw new CannotMakeModelException("unique value not generated for $attribute, got $new_value twice");
            }

            $value = $new_value;
        }

        // track new values - assume that any addiitional factory calls would want to maintain the uniqueness
        // across the set
        self::addExistingValueForAttribute($attribute, $value);

        return $value;
    }

    /**
     * Provides callback to generate candidate unique value for given attribute
     * Override in child classes to implement specific generators for
     *
     * @param string $attribute
     * @param array $params
     */
    public function uniqueAttributeGenerator(string $attribute, array $params = [])
    {
        return function () use ($attribute, $params) {
            $definition = $this->definition();
            if (!array_key_exists($attribute, $definition)) {
                throw new CannotMakeModelException("attribute $attribute not found in model definition");
            }
            return $definition[$attribute] . $params['postfix'] ?? '';
        };
    }


    protected static function getExistingValuesForAttribute($attribute)
    {
        if (!isset(self::$existing_values[$attribute])) {
            self::$existing_values[$attribute] = self::resolveModelName()::model()->findAll(['select' => $attribute]);
        }

        return self::$existing_values[$attribute];
    }

    protected static function addExistingValueForAttribute($attribute, $value)
    {
        self::$existing_values[$attribute] ??= [];
        self::$existing_values[$attribute][] = $value;
    }
}
