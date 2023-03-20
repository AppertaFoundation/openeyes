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

namespace OE\factories\models;

use CDbCriteria;
use EventTemplate;
use EventTemplateUser;
use EventType;
use OE\factories\ModelFactory;

class EventTemplateFactory extends ModelFactory
{
    protected static ?array $available_event_types = null;

    public static function availableEventTypes(): array
    {
        if (is_null(self::$available_event_types)) {
            self::$available_event_types = self::getAvailableEventTypes();
        }

        return self::$available_event_types;
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'event_type_id' => $this->faker->randomElement(self::availableEventTypes()),
            'source_event_id' => function (array $attributes) {
                return EventFactory::forModule(self::getEventTypeById($attributes['event_type_id'])->class_name);
            }
        ];
    }

    public function create($attributes = [])
    {
        $this->afterCreating(function (EventTemplate $event_template) {
            if (!$event_template->user_assignment) {
                EventTemplateUser::factory()->create(['event_template_id' => $event_template->id]);
            } else {
                $event_template->user_assignment->event_template_id = $event_template->id;
                $event_template->user_assignment->save();
            }
        });

        return parent::create($attributes);
    }

    public function forUser($user): self
    {
        return $this->afterMaking(function (EventTemplate $event_template) use ($user) {
            $event_template->user_assignment = EventTemplateUser::factory()
                ->make([
                    'event_template_id' => null,
                    'user_id' => $user
                ]);
        });
    }

    protected static function getAvailableEventTypes(): array
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('template_class_name IS NOT NULL');

        return EventType::model()->findAll($criteria) ?? [];
    }

    protected static function getEventTypeById($id): EventType
    {
        if (!is_numeric($id)) {
            throw new \RuntimeException("Invalid id {$id} for extracting event type.");
        }
        $id = (int) $id;

        foreach (self::getAvailableEventTypes() as $event_type) {
            if ((int) $event_type->getPrimaryKey() === $id) {
                return $event_type;
            }
        }

        throw new \LogicException("invalid id {$id} for template event type.");
    }
}
