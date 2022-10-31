<?php
/**
 * (C) Apperta Foundation, 2022
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

namespace OEModule\OESysEvent\tests\unit\components;

use OEDbTestCase;
use OEModule\OESysEvent\components\Manager;
use OEModule\OESysEvent\events\SystemEvent;

/**
 * @group sample-data
 * @group sys-events
 */
class ManagerTest extends OEDbTestCase
{
    /** @test */
    public function supports_legacy_observer_configuration()
    {
        $manager = new Manager();
        $manager->observers = [
            'fake_event_name' => [
                'event_config_name' => [
                    'class' => LegacyEventHandler::class,
                    'method' => 'eventHandler'
                ]
            ],
        ];

        $manager->init();

        $manager->dispatch('fake_event_name', ['foo'], 'bar');
        $this->assertCount(1, LegacyEventHandler::$received_event_args);
        $this->assertEquals([['foo'], 'bar'], LegacyEventHandler::$received_event_args[0]);
    }

    /** @test */
    public function class_based_events_config_supported()
    {
        $manager = new Manager();
        $manager->observers = [
            [
                'event' => ExampleEvent::class,
                'listener' => InvokableEventHandler::class
            ]
        ];

        $manager->init();

        $test_event = new ExampleEvent();
        $manager->dispatch($test_event);

        $this->assertCount(1, InvokableEventHandler::$received_events);
        $this->assertEquals($test_event, InvokableEventHandler::$received_events[0]);
    }
}

class LegacyEventHandler
{
    public static array $received_event_args = [];

    public function eventHandler(...$arguments)
    {
        static::$received_event_args[] = $arguments;
    }
}

class ExampleEvent extends SystemEvent
{
}

class InvokableEventHandler
{
    public static array $received_events = [];

    public function __invoke($event)
    {
        static::$received_events[] = $event;
    }
}