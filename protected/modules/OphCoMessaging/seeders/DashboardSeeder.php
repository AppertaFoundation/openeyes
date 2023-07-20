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

namespace OEModule\OphCoMessaging\seeders;

use OE\seeders\BaseSeeder;
use OE\seeders\resources\GenericModelResource;
use OE\seeders\resources\SeededEventResource;
use OE\seeders\resources\SeededUserResource;
use OE\seeders\traits\CreatesAuthenticatableUsers;
use OELog;
use OEModule\OphCoMessaging\components\MailboxSearch;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;

class DashboardSeeder extends BaseSeeder
{
    use CreatesAuthenticatableUsers;

    public function __invoke(): array
    {
        [$user, $username, $password] = $this->createAuthenticatableUser($this->app_context->getSelectedInstitution());
        OELog::log(print_r([$user, $password], true));
        $messages = Element_OphCoMessaging_Message::factory()
            ->withPrimaryRecipient($user->personalMailbox)
            ->count(4)
            ->create();

        return [
            'user' => SeededUserResource::from($user, $password)->toArray(),
            'userMailbox' => GenericModelResource::from($user->personalMailbox)->toArray(),
            'messages' => array_map(function ($message) { return SeededEventResource::from($message->event)->toArray(); }, $messages),
            // random sample of folders for now
            'folders' => [MailboxSearch::FOLDER_ALL, MailboxSearch::FOLDER_READ_ALL, MailboxSearch::FOLDER_READ_CC, MailboxSearch::FOLDER_UNREAD_ALL, MailboxSearch::FOLDER_UNREAD_CC]
        ];
    }
}