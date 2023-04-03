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
use OEModule\OESysEvent\contracts\Dispatchable;
use OEModule\OESysEvent\contracts\Dispatcher;
use OEModule\OESysEvent\exceptions\UnrecognisedListenerConfigException;

/**
 * Abstraction to handle the observer configuration and map System Events to the defined
 * listeners from this configuration.
 *
 * Uses Yii conventions for the components for initialisation. The observers property is
 * maintained atm for backwards compatibility with the original OEEventManager setup.
 */
class Manager extends CApplicationComponent implements Dispatcher
{
    use InteractsWithApp;

    public array $observers = [];
    protected array $listeners = [];

    public function init()
    {
        // ensure we are starting from scratch
        $this->forgetAll();

        foreach ($this->observers as $event_name => $observer_config) {
            if (is_int($event_name)) {
                // new config style
                $this->listen($observer_config['system_event'], $observer_config['listener']);
                continue;
            }

            // legacy configuration indexed arrays by strings that have no relevance
            // to how the events are defined or handled, so this property is ignored
            foreach ($observer_config as $handler_id => $handler_config) {
                $this->listen($event_name, $handler_config['class'], $handler_config['method']);
            }
        }

        parent::init();
    }

    public function listen($events, ...$listener): void
    {
        foreach ((array) $events as $event) {
            $this->listeners[$event][] = $this->makeListener(...$listener);
        }
    }

    public function dispatch(...$arguments): void
    {
        if (is_string($arguments[0])) {
            // legacy dispatch pattern
            $listeners = $this->getListenersForEventString($arguments[0]);
            array_shift($arguments);
        } else {
            $listeners = $this->getListenersFor($arguments[0]);
        }

        foreach ($listeners as $listener) {
            $listener(...$arguments);
        }
    }

    public function forget($events): void
    {
        if (!is_array($events)) {
            $events = [$events];
        }

        foreach ($events as $event) {
            unset($this->listeners[$event]);
        }
    }

    public function forgetAll(): void
    {
        $this->listeners = [];
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

        throw new UnrecognisedListenerConfigException('received ' . gettype($listener) . ' can only accept strings or invokable classes');
    }

    /**
     * Creates a callback function that will use the given $listener_class
     * to retrieve a component class from the app, or new up the class if it not defined.
     *
     * If a method is provided, that method will be called with the arguments passed to the
     * callback. Otherwise the class will be invoked with those arguments.
     *
     * @param string $listener_class
     * @param ?string $method
     * @return callable
     */
    protected function makeClassListener($listener_class, $method = null): callable
    {
        return function (...$arguments) use ($listener_class, $method) {
            $listener = $this->getApp()->$listener_class ?? new $listener_class();

            return $method ? $listener->$method(...$arguments) : $listener(...$arguments);
        };
    }

    /**
     * Returns a callback function that will directly invoke the given the listener instance,
     * or (if provided) calls the given $method on that instance.
     *
     * @param object $listener_instance
     * @param string $method
     * @return callable
     */
    protected function wrapClassListener($listener_instance, $method = null): callable
    {
        return function (...$arguments) use ($listener_instance, $method) {
            return $method ? $listener_instance->$method(...$arguments) : $listener_instance(...$arguments);
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
