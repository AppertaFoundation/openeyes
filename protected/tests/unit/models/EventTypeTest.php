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

 use OEModule\OphGeneric\models\HFA;

/**
 * @group sample-data
 * @group system-events
 * @group event-type
 */
class EventTypeTest extends OEDbTestCase
{
    use WithTransactions;

    /** @test */
    public function resolve_element_classes_returned_correct_class_instances()
    {
        $element_types = ElementType::factory()->useExisting()->count(rand(2, 4))->create();

        $resolved_elements = EventType::resolveElementClasses($element_types);

        // some existing element_types may no longer be enabled, so we simply check those that have returned
        $valid_classnames = array_map(function ($element_type) {
            return $element_type->class_name;
        }, $element_types);

        foreach ($resolved_elements as $element) {
            $this->assertTrue(in_array(get_class($element), $valid_classnames));
        }
    }

    /** @test */
    public function resolve_element_classes_doesnt_return_element_type_with_missing_class()
    {
        $element_types = ElementType::factory()->count(rand(2, 4))->create([
            'class_name' => 'EventTypeTest_Enabled'
        ]);
        $unexpected_element_type = ElementType::factory()->create([
            'class_name' => 'FooBar'
        ]);
        $element_types[] = $unexpected_element_type;

        $elements = EventType::resolveElementClasses($element_types);

        $this->assertEquals(count($element_types) - 1, count($elements));
        $this->assertNotContains(
            $unexpected_element_type->class_name,
            array_map(function ($element) {
                return get_class($element);
            }, $elements)
        );
    }

    /** @test */
    public function resolve_element_classes_correctly_filters_disabled_elements()
    {
        $expected_element_types = ElementType::factory()->count(rand(2, 4))->create([
            'class_name' => 'EventTypeTest_Enabled'
        ]);
        $unexpected_element_types = ElementType::factory()->count(rand(2, 4))->create([
            'class_name' => 'EventTypeTest_Disabled'
        ]);

        $elements = EventType::resolveElementClasses(array_merge($expected_element_types, $unexpected_element_types));

        $this->assertEquals(
            array_map(function ($element) {
                return $element->class_name;
            }, $expected_element_types),
            array_map(function ($element) {
                return get_class($element);
            }, $elements)
        );
    }
}

/**
 * Shell classes to test isEnabled functionality
 * extends HFA to picky back valid Active Record requirements
 */
class EventTypeTest_Enabled extends HFA
{
    public function isEnabled()
    {
        return true;
    }
}

class EventTypeTest_Disabled extends HFA
{
    public function isEnabled()
    {
        return false;
    }
}
