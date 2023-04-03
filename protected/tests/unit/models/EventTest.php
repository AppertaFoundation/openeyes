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
use OEModule\OESysEvent\tests\test_traits\MocksSystemEventManager;

/**
 * @group sample-data
 * @group system-events
 */
class EventTest extends OEDbTestCase
{
    use WithTransactions;
    use MocksSystemEventManager;

    /** @test */
    public function soft_delete_triggers_the_appropriate_event()
    {
        $event = Event::factory()->create();
        $manager_mock = $this->mockSystemEventManager();

        $event->softDelete();

        $dispatched = $manager_mock->getDispatched(ClinicalEventSoftDeletedSystemEvent::class);
        $this->assertCount(1, $dispatched);
        $this->assertEquals($event, $dispatched[0]->clinical_event);
    }
}
