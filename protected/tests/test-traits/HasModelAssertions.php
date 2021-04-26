<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

trait HasModelAssertions
{
    /**
     * Simple abstraction for testing validation error on a model attribute
     *
     * @param $instance
     * @param $attribute
     * @param string $message_partial
     */
    protected function assertAttributeInvalid($instance, $attribute, $message_partial)
    {
        $this->assertFalse(
            $instance->validate(),
            "{$attribute} should not be valid with "
            . (empty($instance->$attribute) ? "no value" : "value " . print_r($instance->$attribute, true))
        );

        $this->assertAttributeHasError($instance, $attribute, $message_partial);
    }

    /**
     * @param $instance
     * @param $attribute
     * @param $message_partial
     */
    protected function assertAttributeHasError($instance, $attribute, $message_partial)
    {
        $this->assertNotEmpty(
            $instance->getErrors($attribute),
            "No errors found for {$attribute} in:\n" . print_r($instance->getErrors(), true)
        );

        $this->assertRegExp(
            "/" . preg_quote($message_partial, "/") . "/",
            $instance->getError($attribute)
        );
    }

    protected function assertAttributeValid($instance, $attribute)
    {
        $instance->validate();

        $this->assertEmpty(
            $instance->getErrors($attribute),
            "Not expecting errors for {$attribute}: with "
            . (empty($instance->$attribute) ? "no value" : "value " . $instance->$attribute)
            . print_r($instance->getErrors($attribute), true)
        );
    }

    public function assertRelationRuleDefined($instance, $attr, $cls)
    {
        $this->assertCount(
            1,
            array_filter(
                $instance->rules(),
                function ($rule) use ($attr, $cls) {
                    return (strpos($rule[0], $attr) !== false) && $rule[1] === 'exist' && $rule['className'] === $cls;
                }
            ),
            "Relation rule for attribute $attr is not defined for class $cls"
        );
    }

    public function assertDropdownOptionsHasCorrectKeys(array $options)
    {
        foreach ($options as $option) {
            $this->assertArrayHasKey('id', $option, 'missing id in ' . print_r($option, true));
            $this->assertArrayHasKey('name', $option, 'missing name in ' . print_r($option, true));
        }
    }

    public function assertMinValidation($instance, $attribute, $minimum)
    {
        if (!is_object($instance)) {
            $instance = new $instance();
        }

        $instance->$attribute = rand($minimum - rand(1, 100), $minimum - 1);
        $this->assertAttributeInvalid($instance, $attribute, "{$instance->getAttributeLabel($attribute)} is too small");

        $instance->$attribute = $minimum;
        $this->assertTrue($instance->validate([$attribute]));
    }

    public function assertMaxValidation($instance, $attribute, $maximum)
    {
        if (!is_object($instance)) {
            $instance = new $instance();
        }

        $instance->$attribute = rand($maximum + 1, $maximum + rand(1, 100));
        $this->assertAttributeInvalid($instance, $attribute, "{$instance->getAttributeLabel($attribute)} is too big");

        $instance->$attribute = $maximum;
        $this->assertTrue($instance->validate([$attribute]));
    }

    public function assertBelongsToCompletelyDefined($relation, $attribute, $cls)
    {
        $instance = $this->getElementInstance();
        $this->assertArrayHasKey($relation, $instance->relations());
        $this->assertRelationRuleDefined($instance, $attribute, $cls);
        $this->assertContains($attribute, $instance->getSafeAttributeNames());
        $this->assertOptionsAreRetrievable($instance, $relation, $cls);
    }

    public function assertHasManyDefined($relation, $cls, $attribute, $instance = null)
    {
        if ($instance === null) {
            $instance = $this->getElementInstance();
        }
        $relations = $instance->relations();
        $this->assertArrayHasKey($relation, $relations);
        $defined_relation = $relations[$relation];
        $this->assertEquals(CHasManyRelation::class, $defined_relation[0]);
        $this->assertEquals($cls, $defined_relation[1]);
        $this->assertEquals($attribute, $defined_relation[2]);
    }
}
