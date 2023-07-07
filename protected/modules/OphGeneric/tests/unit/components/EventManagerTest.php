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

namespace OEModule\OphGeneric\tests\unit\components;

use Event;
use EventSubtype;
use EventSubTypeItem;
use EventType;
use ElementType;
use OE\factories\models\EventFactory;
use OEModule\OphGeneric\components\EventManager;
use OEModule\OphGeneric\models\Assessment;
use OEModule\OphGeneric\models\DeviceInformation;
use OEModule\OphGeneric\models\HFA;
use OEModule\OphGeneric\models\Comments;

/**
 * Class EventManagerTest
 *
 * @package OEModule\OphCiExamination\tests\unit\components
 * @covers \OEModule\OphGeneric\components\EventManager
 * @group sample-data
 * @group ophgeneric
 * @group event-manager
 */
class EventManagerTest extends \OEDbTestCase
{
    use \WithFaker;
    use \HasModelAssertions;
    use \WithTransactions;

    /**
     * @test
     * @testWith    ["OphGeneric"]
     *              ["OphInBiometry"]
    */
    public function can_be_initialised_with_a_module_event($class_name)
    {
        $event = $this->createEventWithEventTypeClass($class_name);
        $event_manager = EventManager::forEvent($event);

        $this->assertNotNull($event_manager);
    }

    /** @test */
    public function can_be_initialised_with_an_event_subtype()
    {
        $event_subtype = EventSubtype::factory()->create([
            'event_subtype' => $this->faker->uuid() // to ensure uniqueness over extended test run
        ]);
        $event_manager = EventManager::forEventSubtypePk($event_subtype->event_subtype);

        $this->assertNotNull($event_manager);
    }

    /** @test */
    public function initialised_without_event_or_event_subtype_throws_exception()
    {
        $this->expectExceptionMessage('Event or EventSubtype must be provided.');

        new EventManager();
    }

    /** @test */
    public function invalid_event_type_throws_exception()
    {
        $class_name = $this->faker->word();

        $this->expectExceptionMessage('invalid event type for event manager ' . $class_name);

        $event_type = EventType::factory()->create([
            'class_name' => $class_name
        ]);

        $event = Event::factory()->create([
            'event_type_id' => $event_type->id
        ]);

        EventManager::forEvent($event);
    }

    /** @test */
    public function returns_expected_event_elements()
    {
        $event = EventFactory::forModule('OphGeneric')
            ->withElements([
                HFA::class,
                Comments::class
            ])
            ->create();

        $event_manager = EventManager::forEvent($event);

        $this->assertCount(2, $event_manager->getElements());
        $this->assertEquals($event->getElements(), $event_manager->getElements());
    }

    /** @test */
    public function returns_expected_event_subtype_elements()
    {
        $element_types = ElementType::factory()->useExisting()->count(rand(1, 4))->create();

        $mock_event_subtype = $this->createMock(EventSubtype::class);
        $mock_event_subtype->method('getElementTypes')
            ->willReturn($element_types);

        $event_manager = EventManager::forEventSubtype($mock_event_subtype);

        // because some element_types in the sample db are filtered by whether they are enabled or not
        // we simply test that all returned are in the expected list
        $valid_classnames = array_map(function ($element_type) {
            return $element_type->class_name;
        }, $element_types);

        foreach ($event_manager->getElements() as $element) {
            $this->assertTrue(in_array(get_class($element), $valid_classnames));
        }
    }

    /** @test  */
    public function get_display_name_returns_null_when_event_is_not_event_subtype()
    {
        $event = $this->createEventWithEventTypeClass();
        $event_manager = EventManager::forEvent($event);

        $this->assertNull($event_manager->getDisplayName());
    }

    /** @test  */
    public function get_display_name_returns_correct_string_when_event_is_event_subtype()
    {
        $expected_name = "Foo Bar";

        $event_subtype = EventSubtype::factory()->create([
            'event_subtype' => $this->faker->uuid(), // to ensure uniqueness over extended test run
            'display_name' => $expected_name
        ]);
        $event_manager = EventManager::forEventSubtypePk($event_subtype->event_subtype);

        $this->assertEquals($event_manager->getDisplayName(), $expected_name);
    }

    /** @test  */
    public function get_display_name_returns_correct_string_when_event_has_event_subtype()
    {
        $expected_name = "Foo Bar Baz";

        $event_subtype = EventSubtype::factory()->create([
            'event_subtype' => $this->faker->uuid(), // to ensure uniqueness over extended test run
            'display_name' => $expected_name
        ]);

        $event = $this->createEventWithEventTypeClass();

        $event_subtype_item = EventSubTypeItem::factory()->create([
            'event_id' => $event->id,
            'event_subtype' => $event_subtype->event_subtype
        ]);
        $event->eventSubtypeItems = [$event_subtype_item];

        $event_manager = EventManager::forEvent($event);

        $this->assertEquals($event_manager->getDisplayName(), $expected_name);
    }

    public function editableElementsProvider()
    {
        return [
            'comments_are_editable_for_automatic_elements' => [true, Comments::class, false],
            'core_visual_fields_elements_are_editable_for_manual_elements' => [true, [HFA::class, Comments::class], true],
            'other_elements_are_not_editable_for_automatic_elements' => [false, [Assessment::class, DeviceInformation::class], false]
        ];
    }

    /**
     * @test
     * @dataProvider editableElementsProvider
     */
    public function element_editable_is_affected_by_manual_state_of_event($should_be_editable, $element_type_classes, $for_manual_event)
    {
        $manager = $this->createPartialMock(EventManager::class, ['isManualEvent']);
        $manager->expects($this->any())
            ->method('isManualEvent')
            ->willReturn($for_manual_event);

        if (!is_array($element_type_classes)) {
            $element_type_classes = [$element_type_classes];
        }

        foreach ($element_type_classes as $element_type_class) {
            $element_type = ElementType::factory()->useExisting(['class_name' => $element_type_class])->create();

            $this->assertEquals($should_be_editable, $manager->elementTypeIsEditable($element_type), $element_type_class . ' editable state is not correct');
        }
    }

    protected function createEventWithEventTypeClass($class_name = 'OphGeneric'): Event
    {
        return $event = Event::factory()->create([
            'event_type_id' => EventType::factory()->create([
                'name' => $this->faker->uuid(), // to ensure uniqueness over extended test run
                'class_name' => $class_name
            ])->id
        ]);
    }
}
