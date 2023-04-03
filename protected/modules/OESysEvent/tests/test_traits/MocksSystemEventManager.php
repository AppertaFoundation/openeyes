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

trait MocksSystemEventManager
{
    protected $configured_event_manager = null;

    public function setUpMocksSystemEventManager()
    {
        $this->cacheExistingSystemEventManager();

        $this->teardownCallbacks(function () {
            $this->restoreCachedSystemEventManager();
        });
    }

    protected function mockSystemEventManager()
    {
        $mockManager = new class implements \IApplicationComponent {
            public array $dispatched = [];

            public function init()
            {
            }

            public function getIsInitialized()
            {
                return true;
            }

            public function dispatch(...$arguments)
            {
                $this->dispatched[] = $arguments;
            }

            /**
             * Gets the event instances that have been dispatched that are of the given class
             *
             * @param string $event_class
             * @return array
             */
            public function getDispatched(string $event_class): array
            {
                return array_map(
                    function ($dispatch_args) {
                        return $dispatch_args[0];
                    },
                    array_values(
                        array_filter($this->dispatched, function ($dispatch_args) use ($event_class) {
                            return is_object($dispatch_args[0]) && $dispatch_args[0] instanceof $event_class;
                        })
                    )
                );
            }
        };

        \Yii::app()->setComponent('event', $mockManager);

        return $mockManager;
    }

    protected function cacheExistingSystemEventManager()
    {
        $this->configured_event_manager = \Yii::app()->getComponent('event');
    }

    protected function restoreCachedSystemEventManager()
    {
        \Yii::app()->setComponent('event', $this->configured_event_manager);
    }
}
