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

namespace OEModule\OphCoMessaging\tests\unit\components;

use OE\factories\ModelFactory;
use OEModule\OphCoMessaging\components\MailboxSearch;
use OEModule\OphCoMessaging\components\OphCoMessaging_API;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;

/**
 * class OphCoMessaging_APITest
 * @covers OEModule\OphCoMessaging\components\OphCoMessaging_API
 * @group shared-mailboxes
 * @group sample-data
 */
class OphCoMessaging_APITest extends \OEDbTestCase
{
    use \WithTransactions;
    use \WithFaker;

    private $api;

    private $total_message_count;
    private $mailbox_count;

    private $mailbox_user;
    private $mailboxes = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->api = new OphCoMessaging_API();

        $this->total_message_count = $this->faker->randomNumber(1);
        $this->mailbox_count = $this->faker->randomNumber(1);

        $this->setUpMailboxes();
        $this->populateMailboxMessages();
    }

    /** @test */
    public function total_message_count()
    {
        list($mailbox_message_counts, $total_message_count) = $this->api->getMessageCounts($this->mailbox_user, MailboxSearch::FOLDER_UNREAD_ALL);

        $this->assertEquals($this->total_message_count, $total_message_count, "API returned incorrect total message count for user");

        foreach ($mailbox_message_counts as $mailbox_message_count) {
            $message_count = $mailbox_message_count['all'];

            $mailbox = Mailbox::model()->findByPk($mailbox_message_count['id']);

            $this->assertEquals(count($mailbox->received_messages), $message_count, "API returned incorrect message count for mailbox");
        }
    }

    /** @test */
    public function user_mailboxes()
    {
        $user_mailboxes = array_map(function ($mailbox) {
            return $mailbox['id'];
        }, $this->api->getAllUserMailboxesById($this->mailbox_user));

        foreach ($this->mailboxes as $mailbox) {
            $this->assertContains($mailbox->id, $user_mailboxes, "API did not return a mailbox that was assigned to a user");
        }
    }

    /** @test */
    public function create_personal_mailbox_when_one_already_exists_does_not_create_additional_mailbox()
    {
        $user = \User::factory()->create();
        Mailbox::factory()->personalFor($user)->create();

        $this->assertCount(1, Mailbox::model()->forPersonalMailbox($user->id)->findAll());
        $this->api->createPersonalMailboxIfDoesNotExist($user);
        $this->assertCount(1, Mailbox::model()->forPersonalMailbox($user->id)->findAll());
    }

    /** @test */
    public function create_personal_mailbox_creates_one_for_user_when_none_exists()
    {
        // mock event manager to allow us to manually perform behaviours in test
        $user = \User::factory()->create();
        Mailbox::factory()->personalFor($user)->create();

        $this->assertCount(1, Mailbox::model()->forPersonalMailbox($user->id)->findAll());
        $this->api->createPersonalMailboxIfDoesNotExist($user);
        $this->assertCount(1, Mailbox::model()->forPersonalMailbox($user->id)->findAll());
    }

    private function setUpMailboxes()
    {
        $this->mailbox_user = ModelFactory::factoryFor(\User::class)->create();
        Mailbox::factory()->personalFor($this->mailbox_user)->create();

        $this->mailboxes = array_merge([Mailbox::model()->forPersonalMailbox($this->mailbox_user->id)->find()], Mailbox::factory()->withUsers([$this->mailbox_user])->count($this->mailbox_count)->create());
    }

    private function populateMailboxMessages()
    {
        for ($i = 0; $i < $this->total_message_count; $i++) {
            $recipient_mailbox = $this->mailboxes[rand(0, count($this->mailboxes) - 1)];

            Element_OphCoMessaging_Message::factory()
                ->withPrimaryRecipient($recipient_mailbox)
                ->create();
        }
    }
}
