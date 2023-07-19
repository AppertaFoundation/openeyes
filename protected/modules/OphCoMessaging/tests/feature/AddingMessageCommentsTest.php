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

namespace OEModule\OphCoMessaging\tests\feature;

use HasDatabaseAssertions;
use OEDbTestCase;
use OEModule\OphCoMessaging\components\MailboxSearch;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;
use OEModule\OphCoMessaging\tests\traits\MakesMessagingRequests;
use User;
use WithFaker;
use WithTransactions;

/**
 * class AddingMessageCommentsTest
 * @covers OEModule\OphCoMessaging\components\MailboxSearch
 * @covers OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message
 * @covers OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient
 * @group shared-mailboxes
 * @group sample-data
 */
class AddingMessageCommentsTest extends OEDbTestCase
{
    use HasDatabaseAssertions;
    use MakesMessagingRequests;
    use WithFaker;
    use WithTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockCurrentContext();
    }

    /** @test */
    public function can_mark_message_sent_to_self_as_read()
    {
        [$sender, $sender_mailbox] = $this->createMailboxUser();
        $message = Element_OphCoMessaging_Message::factory()
            ->withReplyRequired()
            ->sentToSelf()
            ->create([
                'sender_mailbox_id' => $sender_mailbox
            ]);

        $this->assertReadMessageCount(0, $sender);
        $this->assertUnreadMessageCount(1, $sender);
        $this->assertMessageCount(1, $sender);
        $this->assertMessageCount(1, $sender, MailboxSearch::FOLDER_UNREAD_TO_ME);
        $this->assertMessageCount(1, $sender, MailboxSearch::FOLDER_UNREAD_BY_RECIPIENT);
        $this->assertMessageCount(1, $sender, MailboxSearch::FOLDER_WAITING_FOR_REPLY);
        $this->assertCountQueryMatchesDataQuery($sender, $sender_mailbox);

        $this->markMessageReadWithRequest($message, $sender);

        $this->assertReadMessageCount(1, $sender);
        $this->assertUnreadMessageCount(0, $sender);
        $this->assertMessageCount(1, $sender);
        $this->assertMessageCount(0, $sender, MailboxSearch::FOLDER_UNREAD_TO_ME);
        $this->assertMessageCount(0, $sender, MailboxSearch::FOLDER_UNREAD_BY_RECIPIENT);
        $this->assertMessageCount(1, $sender, MailboxSearch::FOLDER_WAITING_FOR_REPLY);

        $this->assertCountQueryMatchesDataQuery($sender, $sender_mailbox);
    }

    /** @test */
    public function message_marked_unread_for_recipient_when_comment_is_added_by_sender()
    {
        [$sender, $sender_mailbox] = $this->createMailboxUser();
        $message_element = Element_OphCoMessaging_Message::factory()
            ->withReplyRequired()
            ->withPrimaryRecipient(null, true)
            ->create([
                'sender_mailbox_id' => $sender_mailbox
            ]);

        $recipient = OphCoMessaging_Message_Recipient::model()
            ->find('element_id = ?', [$message_element->id]);

        $this->assertTrue((bool) $recipient->marked_as_read);

        $this->postCommentWithRequestOn($message_element, $sender, $sender_mailbox);

        $this->assertDatabaseHas('ophcomessaging_message_comment', [
            'element_id' => $message_element->id,
            'mailbox_id' => $message_element->sender_mailbox_id
        ]);

        $recipient->refresh();

        $this->assertFalse((bool) $recipient->marked_as_read);

        $this->assertCountQueryMatchesDataQuery($sender, $sender_mailbox);
    }

    /** @test */
    public function message_marked_read_for_recipient_has_correct_counts_for_sender_and_primary()
    {
        [$sender_user, $sender_mailbox] = $this->createMailboxUser();
        [$primary_user, $primary_mailbox] = $this->createMailboxUser();
        [$secondary_user, $secondary_mailbox] = $this->createMailboxUser();

        $message_element = Element_OphCoMessaging_Message::factory()
            ->withSender($sender_user, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($primary_mailbox)
            ->withCCRecipients([[$secondary_mailbox, false]])
            ->create();

        $primary_recipient = OphCoMessaging_Message_Recipient::model()
            ->findByAttributes([
                'element_id' => $message_element->id,
                'mailbox_id' => $primary_mailbox->id
            ]);

        $secondary_recipient = OphCoMessaging_Message_Recipient::model()
            ->findByAttributes([
                'element_id' => $message_element->id,
                'mailbox_id' => $secondary_mailbox->id
            ]);

        // mark read for primary
        $this->markMessageReadWithRequest($message_element, $primary_user);

        $this->assertUnreadMessageCount(0, $primary_user, "unread count for primary should be zero after marking as read.");
        $this->assertUnreadMessageCount(1, $secondary_user, "unread count should still be 1 for cc after primary marking as read.");

        $this->postCommentWithRequestOn($message_element, $primary_user, $primary_mailbox);

        $this->assertUnreadMessageCount(1, $sender_user, "Sender unread count should be 1 after primary reply.");

        $this->markMessageReadWithRequest($message_element, $sender_user);

        $this->assertUnreadMessageCount(0, $sender_user, "Sender unread count should be 0 after marking as read.");

        $this->postCommentWithRequestOn($message_element, $sender_user, $sender_mailbox);

        $this->assertUnreadMessageCount(1, $primary_user, "unread count for primary should be 1 after sender replied.");
        $this->assertUnreadMessageCount(1, $secondary_user, "unread count for secondary should be 1 after sender replied.");

        $this->markMessageReadWithRequest($message_element, $primary_user);

        $this->assertUnreadMessageCount(0, $primary_user, "unread count for primary should be zero after marking as read again.");

        $this->assertUnreadMessageCount(1, $secondary_user, "unread count for secondary should remain 1 after primary marking as read.");

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function message_marked_read_will_update_message_read_state_correctly()
    {
        [$sender_user, $sender_mailbox] = $this->createMailboxUser();
        [$primary_user, $primary_mailbox] = $this->createMailboxUser();

        $message_element = Element_OphCoMessaging_Message::factory()
            ->withSender($sender_user, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($primary_mailbox)
            ->create();

            $this->assertUnreadMessageCount(1, $primary_user, "primary user should have unread message after test setup.");

        // mark read for primary
        $this->markMessageReadWithRequest($message_element, $primary_user);

        $this->assertUnreadMessageCount(0, $primary_user, "marking message read should leave primary user with no unread messages.");

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
    }

    /** @test */
    public function message_marked_read_by_cc_user_resets_to_unread_when_comments_are_made()
    {
        [$sender_user, $sender_mailbox] = $this->createMailboxUser();
        [$primary_user, $primary_mailbox] = $this->createMailboxUser();
        [$secondary_user, $secondary_mailbox] = $this->createMailboxUser();

        $message_element = Element_OphCoMessaging_Message::factory()
            ->withSender($sender_user, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($primary_mailbox)
            ->withCCRecipients([[$secondary_mailbox, false]])
            ->create();

        // arrangement sanity checks
        $this->assertUnreadMessageCount(
            1,
            $secondary_user,
            "message should initially be unread for cc user"
        );

        // mark read for secondary
        $this->markMessageReadWithRequest($message_element, $secondary_user);

        $this->assertUnreadMessageCount(
            0,
            $secondary_user,
            "cc user unread count should be zero after marking read"
        );

        //Comment as the primary recipient of the message
        $this->postCommentWithRequestOn($message_element, $primary_user, $primary_mailbox);

        $this->assertUnreadMessageCount(
            1,
            $secondary_user,
            "cc user should remain marked as read after recipient comment addition."
        );

        // mark read for secondary again
        $this->markMessageReadWithRequest($message_element, $secondary_user);

        $this->assertUnreadMessageCount(
            0,
            $secondary_user,
            "cc user unread count should be zero after marking read"
        );

        //Comment as the original sender
        $this->postCommentWithRequestOn($message_element, $sender_user, $sender_mailbox);

        $this->assertUnreadMessageCount(
            1,
            $secondary_user,
            "cc user should remain marked as read after sender comment addition."
        );

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox, null, "sender message counts should be consistent");
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox, null, "recipient message counts should be consistent");
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox, null, "cc message counts should be consistent");
    }

    /** @test */
    public function comment_added_to_message_by_primary_recipient_marks_message_unread_for_original_sender() {
        [$sender_user, $sender_mailbox] = $this->createMailboxUser();
        [$primary_user, $primary_mailbox] = $this->createMailboxUser();

        $message_element = Element_OphCoMessaging_Message::factory()
            ->withSender($sender_user, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($primary_mailbox)
            ->create();

        $primary_recipient = OphCoMessaging_Message_Recipient::model()
            ->findByAttributes([
                'element_id' => $message_element->id,
                'mailbox_id' => $primary_mailbox->id
            ]);

        $this->assertFalse((bool) $primary_recipient->marked_as_read);

        $this->assertUnreadMessageCount(1, $primary_user);
        $this->assertMessageCount(1, $primary_user);
        $this->assertUnreadMessageCount(0, $sender_user);
        $this->assertMessageCount(1, $sender_user);

        //post a comment on the message
        $this->postCommentWithRequestOn($message_element, $primary_user, $primary_mailbox);

        $this->assertUnreadMessageCount(1, $sender_user);

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
    }

    //sent message does not show in sender's unread mailbox
    /** @test */
    public function sent_message_does_not_show_in_sender_unread_mailbox()
    {
        list(
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            )
        ) = $this->sendMessage();

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $this->assertEmpty($data_provider->getData());

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
    }

    /** @test */
    public function sender_mailbox_counts_follow_expected_logic()
    {
        [$sender, $sender_mailbox] = $this->createMailboxUser();
        [$recipient, $recipient_mailbox] = $this->createMailboxUser();
        $message_count = 3;
        $messages = Element_OphCoMessaging_Message::factory()
            ->withSender($sender, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($recipient_mailbox, false)
            ->count($message_count)
            ->create();

        $this->assertMessageCount(3, $sender, MailboxSearch::FOLDER_STARTED_THREADS);
        $this->assertReadMessageCount(0, $sender, "sent messages are not included in read message total");
        $this->assertCountQueryMatchesDataQuery($sender, $sender_mailbox);

        $this->postCommentWithRequestOn($messages[0], $recipient);

        $this->assertMessageCount(3, $sender, MailboxSearch::FOLDER_STARTED_THREADS);

        $this->assertUnreadMessageCount(1, $sender, "a reply to a sent message should count to the unread message total for a sender.");
        $this->assertCountQueryMatchesDataQuery($sender, $sender_mailbox);

        $this->postCommentWithRequestOn($messages[0], $sender);

        $this->assertMessageCount(3, $sender, MailboxSearch::FOLDER_STARTED_THREADS);
        $this->assertReadMessageCount(1, $sender, "once a thread has responses, it should count toward the read total for a sender");
        $this->assertUnreadMessageCount(0, $sender, "being the last user to reply should mark the thread message as read for a sender");
        $this->assertCountQueryMatchesDataQuery($sender, $sender_mailbox);
    }

    //sent message is marked unread for each recipient
    // - for primary
    // - for cc
    /** @test */
    public function sent_message_is_marked_unread_for_each_recipient()
    {
        list(
            'recipients' => list(
                'primary' => list(
                    'recipient' => $primary_recipient
                ),
                'secondary' => list(
                    'recipient' => $secondary_recipient
                ),
            )
        ) = $this->sendMessage();

        $this->assertFalse((bool)$primary_recipient->marked_as_read);
        $this->assertFalse((bool)$secondary_recipient->marked_as_read);
    }

    /** @test */
    public function sent_message_shows_in_each_recipient_mailbox()
    {
        list(
            'recipients' => list(
                'primary' => list(
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                ),
                'secondary' => list(
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                ),
            )
        ) = $this->sendMessage();

        $this->assertUnreadMessageCount(1, $primary_user, "message sent to user should count as unread.");
        $this->assertUnreadMessageCount(1, $secondary_user, "message cc'd to user should count as unread.");

        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    //marking as read updates read status and shows in read mailbox
    // - for sender
    // - for primary recipient
    // - for cc recipient
    /** @test */
    public function marking_message_as_read_updates_is_reflected_in_read_and_unread_mailbox_counts()
    {
        list(
            'element' => $message_element,
            'recipients' => list(
                'primary' => list(
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                    'recipient' => $primary_recipient
                ),
                'secondary' => list(
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                    'recipient' => $secondary_recipient
                ),
            )
        ) = $this->sendMessage();

        $this->markMessageReadWithRequest($message_element, $primary_user);
        $this->assertUnreadMessageCount(0, $primary_user, "Message should not still be in unread folder for primary user");
        $this->assertReadMessageCount(1, $primary_user, "Message should appear in read folder when marked as read for primary user");
        $this->assertUnreadMessageCount(1, $secondary_user, "Message should still be in unread folder for cc user");
        $this->assertReadMessageCount(0, $secondary_user, "Message should not be in read folder for cc user when primary marks as read");

        $this->markMessageReadWithRequest($message_element, $secondary_user);

        $this->assertUnreadMessageCount(0, $secondary_user, "Message should not still be in unread folder for secondary user");
        $this->assertReadMessageCount(1, $secondary_user, "Message should appear in read folder when marked as read by cc user");

        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function marking_message_unread_shows_in_unread_mailbox()
    {
        list(
            'element' => $message_element,
            'recipients' => list(
                'primary' => list(
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                    'recipient' => $primary_recipient
                ),
                'secondary' => list(
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                    'recipient' => $secondary_recipient
                ),
            )
        ) = $this->sendMessage();

        $this->markMessageReadWithRequest($message_element, $primary_user);
        $this->markMessageReadWithRequest($message_element, $secondary_user);

        $this->markMessageUnreadWithRequest($message_element, $primary_user);

        $this->assertUnreadMessageCount(1, $primary_user, "primary users should have an unread message after marking unread.");

        $this->markMessageUnreadWithRequest($message_element, $secondary_user);
        $this->assertUnreadMessageCount(1, $secondary_user, "cc users should have an unread message after marking unread.");
    }

    //adding a comment marks message read for the sender of the comment
    // - adding comment as sender marks as read for sender
    // - adding comment as primary marks as read for primary
    // - adding comment as cc marks as read for cc
    /** @test */
    public function adding_a_comment_marks_message_read_for_the_sender_of_the_comment()
    {
        list(
            'element' => $element,
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            ),
            'recipients' => list(
                'primary' => list(
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                    'recipient' => $primary_recipient
                ),
                'secondary' => list(
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                    'recipient' => $secondary_recipient
                ),
            )
        ) = $this->sendMessage();

        $this->assertFalse((bool) $primary_recipient->marked_as_read);
        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $this->postCommentWithRequestOn($element, $primary_user, $primary_mailbox);

        $primary_recipient->refresh();
        $this->assertTrue((bool) $primary_recipient->marked_as_read);

        $this->postCommentWithRequestOn($element, $secondary_user, $secondary_mailbox);

        $secondary_recipient->refresh();
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);

        $this->assertUnreadMessageCount(1, $sender_user, "sender should have unread message after cc user replied");

        $this->postCommentWithRequestOn($element, $sender_user, $sender_mailbox);

        $this->assertUnreadMessageCount(0, $sender_user, "sender should not have unread message after replying");

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function cc_recipients_can_mark_sender_replies_read()
    {
        list(
            'element' => $element,
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            ),
            'recipients' => list(
                'primary' => list(
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                    'recipient' => $primary_recipient
                ),
                'secondary' => list(
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                    'recipient' => $secondary_recipient
                ),
            )
        ) = $this->sendMessage();

        $this->postCommentWithRequestOn($element, $sender_user, $sender_mailbox);

        $this->assertUnreadMessageCount(
            1,
            $secondary_user,
            "cc user should still be marked unread after first comment"
        );

        $this->markMessageReadWithRequest($element, $secondary_user);

        $this->assertUnreadMessageCount(
            0,
            $secondary_user,
            "cc user should be marked unread after request"
        );

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function message_is_unread_for_sender_when_comment_made_and_cc_recipients_can_mark_primary_replies_read()
    {
        list(
            'element' => $element,
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            ),
            'recipients' => list(
                'primary' => list(
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                    'recipient' => $primary_recipient
                ),
                'secondary' => list(
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                    'recipient' => $secondary_recipient
                ),
            )
        ) = $this->sendMessage();

        $this->postCommentWithRequestOn($element, $primary_user, $primary_mailbox);
        $this->assertUnreadMessageCount(
            1,
            $sender_user,
            "reply by recipient should mark message unread for sender"
        );

        $this->assertUnreadMessageCount(
            0,
            $primary_user,
            "reply by primary recipient should mark message read for them."
        );

        $this->assertUnreadMessageCount(
            1,
            $secondary_user,
            "cc should still have unread message after primary reply."
        );

        $this->markMessageReadWithRequest($element, $secondary_user);

        $this->assertUnreadMessageCount(
            0,
            $secondary_user,
            "cc should not have unread once marked as read."
        );

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function ping_pong_ping_replies_between_sender_and_primary()
    {
        list(
            'element' => $element,
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            ),
            'recipients' => list(
                'primary' => list(
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                    'recipient' => $primary_recipient
                ),
            )
        ) = $this->sendMessage();

        $this->postCommentWithRequestOn($element, $primary_user, $primary_mailbox);

        $this->assertUnreadMessageCount(
            1,
            $sender_user,
            "message should be unread for sender after reply added."
        );
        $this->assertUnreadMessageCount(
            0,
            $primary_user,
            "message should be read for primary recipient after they reply."
        );

        $this->assertMessageCount(1, $primary_user, MailboxSearch::FOLDER_ALL, null, "primary recipient should have message count when they replied to it.");
        $this->assertMessageCount(1, $primary_user);

        $this->postCommentWithRequestOn($element, $sender_user, $sender_mailbox);

        $this->assertUnreadMessageCount(
            0,
            $sender_user,
            "message should be read for sender after they reply."
        );

        $this->assertUnreadMessageCount(
            1,
            $primary_user,
            "message should be unread for primary recipient after sender replies."
        );

        $this->postCommentWithRequestOn($element, $primary_user, $primary_mailbox);

        $this->assertUnreadMessageCount(
            1,
            $sender_user,
            "message should be unread again for sender after further reply added."
        );
        $this->assertUnreadMessageCount(
            0,
            $primary_user,
            "message should be read for primary recipient after they reply again."
        );

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
    }

    /** @test */
    public function ping_pong_ping_replies_between_sender_and_cc()
    {
        list(
            'element' => $element,
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            ),
            'recipients' => list(
                'secondary' => list(
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                    'recipient' => $secondary_recipient
                ),
            )
        ) = $this->sendMessage();

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $this->assertCount(0, $sender_data_provider->getData());
        $this->assertCount(1, $secondary_data_provider->getData());
        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $this->postCommentWithRequestOn($element, $secondary_user, $secondary_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $secondary_recipient->refresh();

        $this->assertCount(1, $sender_data_provider->getData());
        $this->assertCount(0, $secondary_data_provider->getData());
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);

        $this->postCommentWithRequestOn($element, $sender_user, $sender_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $this->assertCount(0, $sender_data_provider->getData());
        $this->assertCount(1, $secondary_data_provider->getData());

        $this->postCommentWithRequestOn($element, $secondary_user, $secondary_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $this->assertCount(1, $sender_data_provider->getData());
        $this->assertCount(0, $secondary_data_provider->getData());

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function ping_pong_ping_ping_pong_replies_between_sender_primary_and_cc()
    {
        list(
            'element' => $element,
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            ),
            'recipients' => list(
                'primary' => list(
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                    'recipient' => $primary_recipient
                ),
                'secondary' => list(
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                    'recipient' => $secondary_recipient
                ),
            )
        ) = $this->sendMessage();

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $this->assertCount(0, $sender_data_provider->getData());
        $this->assertCount(1, $primary_data_provider->getData());
        $this->assertCount(1, $secondary_data_provider->getData());

        $this->assertFalse((bool) $primary_recipient->marked_as_read);
        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $this->postCommentWithRequestOn($element, $secondary_user, $secondary_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $primary_recipient->refresh();
        $secondary_recipient->refresh();

        $this->assertCount(1, $sender_data_provider->getData());
        $this->assertCount(1, $primary_data_provider->getData());
        $this->assertCount(0, $secondary_data_provider->getData());

        $this->assertFalse((bool) $primary_recipient->marked_as_read);
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);

        $this->postCommentWithRequestOn($element, $sender_user, $sender_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $this->assertCount(0, $sender_data_provider->getData());
        $this->assertCount(1, $primary_data_provider->getData());
        $this->assertCount(1, $secondary_data_provider->getData());

        $primary_recipient->refresh();
        $this->assertFalse((bool) $primary_recipient->marked_as_read);

        $this->postCommentWithRequestOn($element, $primary_user, $primary_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $this->assertCount(1, $sender_data_provider->getData());
        $this->assertCount(0, $primary_data_provider->getData());
        $this->assertCount(1, $secondary_data_provider->getData());

        $primary_recipient->refresh();
        $this->assertTrue((bool) $primary_recipient->marked_as_read);

        $this->postCommentWithRequestOn($element, $sender_user, $sender_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $this->assertCount(0, $sender_data_provider->getData());
        $this->assertCount(1, $primary_data_provider->getData());
        $this->assertCount(1, $secondary_data_provider->getData());

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function counting_matches_content_for_complex_message_chain()
    {
        [$sender_user, $sender_mailbox] = $this->createMailboxUser();
        [$primary_user, $primary_mailbox] = $this->createMailboxUser();
        [$secondary_user, $secondary_mailbox] = $this->createMailboxUser();

        $initial_messages = Element_OphCoMessaging_Message::factory()
            ->withSender($sender_user, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($primary_mailbox)
            ->withCCRecipients([[$secondary_mailbox, false]])
            ->count(5)
            ->create();

        $this->postCommentWithRequestOn($initial_messages[0], $primary_user, $primary_mailbox);
        $this->postCommentWithRequestOn($initial_messages[2], $primary_user, $primary_mailbox);

        $this->markMessageReadWithRequest($initial_messages[0], $secondary_user);
        $this->markMessageReadWithRequest($initial_messages[2], $secondary_user);

        $this->postCommentWithRequestOn($initial_messages[2], $sender_user, $sender_mailbox);

        $this->assertMessageCount(5, $primary_user);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
    }

    /** @test */
    public function three_way_usage_of_mailboxes()
    {
        [$top_user, $top_mailbox] = $this->createMailboxUser();
        [$left_user, $left_mailbox] = $this->createMailboxUser();
        [$right_user, $right_mailbox] = $this->createMailboxUser();

        // Primary - Sender - CC
        $top_sender_left_primary_right_cc_message = $this->createMessage($top_user, $top_mailbox, $left_mailbox, $right_mailbox);

        // CC - Sender - Primary
        $top_sender_right_primary_left_cc_message = $this->createMessage($top_user, $top_mailbox, $right_mailbox, $left_mailbox);

        // Sender - Primary - CC
        $left_sender_top_primary_right_cc_message = $this->createMessage($left_user, $left_mailbox, $top_mailbox, $right_mailbox);

        // Sender - CC - Primary
        $left_sender_right_primary_top_cc_message = $this->createMessage($left_user, $left_mailbox, $right_mailbox, $top_mailbox);

        // CC - Primary - Sender
        $right_sender_top_primary_left_cc_message = $this->createMessage($right_user, $right_mailbox, $top_mailbox, $left_mailbox);

        // Primary - CC - Sender
        $right_sender_left_primary_top_cc_message = $this->createMessage($right_user, $right_mailbox, $left_mailbox, $top_mailbox);

        $this->assertUnreadAndReadMessageCounts(4, 0, $top_user, "top at the start");
        $this->assertUnreadAndReadMessageCounts(4, 0, $left_user, "left at the start");
        $this->assertUnreadAndReadMessageCounts(4, 0, $right_user, "right at the start");

        // Right (as primary) posts a reply to the message sent by left
        $this->postCommentWithRequestOn($left_sender_right_primary_top_cc_message, $right_user, $right_mailbox);

        // Top stays 4 - 0, left moves to 5 - 0, right moves to 3 - 1
        $this->assertUnreadAndReadMessageCounts(4, 0, $top_user, "top which should not be affected by right replying to left");
        $this->assertUnreadAndReadMessageCounts(5, 0, $left_user, "left which should have right's reply in their unread folder");
        $this->assertUnreadAndReadMessageCounts(3, 1, $right_user, "right after replying as primary recipient to a message sent by left");

        // Left (as secondary) marks the message sent by top read
        $this->markMessageReadWithRequest($top_sender_right_primary_left_cc_message, $left_user);

        // Top stays on 4 - 0, left moves to 4 - 1, right stays 3 - 1
        $this->assertUnreadAndReadMessageCounts(4, 0, $top_user, "top which should not be affected by left marking a message sent by them read");
        $this->assertUnreadAndReadMessageCounts(4, 1, $left_user, "left after marking a message sent by top read as cc recipient");
        $this->assertUnreadAndReadMessageCounts(3, 1, $right_user, "right which should not be affected by left marking a message sent by top read");

        // Right (as primary) then posts a reply to the message sent by top
        $this->postCommentWithRequestOn($top_sender_right_primary_left_cc_message, $right_user, $right_mailbox);

        // Top moves to 5 - 0, left moves to 5 - 0, right moves to 2 - 2
        $this->assertUnreadAndReadMessageCounts(5, 0, $top_user, "top which should have right's reply in their unread folder");
        $this->assertUnreadAndReadMessageCounts(5, 0, $left_user, "left which should have right's reply in their unread folder as they previously marked the message read");
        $this->assertUnreadAndReadMessageCounts(2, 2, $right_user, "right after replying as primary recipient to a message sent by top");

        // Left (as primary) marks the message sent by top read
        $this->markMessageReadWithRequest($top_sender_left_primary_right_cc_message, $left_user);

        // Top stays on 5 - 0, left moves to 4 - 1,  right stays 2 - 2
        $this->assertUnreadAndReadMessageCounts(5, 0, $top_user, "top which should not be affected by left marking a message sent by them read");
        $this->assertUnreadAndReadMessageCounts(4, 1, $left_user, "left after marking a message sent by top read as primary recipient");
        $this->assertUnreadAndReadMessageCounts(2, 2, $right_user, "right which should not be affected by left marking a message sent by top read");

        // Right (as cc) marks the message sent by top read
        $this->markMessageReadWithRequest($top_sender_left_primary_right_cc_message, $right_user);

        // Top stays on 5 - 0, left stays on 4 - 1, right moves to 1 - 3
        $this->assertUnreadAndReadMessageCounts(5, 0, $top_user, "top which should not be affected by right marking a message sent by them read");
        $this->assertUnreadAndReadMessageCounts(4, 1, $left_user, "left which should not be affected by right marking a message sent by top read");
        $this->assertUnreadAndReadMessageCounts(1, 3, $right_user, "right after marking a message sent by top read as cc recipient");

        // Top (as sender) then posts a reply to the reply sent by right
        $this->postCommentWithRequestOn($top_sender_right_primary_left_cc_message, $top_user, $top_mailbox);

        // Top moves to 4 - 1, left stays on 4 - 1, right moves to 2 - 2
        $this->assertUnreadAndReadMessageCounts(4, 1, $top_user, "top after replying to a reply on a message it sent");
        $this->assertUnreadAndReadMessageCounts(4, 1, $left_user, "left which should not be affected by right replying to top");
        $this->assertUnreadAndReadMessageCounts(2, 2, $right_user, "right after receiving a reply back from top to a message sent by top");

        // TODO Enable once CC replies are a go - all the counts below for top will need to be adjusted once this is ready to be included
        // Top (as secondary) then posts a reply to the message sent by right
        // $this->postCommentWithRequestOn($right_sender_left_primary_top_cc_message, $top_user, $top_mailbox);

        // // Top moves to 3 - 1, left stays on 4 - 1, right moves to 3 - 2
        // $this->assertUnreadAndReadMessageCounts(3, 1, $top_user, "top after replying as cc recipient to a message sent by right");
        // $this->assertUnreadAndReadMessageCounts(4, 1, $left_user, "left which should not be affected by top replying to right");
        // $this->assertUnreadAndReadMessageCounts(3, 2, $right_user, "right after receing a reply from top on a message by right");

        // Left (as primary) then posts a reply to the message sent by right
        $this->postCommentWithRequestOn($right_sender_left_primary_top_cc_message, $left_user, $left_mailbox);

        // Top stays on 4 - 1, left moves to 3 - 2, right stays on 3 - 2 (as $top_cc_right_sender_left_primary receives both replies, status should change only once)
        $this->assertUnreadAndReadMessageCounts(4, 1, $top_user, "top which should not be affected by left replying to a message sent by right");
        $this->assertUnreadAndReadMessageCounts(3, 2, $left_user, "left which should not be affected by top replying to right");
        $this->assertUnreadAndReadMessageCounts(3, 2, $right_user, "right after receing a reply from left on the same message by right that already has a reply from top");

        // Left (as primary) marks the message sent by top read
        $this->markMessageUnreadWithRequest($top_sender_left_primary_right_cc_message, $left_user);

        // Top stays on 4 - 1, left moves to 4 - 1,  right stays 3 - 2
        $this->assertUnreadAndReadMessageCounts(4, 1, $top_user, "top which should not be affected by left marking a message sent by them read");
        $this->assertUnreadAndReadMessageCounts(4, 1, $left_user, "left after marking a message sent by top unread as primary recipient");
        $this->assertUnreadAndReadMessageCounts(3, 2, $right_user, "right which should not be affected by left marking a message sent by top read");

        // Top (as sender) then posts a reply to the message sent by itself
        $this->postCommentWithRequestOn($top_sender_left_primary_right_cc_message, $top_user, $top_mailbox);

        // Top moves to 4 - 2, left stays on 4 - 1, right moves to 4 - 1
        $this->assertUnreadAndReadMessageCounts(4, 2, $top_user, "top after replying to a message it sent");
        $this->assertUnreadAndReadMessageCounts(4, 1, $left_user, "left which should not be affected by right replying to top");
        $this->assertUnreadAndReadMessageCounts(4, 1, $right_user, "right after receiving a reply back from top to a message sent by top");

        $this->assertCountQueryMatchesDataQuery($top_user, $top_mailbox);
        $this->assertCountQueryMatchesDataQuery($left_user, $left_mailbox);
        $this->assertCountQueryMatchesDataQuery($right_user, $right_mailbox);
    }

    /**
     * Helper abstraction to build out a standard thread based message for the given
     * senders and recipients
     */
    protected function createMessage(User $sender_user, Mailbox $sender_mailbox, Mailbox $primary_mailbox, Mailbox $secondary_mailbox, ?int $count = null)
    {
        $factory = Element_OphCoMessaging_Message::factory()
            ->withSender($sender_user, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($primary_mailbox)
            ->withCCRecipients([[$secondary_mailbox, false]]);

        if ($count !== null) {
            $factory->count($count);
        }

        return $factory->create();
    }

    /**
     * Generic starting point abstraction to setup a message and return the particpants for testing behaviour
     */
    protected function sendMessage(): array
    {
        [$sender_user, $sender_mailbox] = $this->createMailboxUser();
        [$primary_user, $primary_mailbox] = $this->createMailboxUser();
        [$secondary_user, $secondary_mailbox] = $this->createMailboxUser();

        $message_element = $this->createMessage($sender_user, $sender_mailbox, $primary_mailbox, $secondary_mailbox);

        $primary_recipient = OphCoMessaging_Message_Recipient::model()
            ->findByAttributes([
                'element_id' => $message_element->id,
                'mailbox_id' => $primary_mailbox->id
            ]);

        $secondary_recipient = OphCoMessaging_Message_Recipient::model()
            ->findByAttributes([
                'element_id' => $message_element->id,
                'mailbox_id' => $secondary_mailbox->id
            ]);

        return [
            'element' => $message_element,
            'sender' => [
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            ],
            'recipients' => [
                'primary' => [
                    'user' => $primary_user,
                    'mailbox' => $primary_mailbox,
                    'recipient' => $primary_recipient
                ],
                'secondary' => [
                    'user' => $secondary_user,
                    'mailbox' => $secondary_mailbox,
                    'recipient' => $secondary_recipient
                ],
            ],
        ];
    }

    protected function assertUnreadMessageCount(
        int $count,
        User $user,
        string $message = "unread message count for user is incorrect",
        null|Mailbox|array $mailbox = null
    ) {
        $this->assertMessageCount(
            $count,
            $user,
            MailboxSearch::FOLDER_UNREAD_ALL,
            $mailbox ?? $user->personalMailbox,
            $message
        );
    }

    protected function assertReadMessageCount(
        int $count,
        User $user,
        string $message = "read message count for user is incorrect",
        null|Mailbox|array $mailbox = null
    ) {
        $this->assertMessageCount(
            $count,
            $user,
            MailboxSearch::FOLDER_READ_ALL,
            $mailbox ?? $user->personalMailbox,
            $message
        );
    }

    protected function assertUnreadAndReadMessageCounts(
        int $unread_count,
        int $read_count,
        User $user,
        string $who_for = "user",
        null|Mailbox|array $mailbox = null
    ) {
        $this->assertUnreadMessageCount($unread_count, $user, "unread message count for $who_for is incorrect", $mailbox);
        $this->assertReadMessageCount($read_count, $user, "read message count for $who_for is incorrect", $mailbox);
    }

    /**
     * @param integer $count
     * @param User $user
     * @param [type] $folder
     * @param null|Mailbox|Mailbox[] $mailbox
     * @param string $message
     * @return void
     */
    protected function assertMessageCount(
        int $count,
        User $user,
        string $folder = MailboxSearch::FOLDER_ALL,
        null|Mailbox|array $mailbox = null,
        string $message = "message count for user folder is incorrect"
    ) {
        $search = new MailboxSearch($user, $folder);

        $mailbox_ids = $this->mapToIds($mailbox);

        $data = $search->retrieveMailboxContentsUsingSQL($user->id, $mailbox_ids)->getData();

        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        if ($count === 0) {
            $this->assertCount(0, $data, $message);
        } else {
            $this->assertArrayHasKey(0, $data, $message . " - no messages found");
            $this->assertEquals($count, $data[0]['total_message_count'], $message);
        }
    }

    /**
     * This assertion is designed as a sanity check to cover the MailSearch behaviour
     * which has two separate SQL queries for folder calculations
     *
     */
    protected function assertCountQueryMatchesDataQuery(
        User $user,
        Mailbox $mailbox,
        array $folders = null,
        string $message = ""
    ) {
        $message = strlen($message) ? $message . ":\n" : "";
        $folders = $folders ?? [
            MailboxSearch::FOLDER_ALL,

            MailboxSearch::FOLDER_UNREAD_ALL,
            MailboxSearch::FOLDER_UNREAD_URGENT,
            MailboxSearch::FOLDER_UNREAD_QUERY,
            MailboxSearch::FOLDER_UNREAD_TO_ME,
            MailboxSearch::FOLDER_UNREAD_CC,
            MailboxSearch::FOLDER_UNREAD_REPLIES,

            MailboxSearch::FOLDER_READ_ALL,
            MailboxSearch::FOLDER_READ_URGENT,
            MailboxSearch::FOLDER_READ_TO_ME,
            MailboxSearch::FOLDER_READ_CC,

            MailboxSearch::FOLDER_STARTED_THREADS,
            MailboxSearch::FOLDER_WAITING_FOR_REPLY,
            MailboxSearch::FOLDER_UNREAD_BY_RECIPIENT,
        ];

        $search = new MailboxSearch($user, MailboxSearch::FOLDER_ALL);
        $count_reported_counts = $search->getMailboxFolderCounts($user->id, [$mailbox->id]);

        $data_reported_counts = [];
        $actual_returned_counts = [];

        foreach ($folders as $folder) {
            $search = new MailboxSearch($user, $folder);
            $mailbox_data = $search->retrieveMailboxContentsUsingSQL($user->id, [$mailbox->id])->getData();

            $actual_returned_message_count = count($mailbox_data);
            $data_reported_count = $actual_returned_message_count > 0 ? $mailbox_data[0]['total_message_count'] : 0;

            $data_reported_counts[$folder] = (string) $data_reported_count;
            $actual_returned_counts[$folder] = (string) $actual_returned_message_count;
        }

        $this->assertEquals($count_reported_counts, $data_reported_counts, "{$message}COUNT QUERY message counts do not match DATA QUERY message counts");
        $this->assertEquals($data_reported_counts, $actual_returned_counts, "{$message}DATA QUERY reported message counts do not match ACTUAL COUNTS of messages returned for folder");
        $this->assertEquals($count_reported_counts, $actual_returned_counts, "{$message}COUNT QUERY message counts do not match ACTUAL COUNTS of messages returned");
    }

    private function mapToIds($models)
    {
        if ($models === null) {
            return [];
        }
        if (!is_array($models)) {
            $models = [$models];
        }

        return array_map(function ($model) { return $model->id; }, $models);
    }
}
