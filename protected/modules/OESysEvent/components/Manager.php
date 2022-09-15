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

namespace OEModule\OESysEvent\components;

use CApplicationComponent;
use OE\concerns\InteractsWithApp;
use OEMOdule\OESysEvent\contracts\Dispatchable;
use SebastianBergmann\GlobalState\RuntimeException;

/**
 * Abstraction to handle the observer configuration and map System Events to the defined
 * listeners from this configuration.
 *
 * Uses Yii conventions for the components for initialisation. The observers property is
 * maintained atm for backwards compatibility with the original OEEventManager setup.
 */
class Manager extends CApplicationComponent
{
    use InteractsWithApp;

    public array $observers = [];
    protected array $listeners = [];

    public function init()
    {
        foreach ($this->observers as $event_name => $observer_config) {
            if (is_int($event_name)) {
                // new config style
                $this->listen($observer_config['event'], $observer_config['listener']);
                continue;
            }

            foreach ($observer_config as $handler_id => $handler_config) {
                $this->listen($event_name, $handler_config['class'], $handler_config['method']);
            }
        }

        parent::init();
    }

    public function listen($events, ...$listener)
    {
        foreach ((array) $events as $event) {
            $this->listeners[$event][] = $this->makeListener(...$listener);
        }
    }

    public function dispatch($system_event): void
    {
        $listeners = is_string($system_event) ?
            $this->getListenersForEventString($system_event) :
            $this->getListenersFor($system_event);

        foreach ($listeners as $listener) {
            $listener($system_event);
        }
    }

    protected function makeListener($listener, $method = null): callable
    {
        if (is_callable($listener)) {
            return $listener;
        }

        if (is_string($listener)) {
            return $this->makeClassListener($listener, $method);
        }
        if (is_object($listener)) {
            return $this->wrapClassListener($listener, $method);
        }

        // TODO: make this more informative
        throw new RuntimeException('unable to configure listener');
    }

    protected function makeClassListener($listener_class, $method = null): callable
    {
        return function ($event) use ($listener_class, $method) {
            $listener = $this->getApp()->$listener_class ?? new $listener_class();

            return $method ? $listener->$method($event) : $listener($event);
        };
    }

    protected function wrapClassListener($listener_instance, $method = null)
    {
        return function ($event) use ($listener_instance, $method) {
            return $method ? $listener_instance->$method($event) : $listener_instance($event);
        };
    }

    protected function getListenersFor(Dispatchable $dispatchable): array
    {
        return $this->getListenersForEventString(get_class($dispatchable));
    }

    protected function getListenersForEventString(string $event_name): array
    {
        return $this->listeners[$event_name] ?? [];
    }
}
