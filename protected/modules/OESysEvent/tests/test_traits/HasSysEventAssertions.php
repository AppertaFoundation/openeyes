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

namespace OEModule\OESysEvent\tests\test_traits;

use InteractsWithFakedClasses;
use OEModule\OESysEvent\components\Manager as EventManager;

trait HasSysEventAssertions
{
    use InteractsWithFakedClasses;

    protected function fakeEvents(array|string|null $events = null)
    {
        if (is_string($events)) {
            $events = [$events];
        }

        EventManager::fake($events);
    }

    protected function assertEventDispatched(string $event_name, callable $callback = null): self
    {
        $this->assertTrue(EventManager::eventDispatched($event_name, $callback));

        return $this;
    }

    protected function assertEventNotDispatched(string $event_name, callable $callback = null): self
    {
        $this->assertFalse(EventManager::eventDispatched($event_name, $callback));

        return $this;
    }
}
