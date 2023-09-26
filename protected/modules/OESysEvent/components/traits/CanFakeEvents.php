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

namespace OEModule\OESysEvent\components\traits;

use FakedClassesTracker;
use Yii;

/**
 * Decoupling of faking behaviour for the system event manager to keep the base
 * code of the manager cleaner.
 *
 * Essentially provides some convenience wrappers for stubbing out and asserting
 * that events have been dispatched.
 */
trait CanFakeEvents
{
    public static function fake(?array $events = null): void
    {
        $manager = Yii::app()->event;
        $manager->fakeEvents($events);
    }

    public static function eventDispatched(string $event_name, $callback = null): bool
    {
        $manager = Yii::app()->event;

        return $manager->eventHasBeenFaked($event_name, $callback);
    }

    public function fakeEvents(?array $events = null): void
    {
        if ($events === null) {
            $this->setFakeEventConfig(true);
        } else {
            $this->setFakeEventConfig(false, $events);
        }
    }

    public function eventHasBeenFaked(string $event_name, $callback = null): bool
    {
        $recorded_events = $this->getFakeEventData()['recorded_events'] ?? [];

        if (!array_key_exists($event_name, $recorded_events)) {
            return false;
        }

        if ($callback === null) {
            return true;
        }

        foreach ($recorded_events[$event_name] as $event_name => $payload) {
            if ($callback(...$payload)) {
                return true;
            }
        }

        return false;
    }

    protected function shouldFakeEvent($event_name): bool
    {
        $settings = $this->getFakeEventData();
        if ($settings === null) {
            return false;
        }

        return $settings['fake_all'] || in_array($event_name, $settings['fake_events']);
    }

    protected function recordEvent($event_name, $payload): void
    {
        $current_fake_data = $this->getFakeEventData();
        $recorded_events = $current_fake_data['recorded_events'] ?? [];
        $recorded_events[$event_name] = array_merge(
            $recorded_events[$event_name] ?? [],
            [$payload]
        );

        $current_fake_data['recorded_events'] = $recorded_events;

        FakedClassesTracker::setFakeForClass(static::class, $current_fake_data);
    }

    protected function getFakeEventData(): ?array
    {
        return FakedClassesTracker::getFakeForClass(static::class);
    }

    protected function setFakeEventConfig(bool $fake_all, array $fake_events = []): void
    {
        $current_fake_data = $this->getFakeEventData();
        $current_fake_data['fake_all'] = $fake_all;
        $current_fake_data['fake_events'] = array_merge(
            $current_fake_data['fake_events'] ?? [],
            $fake_events
        );

        FakedClassesTracker::setFakeForClass(static::class, $current_fake_data);
    }
}
