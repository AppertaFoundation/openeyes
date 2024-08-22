<?php
use OEModule\OESysEvent\components\ListenerBuilder;
use OEModule\OESysEvent\events\ClinicalEventSoftDeletedSystemEvent;
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

use OE\listeners\RemoveDraftEventAfterSoftDelete;
use OEModule\OESysEvent\tests\test_traits\HasSysEventListenerAssertions;

/**
 * @group sample-data
 * @group event-draft
 * @group sys-events
 */
class RemoveDraftEventAfterSoftDeleteTest extends OEDbTestCase
{
    use HasModelAssertions;
    use HasSysEventListenerAssertions;
    use WithTransactions;

    /** @test */
    public function listener_is_triggered_for_event()
    {
        $clinical_event = Event::factory()->create();

        $this->expectListenerToBeInvoked(RemoveDraftEventAfterSoftDelete::class);

        ClinicalEventSoftDeletedSystemEvent::dispatch($clinical_event);
    }

    /** @test */
    public function only_removes_drafts_for_the_clinical_event_being_deleted()
    {
        $listener = new RemoveDraftEventAfterSoftDelete();
        $user = User::factory()->create();

        $event_to_delete = Event::factory()->create([
            'last_modified_user_id' => $user->id
        ]);
        $event_to_keep = Event::factory()->create([
            'episode_id' => $event_to_delete->episode_id,
            'event_type_id' => $event_to_delete->event_type_id,
            'last_modified_user_id' => $user->id
        ]);
        $draft_to_be_deleted = EventDraft::factory()->forEvent($event_to_delete)->forUser($user)->create();
        $draft_for_other_user = EventDraft::factory()->forEvent($event_to_delete)->forUser()->create();
        $draft_for_other_event = EventDraft::factory()->forEvent($event_to_keep)->forUser($user)->create();

        $event = new ClinicalEventSoftDeletedSystemEvent($event_to_delete);

        $listener($event);

        $this->assertModelDoesNotExist($draft_to_be_deleted);
        $this->assertModelStillExists($draft_for_other_user);
        $this->assertModelStillExists($draft_for_other_event);
    }
}
