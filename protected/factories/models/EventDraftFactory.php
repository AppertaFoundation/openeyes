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

use OE\factories\models\traits\HasEventTypeRelation;
use OE\factories\ModelFactory;
use EventDraft;
use Episode;
use Event;
use User;

class EventDraftFactory extends ModelFactory
{
    use HasEventTypeRelation;

    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'is_auto_save' => true,
            'institution_id' => \Institution::factory()->useExisting(),
            'site_id' => \Site::factory()->useExisting(),
            'episode_id' => Episode::factory(),
            'event_type_id' => $this->faker->randomElement($this->availableEventTypes()),
            'event_id' => null,
            'data' => '{}'
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (EventDraft $draft) {
            $draft->originating_url = $draft->event_id
                                    ? '/' . $draft->eventType->class_name . '/Default/update?id=' . $draft->event->id
                                    : '/' . $draft->eventType->class_name . '/Default/create?patient_id=' . $draft->episode->patient->id;

            $this->persistInstance($draft);
        });
    }

    /**
     * @param Episode|EpisodeFactory|string|int|null $episode
     * @return EventDraftFactory
     */
    public function forEpisode($episode = null): self
    {
        $episode = $episode ?? Episode::factory();

        return $this->state([
            'episode_id' => $episode
        ]);
    }

    /**
     * @param EventType|string|int
     * @return EventDraftFactory
     * */
    public function forEventType($event_type): self
    {
        return $this->state([
            'event_type_id' => $event_type
        ]);
    }

    public function forEventTypeByName($event_type_name): self
    {
        return $this->state([
            'event_type_id' => $this->getEventTypeByName($event_type_name)
        ]);
    }

    /**
     * @param Event|EventFactory|string|int|null $event
     * @return EventDraftFactory
     */
    public function forEvent($event = null): self
    {
        $event = $event ?? Event::factory();

        return $this->state(function ($attributes) use ($event) {
            if ($event) {
                return [
                    'event_id' => $event,
                    'event_type_id' => function ($attributes) {
                        return Event::model()->findByPk($attributes['event_id'])->eventType;
                    },
                    'episode_id' => function ($attributes) {
                        return Event::model()->findByPk($attributes['event_id'])->episode;
                    }
                ];
            }
            // we want an event based on draft properties
            return [
                'event_id' => function ($attributes) {
                    return Event::factory()->forEpisode($attributes['episode_id'])->forEventType($attributes['event_type_id']);
                }
            ];
        });
    }

    /**
     * @param User|UserFactory|OEWebUser|string|int|null $user
     * @return EventDraftFactory
     */
    public function forUser($user = null): self
    {
        $user = $user ?? new UserFactory();
        return $this->state([
            'last_modified_user_id' => $user
        ]);
    }

    /**
     * Override persistInstance to call save with its third parameter, $allow_overriding, set to true.
     * Setting that parameter to true ensures that the value of last_modified_user_id and/or created_user_id
     * that is provided is maintained.
     *
     * @param mixed $instance
     * @return bool
     */
    protected function persistInstance($instance): bool
    {
        return $instance->save(false, null, true);
    }
}
