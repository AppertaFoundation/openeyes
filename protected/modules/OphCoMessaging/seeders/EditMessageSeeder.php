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

use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OE\seeders\resources\SeededEventResource;
use OE\seeders\BaseSeeder;

/**
* Seeder for generating data used solely in the test to verify desired behaviour of editing messages (messaging/edit.cy.js)
*/
class EditMessageSeeder extends BaseSeeder
{
    /**
    * Returns the data required to verify the desired behaviour of editing messages.
    * Return data includes:
    * - messageEvent - a message event with admin as sender and a user shared mailbox as the primary recipient and team shared mailbox as CC'd recipient
    * - messageEventRecipientsCount - the total number of recipients of the message
    * @return array
    */
    public function __invoke(): array
    {
        $admin_user = \User::model()->findByAttributes(['first_name' => 'admin']);

        // Seed two mailboxes, one for the primary recipient and one as a CC recipient
        $primary_mailbox = Mailbox::factory()
            ->withUsers(\User::factory()->count(2)->create())
            ->withUniqueMailboxName('Editing Test Primary Mailbox')
            ->create();

        $cc_mailbox = Mailbox::factory()
            ->withUsers(\User::factory()->count(2)->create())
            ->withUniqueMailboxName('Editing Test CC Mailbox')
            ->create();

        // Seed a message event with sender - admin - and recipients $primary_mailbox andd $cc_mailbox.
        $message = Element_OphCoMessaging_Message::factory()
            ->withSender($admin_user, Mailbox::model()->forPersonalMailbox($admin_user->id)->find())
            ->withPrimaryRecipient($primary_mailbox)
            ->withCCRecipients([[$cc_mailbox, false]])
            ->create();

        return [
            'messageEvent' => SeededEventResource::from($message->event)->toArray(),
            'messageEventRecipientCount' => count($message->recipients),
            'primaryMailboxName' => $primary_mailbox->name,
            'ccMailboxName' => $cc_mailbox->name
        ];
    }
}
