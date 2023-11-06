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

use OEModule\OESysEvent\events\ClinicalEventSoftDeletedSystemEvent;
use OEModule\OESysEvent\tests\test_traits\HasSysEventAssertions;

/**
 * @group sample-data
 */
class EventTest extends OEDbTestCase
{
    use HasModelAssertions;
    use HasSysEventAssertions;
    use WithTransactions;

    /**
     * @test
     * @group sys-events
     */
    public function soft_delete_triggers_the_appropriate_event()
    {
        $clinical_event = Event::factory()->create();
        $this->fakeEvents();

        $clinical_event->softDelete();

        $this->assertEventDispatched(
            ClinicalEventSoftDeletedSystemEvent::class,
            function (ClinicalEventSoftDeletedSystemEvent $event) use ($clinical_event) {
                return $event->clinical_event->id === $clinical_event->id;
            }
        );
    }

    /**
     * @test
     * @group workflow
     */
    public function event_cannot_be_saved_with_mismatched_worklist_patient()
    {
        $episode = Episode::factory()->create();
        $worklist_patient = WorklistPatient::factory()->create();

        $clinical_event = Event::factory()->make([
            'episode_id' => $episode->id,
            'worklist_patient_id' => $worklist_patient->id
        ]);

        $this->assertAttributeInvalid($clinical_event, 'worklist_patient_id', 'Mismatch');
    }
}
