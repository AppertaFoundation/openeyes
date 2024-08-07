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
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment;
use User;

/**
 * class MailboxSearchTest
 * @covers OEModule\OphCoMessaging\components\MailboxSearch
 * @group shared-mailboxes
 * @group sample-data
 */
class MailboxSearchTest extends \OEDbTestCase
{
    use \WithTransactions;
    use \WithFaker;

    private $sender_user;
    private $sender_mailbox;

    private $receiver_user;
    private $receiver_mailbox;

    private $other_user;
    private $other_mailbox;

    private $shared_mailbox;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpMailboxes();
    }

    /** @test */
    public function default_sort_order_is_latest_sent_date_descending()
    {
        $message_dates = [
            $this->faker->dateTimeBetween('-5 years', '-1 year'),
            $this->faker->dateTimeBetween('-11 months', '-10 months'),
            $this->faker->dateTimeBetween('-9 months', '-8 months'),
            $this->faker->dateTimeBetween('-5 months', '-3 months'),
            $this->faker->dateTimeBetween('-2 months', '-1 month')
        ];

        // create in random order to ensure not relying on PKs for sorting
        $message_ids = $this->sendMessagesInRandomOrder(
            array_map(function ($date) { return ['created_date' => $date->format('Y-m-d H:i:s')]; }, $message_dates)
        );
        $expected = array_reverse($message_ids);

        $results = $this->messageIdsFromSearch($this->receiver_user, MailboxSearch::FOLDER_ALL);

        $this->assertEquals($expected, $results);

        // now add a reply to a message that should pop it to the top of the list
        OphCoMessaging_Message_Comment::factory()
            ->withSender($this->receiver_mailbox)
            ->create([
                'element_id' => $expected[2],
                'created_date' => $this->faker->dateTimeBetween('-3 weeks', '-5 days')->format('Y-m-d H:i:s')
            ]);

        $updated_expected = $expected;
        unset($updated_expected[2]);
        $updated_expected = array_values($updated_expected);
        array_unshift($updated_expected, $expected[2]);

        $results = $this->messageIdsFromSearch($this->receiver_user, MailboxSearch::FOLDER_ALL);

        $this->assertEquals($updated_expected, $results);
    }

    /** @test */
    public function multiple_mailbox_unread()
    {
        $this->setUpMessagesWithDeletedEquivalents(function () {
            return [
                $this->makeMessageForOne($this->receiver_mailbox, false),
                $this->makeMessageForOne($this->receiver_mailbox, true),

                $this->makeMessageForOne($this->shared_mailbox, false),
                $this->makeMessageForOne($this->shared_mailbox, true)
            ];
        });


        $receiver_results = $this->searchAll($this->receiver_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $other_results = $this->searchAll($this->other_user, MailboxSearch::FOLDER_UNREAD_ALL);

        $this->assertCount(2, $receiver_results);
        $this->assertCount(1, $other_results);
    }

    /** @test */
    public function unread_not_read()
    {
        $this->setUpMessagesWithDeletedEquivalents(function () {
            return [
                $this->makeMessageForOne($this->receiver_mailbox, false),
                $this->makeMessageForOne($this->receiver_mailbox, true)
            ];
        });

        $results = $this->searchOne($this->receiver_user, $this->receiver_mailbox, MailboxSearch::FOLDER_UNREAD_ALL);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function read_not_unread()
    {
        $this->setUpMessagesWithDeletedEquivalents(function () {
            return [
                $this->makeMessageForOne($this->receiver_mailbox, true),
                $this->makeMessageForOne($this->receiver_mailbox, false)
            ];
        });

        $results = $this->searchOne($this->receiver_user, $this->receiver_mailbox, MailboxSearch::FOLDER_READ_ALL);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function unread_all()
    {
        $this->setUpMessagesWithDeletedEquivalents(function () {
            return [
                $this->makeMessageForOne($this->receiver_mailbox, false),
                $this->makeMessageForTwo($this->other_mailbox, false, $this->receiver_mailbox, false),
                $this->makeMessageWithReply($this->receiver_user, $this->receiver_mailbox, $this->sender_user, $this->sender_mailbox)
            ];
        });

        $results = $this->searchOne($this->receiver_user, $this->receiver_mailbox, MailboxSearch::FOLDER_UNREAD_ALL);

        $this->assertCount(3, $results);
    }

    /** @test */
    public function unread_to_primary_recipient()
    {
        $this->setUpMessagesWithDeletedEquivalents(function () {
            return [
                $this->makeMessageForOne($this->receiver_mailbox, false),
                $this->makeMessageForTwo($this->other_mailbox, false, $this->receiver_mailbox, false)
            ];
        });
        $results = $this->searchOne($this->receiver_user, $this->receiver_mailbox, MailboxSearch::FOLDER_UNREAD_TO_ME);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function unread_to_copy_recipient()
    {
        $this->setUpMessagesWithDeletedEquivalents(function () {
            return [
                $this->makeMessageForOne($this->other_mailbox, false),
                $this->makeMessageForTwo($this->other_mailbox, false, $this->receiver_mailbox, false)
            ];
        });

        $results = $this->searchOne($this->receiver_user, $this->receiver_mailbox, MailboxSearch::FOLDER_UNREAD_CC);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function unread_reply()
    {
        $this->setUpMessagesWithDeletedEquivalents(function () {
            return [
                $this->makeMessageWithReply($this->receiver_user, $this->receiver_mailbox, $this->sender_user, $this->sender_mailbox),
                $this->makeMessageForOne($this->receiver_mailbox, false)
            ];
        });

        $results = $this->searchOne($this->receiver_user, $this->receiver_mailbox, MailboxSearch::FOLDER_UNREAD_REPLIES);

        $this->assertCount(1, $results);
    }

    /** @test */
    public function unread_message_sent_to_self_is_in_unread_folder()
    {
        $this->setUpMessagesWithDeletedEquivalents(function () {
            return [
                $this->makeMessageForOne($this->sender_mailbox, false)
            ];
        });

        $results = $this->searchOne($this->sender_user, $this->sender_mailbox, MailboxSearch::FOLDER_UNREAD_TO_ME);

        $this->assertCount(1, $results);
    }

    private function setUpMailboxes()
    {
        $users = ModelFactory::factoryFor(\User::class)
            ->count(3)
            ->create();
        foreach ($users as $user) {
            Mailbox::factory()->personalFor($user)->create();
        }

        list($this->receiver_user, $this->sender_user, $this->other_user) = $users;

        $this->receiver_mailbox = Mailbox::model()->forPersonalMailbox($this->receiver_user->id)->find();
        $this->sender_mailbox = Mailbox::model()->forPersonalMailbox($this->sender_user->id)->find();
        $this->other_mailbox = Mailbox::model()->forPersonalMailbox($this->other_user->id)->find();

        $this->shared_mailbox = Mailbox::factory()
                              ->withUsers([$this->receiver_user, $this->other_user])
                              ->create();
    }

    private function makeMessageForOne(Mailbox $recipient, bool $marked_as_read): Element_OphCoMessaging_Message
    {
        return ModelFactory::factoryFor(Element_OphCoMessaging_Message::class)
                               ->withSender($this->sender_user, $this->sender_mailbox)
                               ->withPrimaryRecipient($recipient, $marked_as_read)
                               ->create();
    }

    private function makeMessageForTwo(Mailbox $to, bool $to_marked_as_read, Mailbox $cc, bool $cc_marked_as_read): Element_OphCoMessaging_Message
    {
        return ModelFactory::factoryFor(Element_OphCoMessaging_Message::class)
            ->withSender($this->sender_user, $this->sender_mailbox)
            ->withPrimaryRecipient($to, $to_marked_as_read)
            ->withCCRecipients([
                [$cc, $cc_marked_as_read]
            ])
            ->create();
    }

    private function makeMessageWithReply(\User $from_user, Mailbox $from_mailbox, \User $to_user, Mailbox $to_mailbox): Element_OphCoMessaging_Message
    {
        $message_query_type = OphCoMessaging_Message_MessageType::model()->find('name = "Query"');

        $message = ModelFactory::factoryFor(Element_OphCoMessaging_Message::class)
                 ->withMessageType($message_query_type)
                 ->withSender($from_user, $from_mailbox)
                 ->withPrimaryRecipient($to_mailbox, true)
                 ->create();

        $reply = ModelFactory::factoryFor(OphCoMessaging_Message_Comment::class)
               ->withElement($message)
               ->withUser($to_user)
               ->withSender($to_mailbox)
               ->create();

        return $message;
    }

    private function searchOne(\User $user, Mailbox $mailbox, string $folder): array
    {
        $search = new MailboxSearch($user, $folder);
        $messages = $search->retrieveMailboxContentsUsingSQL($user->id, [$mailbox->id])->getData();

        return $messages;
    }

    private function searchAll(\User $user, $folder): array
    {
        $search = new MailboxSearch($user, $folder);
        $messages = $search->retrieveMailboxContentsUsingSQL($user->id, null)->getData();

        return $messages;
    }

    /**
     * Convenience function to make sure that all query types respect the deleted state of
     * patient episodes and events.
     *
     * @param callable $callback
     */
    private function setUpMessagesWithDeletedEquivalents($callback): array
    {
        // generate messages to be kept:
        $generated = $callback();

        // then generate them but delete them at the event level
        foreach ($callback() as $message) {
            $this->deleteMessageEvent($message);
        }

        // generate again, but delete at the episode level
        foreach ($callback() as $message) {
            $this->deleteMessageEpisode($message);
        }

        return $generated;
    }

    private function deleteMessageEvent(Element_OphCoMessaging_Message $message)
    {
        $message->event->softDelete();
    }

    private function deleteMessageEpisode(Element_OphCoMessaging_Message $message)
    {
        $message->event->episode->deleted = 1;
        $message->event->episode->save();
    }

    private function sendMessagesInRandomOrder(array $message_attributes): array
    {
        $creation_indexes = array_keys($message_attributes);
        shuffle($creation_indexes);
        $index_to_message_id = [];

        foreach ($creation_indexes as $index) {
            $index_to_message_id[$index] = Element_OphCoMessaging_Message::factory()
                ->withPrimaryRecipient($this->receiver_mailbox, false)
                ->create($message_attributes[$index]);
        }

        return array_map(
            function ($index) use ($index_to_message_id) {
                return $index_to_message_id[$index]->id;
            },
            array_keys($message_attributes)
        );
    }

    private function messageIdsFromSearch(User $user, $folder): array
    {
        return array_map(
            function ($message) {
                return $message['element_id'];
            },
            $this->searchAll($user, $folder)
        );
    }
}
