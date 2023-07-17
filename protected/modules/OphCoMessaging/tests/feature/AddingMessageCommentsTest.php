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
use MakesApplicationRequests;
use MocksSession;
use OEDbTestCase;
use OEModule\OphCoMessaging\components\MailboxSearch;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;
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
    use MakesApplicationRequests;
    use MocksSession;
    use WithFaker;
    use WithTransactions;

    /** @test */
    public function message_marked_unread_for_recipient_when_comment_is_added_by_sender()
    {
        [$sender, $sender_mailbox] = $this->getMailboxUser();
        $message_element = Element_OphCoMessaging_Message::factory()
            ->withReplyRequired()
            ->withPrimaryRecipient(null, true)
            ->create([
                'sender_mailbox_id' => $sender_mailbox
            ]);

        $recipient = OphCoMessaging_Message_Recipient::model()
            ->find('element_id = ?', [$message_element->id]);

        $this->assertTrue((bool) $recipient->marked_as_read);

        $this->mockCurrentContext();
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
        [$sender_user, $sender_mailbox] = $this->getMailboxUser();
        [$primary_user, $primary_mailbox] = $this->getMailboxUser();
        [$secondary_user, $secondary_mailbox] = $this->getMailboxUser();

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

        $this->mockCurrentContext();
        $this->mockCurrentUser($primary_user);

        // mark read for primary
        $this->markReadWithRequest($message_element, $primary_user);

        $this->assertUnreadMessageCount(0, $primary_user, "unread count for primary should be zero after marking as read.");
        $this->assertUnreadMessageCount(1, $secondary_user, "unread count should still be 1 for cc after primary marking as read.");

        $this->postCommentWithRequestOn($message_element, $primary_user, $primary_mailbox);

        $this->assertUnreadMessageCount(1, $sender_user, "Sender unread count should be 1 after primary reply.");

        $this->markReadWithRequest($message_element, $sender_user);

        $this->assertUnreadMessageCount(0, $sender_user, "Sender unread count should be 0 after marking as read.");

        $this->postCommentWithRequestOn($message_element, $sender_user, $sender_mailbox);

        $this->assertUnreadMessageCount(1, $primary_user, "unread count for primary should be 1 after sender replied.");
        $this->assertUnreadMessageCount(1, $secondary_user, "unread count for secondary should be 1 after sender replied.");

        $this->markReadWithRequest($message_element, $primary_user);

        $this->assertUnreadMessageCount(0, $primary_user, "unread count for primary should be zero after marking as read again.");

        $this->assertUnreadMessageCount(1, $secondary_user, "unread count for secondary should remain 1 after primary marking as read.");

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function message_marked_read_will_update_message_read_state_correctly()
    {
        [$sender_user, $sender_mailbox] = $this->getMailboxUser();
        [$primary_user, $primary_mailbox] = $this->getMailboxUser();

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

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_UNREAD_ALL);

        $this->assertFalse((bool) $primary_recipient->marked_as_read);

        $this->mockCurrentContext();
        $this->mockCurrentUser($primary_user);

        // mark read for primary
        $this->markReadWithRequest($message_element, $primary_user);

        $primary_recipient->refresh();
        $this->assertTrue((bool) $primary_recipient->marked_as_read);
        $primary_recipient->refresh();
        $this->assertTrue((bool) $primary_recipient->marked_as_read);
        //Ensure that this message does not appear in CC recipient's unread folder
        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
    }

    /** @test */
    public function message_marked_read_by_cc_user_resets_to_unread_when_comments_are_made()
    {
        [$sender_user, $sender_mailbox] = $this->getMailboxUser();
        [$primary_user, $primary_mailbox] = $this->getMailboxUser();
        [$secondary_user, $secondary_mailbox] = $this->getMailboxUser();

        $message_element = Element_OphCoMessaging_Message::factory()
            ->withSender($sender_user, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($primary_mailbox)
            ->withCCRecipients([[$secondary_mailbox, false]])
            ->create();

        $secondary_recipient = OphCoMessaging_Message_Recipient::model()
            ->findByAttributes([
                'element_id' => $message_element->id,
                'mailbox_id' => $secondary_mailbox->id
            ]);

        // arrangement sanity checks
        $this->assertFalse((bool) $secondary_recipient->marked_as_read);
        $this->assertUnreadMessageCount(
            1,
            $secondary_user,
            "message should initially be unread for cc user"
        );

        $this->mockCurrentContext();
        // mark read for secondary
        $this->markReadWithRequest($message_element, $secondary_user);

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
        $this->markReadWithRequest($message_element, $secondary_user);

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

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    /** @test */
    public function comment_added_to_message_by_primary_recipient_marks_message_unread_for_original_sender() {
        [$sender_user, $sender_mailbox] = $this->getMailboxUser();
        [$primary_user, $primary_mailbox] = $this->getMailboxUser();

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

        $this->mockCurrentContext();

        $primary_search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $sender_search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);

        //Assert that message was received by the primary recipient
        $data_provider = $primary_search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEquals(1, $data_provider->getData()[0]['total_message_count']);

        //assert that message does not show in the sender's unread mailbox
        $data_provider = $sender_search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEmpty($data_provider->getData());

        //post a comment on the message
        $this->postCommentWithRequestOn($message_element, $primary_user, $primary_mailbox);

        //assert that the message now appears in the sender's unread mailbox
        $data_provider = $sender_search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEquals(1, $data_provider->getData()[0]['total_message_count']);

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
    }

    //sent message does not show in sender's unread mailbox
    /** @test */
    public function sent_message_does_not_show_in_sender_unread_mailbox() {
        list(
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            )
        ) = $this->sendMessage();

        $this->mockCurrentContext();

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $this->assertEmpty($data_provider->getData());

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
    }

    //sent message shows in sent mailbox
    /** @test */
    public function sent_message_shows_in_sender_sent_mailbox() {
        list(
            'sender' => list(
                'user' => $sender_user,
                'mailbox' => $sender_mailbox,
            )
        ) = $this->sendMessage();

        $this->mockCurrentContext();

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_STARTED_THREADS);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());

        $this->assertCountQueryMatchesDataQuery($sender_user, $sender_mailbox);
    }

    //sent message is marked unread for each recipient
    // - for primary
    // - for cc
    /** @test */
    public function sent_message_is_marked_unread_for_each_recipient() {
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

        $this->mockCurrentContext();

        $this->assertFalse((bool)$primary_recipient->marked_as_read);
        $this->assertFalse((bool)$secondary_recipient->marked_as_read);
    }

    //sent message shows in each recipient's unread mailbox
    // - for primary
    // - for cc
    /** @test */
    public function sent_message_is_shows_in_each_recipient_mailbox() {
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

        $this->mockCurrentContext();

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());

        $search = new MailboxSearch($secondary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());

        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    //marking as read updates read status and shows in read mailbox
    // - for sender
    // - for primary recipient
    // - for cc recipient
    /** @test */
    public function marking_message_as_read_updates_read_status_and_shows_in_read_mailbox() {
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

        $this->mockCurrentContext();

        $this->markReadWithRequest($message_element, $primary_user);

        $this->assertMessageCount(
            1,
            $primary_user,
            MailboxSearch::FOLDER_READ_ALL,
            $primary_mailbox,
            "Message should appear in read folder when marked as read for primary recipient"
        );

        $this->markReadWithRequest($message_element, $secondary_user);

        $this->assertMessageCount(
            1,
            $secondary_user,
            MailboxSearch::FOLDER_READ_ALL,
            $secondary_mailbox,
            "Message should appear in read folder when marked as read for cc recipient"
        );

        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    //marking as read updates read status and does not show in unread mailbox
    // - for sender
    // - for primary recipient
    // - for cc recipient
    /** @test */
    public function marking_as_read_updates_read_status_and_does_not_show_in_unread_mailbox()
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

        $this->mockCurrentContext();

        $this->markReadWithRequest($message_element, $primary_user);
        $this->assertUnreadMessageCount(0, $primary_user, "message should not be unread for primary after marking read.");

        $this->markReadWithRequest($message_element, $secondary_user);
        $this->assertUnreadMessageCount(0, $secondary_user, "message should not be unread for cc user after marking read.");

        $this->assertCountQueryMatchesDataQuery($primary_user, $primary_mailbox);
        $this->assertCountQueryMatchesDataQuery($secondary_user, $secondary_mailbox);
    }

    //marking as unread updates unread status and shows in unread mailbox
    // - for sender
    // - for primary recipient
    // - for cc recipient
    /** @test */
    public function marking_message_shows_in_unread_mailbox()
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

        $this->mockCurrentContext();

        $this->markReadWithRequest($message_element, $primary_user);
        $this->markReadWithRequest($message_element, $secondary_user);

        $this->markUnreadWithRequest($message_element, $primary_user);

        $this->assertUnreadMessageCount(1, $primary_user, "primary users should have an unread message after marking unread.");

        $this->markUnreadWithRequest($message_element, $secondary_user);
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

        $this->mockCurrentContext();

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

        $this->mockCurrentContext();

        $this->postCommentWithRequestOn($element, $sender_user, $sender_mailbox);

        $this->assertUnreadMessageCount(
            1,
            $secondary_user,
            "cc user should still be marked unread after first comment"
        );

        $this->markReadWithRequest($element, $secondary_user);

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

        $this->mockCurrentContext();

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

        $this->markReadWithRequest($element, $secondary_user);

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

        $this->mockCurrentContext();

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

        $this->assertMessageCount(1, $primary_user, MailboxSearch::FOLDER_READ_ALL);
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

        $this->mockCurrentContext();

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

        $this->mockCurrentContext();

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
        [$sender_user, $sender_mailbox] = $this->getMailboxUser();
        [$primary_user, $primary_mailbox] = $this->getMailboxUser();
        [$secondary_user, $secondary_mailbox] = $this->getMailboxUser();

        $initial_messages = Element_OphCoMessaging_Message::factory()
            ->withSender($sender_user, $sender_mailbox)
            ->withReplyRequired()
            ->withPrimaryRecipient($primary_mailbox)
            ->withCCRecipients([[$secondary_mailbox, false]])
            ->count(5)
            ->create();

        $this->mockCurrentContext();

        $this->postCommentWithRequestOn($initial_messages[0], $primary_user, $primary_mailbox);
        $this->postCommentWithRequestOn($initial_messages[2], $primary_user, $primary_mailbox);

        $this->markReadWithRequest($initial_messages[0], $secondary_user);
        $this->markReadWithRequest($initial_messages[2], $secondary_user);

        $this->postCommentWithRequestOn($initial_messages[2], $sender_user, $sender_mailbox);

        $this->assertMessageCount(5, $primary_user);
        $this->assertCountQueryMatchesDataQuery($primary_mailbox, $primary_mailbox);
    }

    protected function sendMessage(): array
    {
        [$sender_user, $sender_mailbox] = $this->getMailboxUser();
        [$primary_user, $primary_mailbox] = $this->getMailboxUser();
        [$secondary_user, $secondary_mailbox] = $this->getMailboxUser();

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

    protected function getMailboxUser()
    {
        $user = \User::factory()->withAuthItems([
            'User',
            'Edit',
            'View clinical'
        ])->create();

        return [$user, Mailbox::factory()->personalFor($user)->create()];
    }

    protected function markReadWithRequest($message_element, $user)
    {
        $this->actingAs($user)
            ->get('/OphCoMessaging/default/markRead?id=' . $message_element->event_id);
    }

    protected function markUnreadWithRequest($message_element, $user)
    {
        $this->actingAs($user)
            ->get('/OphCoMessaging/default/markUnread?id=' . $message_element->event_id);
    }

    protected function postCommentWithRequestOn($message_element, $user, $mailbox, $text = null)
    {
        $this->actingAs($user)
            ->post(
                '/OphCoMessaging/default/addComment?id=' . $message_element->event_id,
                [
                    'comment_reply_mailbox' => $mailbox->id,
                    'OEModule_OphCoMessaging_models_OphCoMessaging_Message_Comment' => [
                        'comment_text' => $text ?? $this->faker->sentence()
                    ]
                ]
            )
            ->assertRedirect();
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

    protected function assertCountQueryMatchesDataQuery(
        User $user,
        Mailbox $mailbox,
        array $folders = null,
    ) {
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

        foreach($folders as $folder) {
            $search = new MailboxSearch($user, $folder);
            $mailbox_data = $search->retrieveMailboxContentsUsingSQL($user->id, [$mailbox->id])->getData();

            $actual_returned_message_count = count($mailbox_data);
            $data_reported_count = $actual_returned_message_count > 0 ? $mailbox_data[0]['total_message_count'] : 0;

            $data_reported_counts[$folder] = (string) $data_reported_count;
            $actual_returned_counts[$folder] = (string) $actual_returned_message_count;
        }

        $this->assertEquals($count_reported_counts, $data_reported_counts, "COUNT QUERY message counts do not match DATA QUERY message counts");
        $this->assertEquals($data_reported_counts, $actual_returned_counts, "DATA QUERY reported message counts do not match ACTUAL COUNTS of messages returned for folder");
        $this->assertEquals($count_reported_counts, $actual_returned_counts, "COUNT QUERY message counts do not match ACTUAL COUNTS of messages returned");
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
