<?php
use OEModule\OESysEvent\components\ListenerBuilder;
use OEModule\OESysEvent\tests\test_traits\HasSysEventListenerAssertions;

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

/**
 * @group sample-data
 * @group event-draft
 * @group sys-events
 */
class EventDraftObserverTest extends OEDbTestCase
{
    use HasModelAssertions;
    use HasSysEventListenerAssertions;
    use WithTransactions;

    /** @test */
    public function created_method_is_triggered_for_event_created()
    {
        $clinical_event = Event::factory()->create();
        $this->fakeOtherEventListeners();

        $this->expectListenerWithMethod(EventDraftObserver::class, 'removeDraftForCreated');

        \Yii::app()->event->dispatch('event_created', ['event' => $clinical_event]);
    }

    /** @test */
    public function updated_method_is_triggered_for_event_updated()
    {
        $clinical_event = Event::factory()->create();
        $this->fakeOtherEventListeners();
        $this->expectListenerWithMethod(EventDraftObserver::class, 'removeDraftForUpdated');

        \Yii::app()->event->dispatch('event_updated', ['event' => $clinical_event]);
    }

    /** @test */
    public function only_removes_drafts_for_the_clinical_event_being_created_for_the_saving_user()
    {
        $user = User::factory()->create();

        $created_event = Event::factory()->create([
            'last_modified_user_id' => $user->id
        ]);

        // not being for an event marks draft as draft for newly created, and so should be removed
        // create 2 here to ensure we are resetting completely
        $drafts_to_be_deleted = EventDraft::factory()
            ->forEpisode($created_event->episode_id)
            ->forEventType($created_event->event_type_id)
            ->forUser($user)
            ->count(2)
            ->create();

        $draft_for_other_event = EventDraft::factory()
            ->forEpisode($created_event->episode_id)
            ->forEventType($created_event->event_type_id)
            ->forUser($user)
            ->forEvent() // by attaching to event, it should not be removed.
            ->create();

        $new_draft_for_other_user = EventDraft::factory()
            ->forEpisode($created_event->episode_id)
            ->forEventType($created_event->event_type_id)
            ->forUser()
            ->create();

        $observer = new EventDraftObserver();
        $observer->removeDraftForCreated(['event' => $created_event]);

        foreach ($drafts_to_be_deleted as $draft) {
            $this->assertModelDoesNotExist($draft);
        }

        $this->assertModelStillExists($draft_for_other_event);
        $this->assertModelStillExists($new_draft_for_other_user);
    }

    /** @test */
    public function only_removes_draft_for_the_clinical_event_being_updated_for_the_saving_user()
    {
        $user = User::factory()->create();

        $updated_event = Event::factory()->create([
            'last_modified_user_id' => $user->id
        ]);
        $other_event = Event::factory()->create([
            'episode_id' => $updated_event->episode_id,
            'event_type_id' => $updated_event->event_type_id,
            'last_modified_user_id' => $user->id
        ]);
        $draft_for_updated_event = EventDraft::factory()
            ->forEvent($updated_event)
            ->forUser($user)
            ->create();

        // not being for an event marks draft as draft for newly created, and so should not be removed
        $draft_for_new_event = EventDraft::factory()
            ->forEpisode($updated_event->episode_id)
            ->forEventType($updated_event->event_type_id)
            ->forUser($user)
            ->create();
        $draft_for_new_event_other_user = EventDraft::factory()
            ->forEpisode($updated_event->episode_id)
            ->forEventType($updated_event->event_type_id)
            ->forUser($user)
            ->create();
        $draft_for_other_event = EventDraft::factory()
            ->forEpisode($updated_event->episode_id)
            ->forEventType($updated_event->event_type_id)
            ->forUser($user)
            ->forEvent()
            ->create();

        $observer = new EventDraftObserver();
        $observer->removeDraftForUpdated(['event' => $updated_event]);

        $this->assertModelDoesNotExist($draft_for_updated_event);

        $this->assertModelStillExists($draft_for_new_event);
        $this->assertModelStillExists($draft_for_new_event_other_user);
        $this->assertModelStillExists($draft_for_other_event);
    }


    /**
     * Prevent other listeners/observers from firing that are related to the sys events under test
     */
    protected function fakeOtherEventListeners(): void
    {
        ListenerBuilder::fakeWith(PathstepObserver::class, $this->createMock(PathstepObserver::class));
    }
}
