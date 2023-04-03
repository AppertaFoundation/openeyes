<?php
use OEModule\OESysEvent\events\UserSavedSystemEvent;
use OEModule\OESysEvent\tests\test_traits\MocksSystemEventManager;
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
 * @group user
 * @group system-events
 */
class UserWithSampleDataTest extends OEDbTestCase
{
    use WithTransactions;
    use MocksSystemEventManager;

    /** @test */
    public function user_saved_event_is_dispatched()
    {
        $event_manager = $this->mockSystemEventManager();

        $user = User::factory()->make();
        if (!$user->save()) {
            $this->fail(print_r($user->getErrors(), true));
        }

        $dispatched = $event_manager->getDispatched(UserSavedSystemEvent::class);
        $this->assertCount(1, $dispatched);
        $this->assertEquals($user, $dispatched[0]->user);
    }
}
