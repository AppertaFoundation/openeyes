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

namespace OEModule\OphCiExamination\tests\unit\models\traits;

use OEModule\OphCiExamination\models\traits\HasChildrenWithEventScopeValidation;

/**
 * Class HasChildrenWithEventScopeValidationTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models\traits
 * @covers OEModule\OphCiExamination\models\traits\HasChildrenWithEventScopeValidation
 * @group sample-data
 * @group strabismus
 */
class HasChildrenWithEventScopeValidationTest extends \OEDbTestCase
{
    use \HasModelAssertions;

    public function setUp(): void
    {
        parent::setUp();

        $this->createTestTable('test_has_children_with_event_scope_validation', []);
    }

    /** @test */
    public function ignores_error_that_already_exists()
    {
        $relation_entry = $this->createMockActiveRecordExpectingEventScopeValidation();

        $relation_entry->method('getErrors')
            ->with('singleAttr')
            ->willReturn(['an error']);

        $instance = new HasChildrenWithEventScopeValidation_TestClass();
        $instance->a_relation_with_one_attribute = [$relation_entry];
        $instance->eventScopeValidation([]);
        $this->assertEmpty($instance->getErrors('a_relation_with_one_attribute.0'));
    }

    /** @test */
    public function adds_error_when_new_after_scope_check()
    {
        $relation_entry = $this->createMockActiveRecordExpectingEventScopeValidation();

        $relation_entry->method('getErrors')
            ->with('singleAttr')
            ->will($this->onConsecutiveCalls(['an error'], ['an error', 'new error']));

        $instance = new HasChildrenWithEventScopeValidation_TestClass();
        $instance->a_relation_with_one_attribute = [$relation_entry];
        $instance->eventScopeValidation([]);
        $this->assertAttributeHasError($instance, 'a_relation_with_one_attribute.0', 'new error');
    }

    /** @test */
    public function picks_up_error_for_multiple_attribute_entry()
    {
        $relation_entry = $this->createMockActiveRecordExpectingEventScopeValidation();

        $attribute2_call_count = 0;
        $relation_entry->method('getErrors')
            ->will($this->returnCallback(
                // error returned for second check of attribute2
                function () use (&$attribute2_call_count) {
                    if (func_get_args()[0] !== 'attribute2') {
                        return [];
                    }
                    if ($attribute2_call_count++ === 0) {
                        return [];
                    }
                    return ['an error message'];
                }
            ));

        $instance = new HasChildrenWithEventScopeValidation_TestClass();
        $instance->a_relation_with_two_attributes_to_check = [$relation_entry];
        $instance->eventScopeValidation([]);
        $this->assertAttributeHasError($instance, 'a_relation_with_two_attributes_to_check.0', 'an error message');
    }

    protected function createMockActiveRecordExpectingEventScopeValidation()
    {
        $ar = $this->getMockBuilder(\BaseActiveRecord::class)
            ->disableOriginalConstructor()
            ->setMethods(['getErrors', 'eventScopeValidation'])
            ->getMock();
        $ar->expects($this->exactly(1))
            ->method('eventScopeValidation');

        return $ar;
    }
}

class HasChildrenWithEventScopeValidation_TestClass extends \BaseEventTypeElement
{
    use HasChildrenWithEventScopeValidation;

    public $a_relation_with_one_attribute = [];
    public $a_relation_with_two_attributes_to_check = [];

    protected const EVENT_SCOPED_CHILDREN = [
        'a_relation_with_one_attribute' => 'singleAttr',
        'a_relation_with_two_attributes_to_check' => ['attribute1', 'attribute2']
    ];

    public function tableName()
    {
        return 'test_has_children_with_event_scope_validation';
    }
}
