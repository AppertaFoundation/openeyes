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

namespace OEModule\OphGeneric\components;

use EventSubtype;
use OE\concerns\InteractsWithApp;
use OEModule\OphGeneric\models\Attachment;
use OEModule\OphGeneric\models\Comments;
use OEModule\OphGeneric\models\DeviceInformation;
use OEModule\OphGeneric\models\HFA;
use OEModule\OphGeneric\OphGenericModule;

/**
 * A class to abstract event related functionality for use within OphGeneric
 */
class EventManager
{
    use InteractsWithApp;

    protected const MANUAL_ELEMENTS = [
        Comments::class,
        HFA::class,
        DeviceInformation::class // considered manual even though it doesn't provide form fields
    ];

    protected const EDITABLE_IMPORTING_ELEMENTS = [
        Comments::class
    ];

    protected static array $managersByEventId = [];

    protected ?\Event $event = null;
    protected ?\EventSubtype $event_subtype = null;

    public function __construct(?\Event $event = null, ?\EventSubtype $event_subtype = null)
    {
        $this->event = $event;
        $this->event_subtype = $event_subtype;
        if (!$this->event && !$this->event_subtype) {
            throw new \RuntimeException('Event or EventSubtype must be provided.');
        }

        if ($this->event) {
            $this->validateEvent($event);
        }
    }

    public static function forEvent(\Event $event): self
    {
        if (!$event->id) {
            return new self($event);
        }

        if (!array_key_exists($event->id, self::$managersByEventId)) {
            self::$managersByEventId[$event->id] = new self($event);
        }

        return self::$managersByEventId[$event->id];
    }

    public static function forEventId($event_id): self
    {
        return self::forEvent(\Event::model()->findByPk($event_id));
    }

    public static function forEventSubtype(\EventSubtype $event_subtype)
    {
        if (!$event_subtype) {
            throw new \RuntimeException('No subtype provided');
        }

        return new self(null, $event_subtype);
    }

    public static function forEventSubtypePk(string $pk)
    {
        $event_subtype =  \EventSubtype::model()->findByPk($pk);
        if (!$event_subtype) {
            throw new \RuntimeException('Could not find subtype');
        }

        return new self(null, $event_subtype);
    }

    public function isManualEvent(): bool
    {
        if (!$this->event) {
            return true;
        }

        // When manual events do support attachments, this check will need to be expanded to consider if the
        // attachment has a api request associated with it. This will depend on the exact implementation for
        // manual attachments, but this distinction will need to be considered.
        $attachments = Attachment::model()->findAll([
                'condition' => 'event_id=:event_id',
                'params' => [':event_id' => $this->event->id]
            ]);
        return count($attachments) < 1;
    }

    public function getElementTypes()
    {
        if ($this->event) {
            return $this->getElementTypesForEvent();
        }

        return $this->getElementTypesForEventSubtype();
    }

    public function getElements()
    {
        if ($this->event) {
            return $this->orderElementsByConfig($this->getElementsForEvent());
        }

        return $this->getElementsForEventSubtype();
    }

    public function elementTypeIsEditable(\ElementType $element_type): bool
    {
        $editable_classes = $this->isManualEvent() ? self::MANUAL_ELEMENTS : self::EDITABLE_IMPORTING_ELEMENTS;

        return in_array($element_type->class_name, $editable_classes);
    }

    public function getDisplayName(): ?string
    {
        return $this->getEventSubtype() ? $this->getEventSubtype()->display_name : null;
    }

    public function syncEventSubtypeFor(\Event $event): bool
    {
        if (!$this->event_subtype) {
            return false;
        }

        // sanity check if it's already synced
        if ($event->firstEventSubtypeItem) {
            $current_event_subtype = $event->firstEventSubtypeItem->event_subtype;
            // If they are not the same, we need to introduce support for changing the event subtype
            // or it has been called incorrectly
            if ($current_event_subtype !== $this->event_subtype->event_subtype) {
                $this->event->addError('event_subtype', 'Event subtype mismatch is not currently supported');
                return false;
            }
            return true;
        }

        $event_subtype_item = new \EventSubTypeItem();
        $event_subtype_item->event_id = $event->id;
        // note unusual attribute specification does not reference ids in event_subtype
        $event_subtype_item->event_subtype = $this->event_subtype->event_subtype;
        if (!$event_subtype_item->save()) {
            throw new \Exception('Unable to set event subtype on event: ' . print_r($event_subtype_item->getErrors(), true));
        };

        return true;
    }

    protected function getEventSubtype(): ?EventSubtype
    {
        if ($this->event_subtype) {
            return $this->event_subtype;
        }
        if (!$this->event) {
            return null;
        }

        return $this->event->firstEventSubtypeItem ? $this->event->firstEventSubtypeItem->eventSubtype : null;
    }

    protected function getElementsForEvent()
    {
        return $this->event->getElements();
    }

    protected function getElementTypesForEvent()
    {
        $criteria = new \CDbCriteria();
        $criteria->addInCondition(
            'class_name',
            array_map(function ($element) {
                return get_class($element);
            }, $this->getElementsForEvent())
        );

        return \ElementType::model()->findAll($criteria);
    }

    protected function getElementsForEventSubtype()
    {
        return \EventType::resolveElementClasses($this->getElementTypesForEventSubtype());
    }

    protected function getElementTypesForEventSubtype(): ?array
    {
        return $this->getEventSubtype() ? $this->getEventSubtype()->getElementTypes() : null;
    }

    protected function orderElementsByConfig(array $elements)
    {
        if (!$this->isManualEvent() || !$this->getEventSubtype()) {
            // maintain the base element configuration ordering
            return $elements;
        }

        $classes_in_order = array_map(function ($element_type) {
            return $element_type->class_name;
        }, $this->getElementTypesForEventSubtype());

        $result = [];
        foreach ($classes_in_order as $element_type_class) {
            $result = array_merge($result, array_filter($elements, function ($element) use ($element_type_class) {
                return get_class($element) === $element_type_class;
            }));
        }

        // final safety check to ensure that any elements on the event that have been stored and
        // are not longer ordered by config are put on the end.
        foreach ($elements as $element) {
            if (!in_array(get_class($element), $classes_in_order)) {
                $result[] = $element;
            }
        }

        return $result;
    }

    protected function validateEvent(\Event $event): void
    {
        // slightly weird situation where the \EventType class_name property doesn't actually align with the module
        if (strpos(OphGenericModule::class, $event->eventType->class_name) === false && strpos(\OphInBiometryModule::class, $event->eventType->class_name) === false) {
            throw new \RuntimeException('invalid event type for event manager ' . $event->eventType->class_name);
        }
    }
}
