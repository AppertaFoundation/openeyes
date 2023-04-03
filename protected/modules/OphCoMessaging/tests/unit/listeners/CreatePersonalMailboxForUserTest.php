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

namespace OEModule\OphCoMessaging\tests\unit\listeners;


use OEModule\OESysEvent\events\UserSavedSystemEvent;
use OEModule\OphCoMessaging\components\OphCoMessaging_API;
use OEModule\OphCoMessaging\listeners\CreatePersonalMailboxForUser;

class CreatePersonalMailboxForUserTest extends \OEDbTestCase
{
    use \WithTransactions;

    /** @test */
    public function uses_api_to_create_personal_mailbox()
    {
        $user = \User::factory()->create();

        $api = $this->createPartialMock(OphCoMessaging_API::class, ['createPersonalMailboxIfDoesNotExist']);
        $api->expects($this->once())
            ->method('createPersonalMailboxIfDoesNotExist')
            ->with(
                $this->callback(function (...$args) use ($user) {
                    return $args[0]->id == $user->id;
                })
            );


        $moduleAPI = $this->getMockBuilder('ModuleAPI')->disableOriginalConstructor()->getMock();
        $moduleAPI->expects($this->any())
            ->method('get')
            ->willReturn($api);

        \Yii::app()->setComponent('moduleAPI', $moduleAPI);

        $listener = new CreatePersonalMailboxForUser();
        $event = new UserSavedSystemEvent($user);
        $listener($event);
    }
}
