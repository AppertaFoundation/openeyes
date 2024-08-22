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

use OEModule\OESysEvent\components\ListenerBuilder;

trait HasSysEventListenerAssertions
{
    protected bool $event_manager_initialised = false;

    /**
     * Tests environment is setup with all listeners "forgotten"
     * This restores the listener configuration to support testing
     * that they are triggered by events.
     *
     * It sets up the teardown callback to reset back to the forgotten
     * state.
     *
     * @return void
     */
    protected function initialiseSysEvents()
    {
        if (!$this->event_manager_initialised) {
            \Yii::app()->event->init();
            $this->event_manager_initialised = true;

            $this->tearDownCallbacks(function () {
                \Yii::app()->event->forgetAll();
                $this->event_manager_initialised = false;
            });
        }
    }

    protected function expectListenerToBeInvoked(string $listener_class, ?callable $callback = null): void
    {
        $this->expectListenerWithMethod($listener_class, '__invoke', $callback);
    }

    protected function expectListenerWithMethod(string $listener_class, string $method, ?callable $callback = null): void
    {
        $this->initialiseSysEvents();

        $mock_listener = $this->createMock($listener_class);
        $expectation = $mock_listener->expects($this->once())
            ->method($method);

        if ($callback) {
            $expectation->with(self::callback($callback));
        }

        ListenerBuilder::fakeWith($listener_class, $mock_listener);
    }
}
