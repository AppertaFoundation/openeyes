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
    //use WithTransactions; // Currently disabled due to database integrity issues caused in other tests

    /** @test */
    public function message_marked_unread_for_recipient_when_comment_is_added_by_sender()
    {
        $message_element = Element_OphCoMessaging_Message::factory()
            ->withReplyRequired()
            ->withPrimaryRecipient(null, true)
            ->create();

        $recipient = OphCoMessaging_Message_Recipient::model()
            ->find('element_id = ?', [$message_element->id]);

        $this->assertTrue((bool) $recipient->marked_as_read);


        $this->mockCurrentContext();
        $this->mockCurrentUser($message_element->user);

        $response = $this->post('/OphCoMessaging/default/addComment?id=' . $message_element->event_id, [
            'comment_reply_mailbox' => $message_element->sender_mailbox_id,
            'OEModule_OphCoMessaging_models_OphCoMessaging_Message_Comment' => [
                'comment_text' => $this->faker->sentence()
            ]
        ]);

        $response->assertRedirect('/OphCoMessaging/Default/view/' . $message_element->event_id, 'Response should redirect to message view', true);

        $this->assertDatabaseHas('ophcomessaging_message_comment', [
            'element_id' => $message_element->id,
            'mailbox_id' => $message_element->sender_mailbox_id
        ]);

        $recipient->refresh();

        $this->assertFalse((bool) $recipient->marked_as_read);
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

        $this->assertFalse((bool) $primary_recipient->marked_as_read);

        $this->mockCurrentContext();
        $this->mockCurrentUser($primary_user);

        // mark read for primary
        $this->markReadFor($message_element, $primary_user);

        $primary_recipient->refresh();
        $this->assertTrue((bool) $primary_recipient->marked_as_read);

        $secondary_recipient->refresh();
        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $this->postCommentOn($message_element, $primary_user, $primary_mailbox);

        $search = new MailboxSearch(null, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEquals(1, $data_provider->getData()[0]['total_message_count']);

        $this->markReadFor($message_element, $sender_user);

        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertCount(0, $data_provider->getData());

        $this->postCommentOn($message_element, $sender_user, $sender_mailbox);

        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEquals(1, $data_provider->getData()[0]['total_message_count']);

        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEquals(1, $data_provider->getData()[0]['total_message_count']);

        $this->markReadFor($message_element, $primary_user);

        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertCount(0, $data_provider->getData());

        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEquals(1, $data_provider->getData()[0]['total_message_count']);
    }

    /** @test */
    public function message_marked_read_will_update_message_read_state_correctly() {
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
        $this->markReadFor($message_element, $primary_user);

        $primary_recipient->refresh();
        $this->assertTrue((bool) $primary_recipient->marked_as_read);
        $primary_recipient->refresh();
        $this->assertTrue((bool) $primary_recipient->marked_as_read);
        //Ensure that this message does not appear in CC recipient's unread folder
        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());
    }

    /** @test */
    public function message_marked_read_by_cc_user_remains_read_when_comments_are_made() {
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

        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $this->mockCurrentContext();
        // $this->mockCurrentUser($primary_user);

        $search = new MailboxSearch($secondary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        
        //Ensure that CC recipient actually received the message
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEquals(1, $data_provider->getData()[0]['total_message_count']);

        // mark read for secondary
        $this->markReadFor($message_element, $secondary_user);

        //Ensure that CC recipient's marked_as_read is left intact by the previous action
        $secondary_recipient->refresh();
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);
        //Ensure that this message does not appear in CC recipient's unread folder
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());

        //Comment as the primary recipient of the message
        $this->postCommentOn($message_element, $primary_user, $primary_mailbox);

        //Ensure that CC recipient's marked_as_read is left intact by the previous action
        $secondary_recipient->refresh();
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);
        //Ensure that this message does not appear in CC recipient's unread folder
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());

        //Comment as the original sender
        $this->postCommentOn($message_element, $sender_user, $sender_mailbox);

        //Ensure that CC recipient's marked_as_read is left intact by the previous action
        $secondary_recipient->refresh();
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);
        //Ensure that this message does not appear in CC recipient's unread folder
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());

        // mark read for primary
        $this->markReadFor($message_element, $primary_user);

        //Ensure that CC recipient's marked_as_read is left intact by the previous action
        $secondary_recipient->refresh();
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);
        //Ensure that this message does not appear in CC recipient's unread folder
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());
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
        $this->postCommentOn($message_element, $primary_user, $primary_mailbox);

        //assert that the message now appears in the sender's unread mailbox
        $data_provider = $sender_search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        // due to construction of the dataprovider in the searcher, we rely on the first
        // entry in the data to validate the total message count
        $this->assertEquals(1, $data_provider->getData()[0]['total_message_count']);
    }

    //sent message does not show in sender's unread mailbox
    /** @test */
    public function sent_message_does_not_show_in_sender_unread_mailbox() {
        $data = $this->sendMessage();
        $sender_user = $data['sender']['user'];
        $sender_mailbox = $data['sender']['mailbox'];

        $this->mockCurrentContext();
        
        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $this->assertEmpty($data_provider->getData());
    }

    //sent message shows in sent mailbox
    /** @test */
    public function sent_message_shows_in_sender_sent_mailbox() {
        $data = $this->sendMessage();
        $sender_user = $data['sender']['user'];
        $sender_mailbox = $data['sender']['mailbox'];
        
        $this->mockCurrentContext();

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_SENT_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());
    }

    //sent message is marked unread for each recipient
    // - for primary
    // - for cc
    /** @test */
    public function sent_message_is_marked_unread_for_each_recipient() {
        $data = $this->sendMessage();
        $primary_recipient = $data['recipients']['primary']['recipient'];
        $secondary_recipient = $data['recipients']['secondary']['recipient'];
        
        $this->mockCurrentContext();

        $this->assertFalse((bool)$primary_recipient->marked_as_read);
        $this->assertFalse((bool)$secondary_recipient->marked_as_read);
    }

    //sent message shows in each recipient's unread mailbox
    // - for primary
    // - for cc
    /** @test */
    public function sent_message_is_shows_in_each_recipient_mailbox() {
        $data = $this->sendMessage();
        $primary_user = $data['recipients']['primary']['user'];
        $primary_mailbox = $data['recipients']['primary']['mailbox'];
        $secondary_user = $data['recipients']['secondary']['user'];
        $secondary_mailbox = $data['recipients']['secondary']['mailbox'];
        
        $this->mockCurrentContext();

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());
        
        $search = new MailboxSearch($secondary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());
    }

    //marking as read updates read status and shows in read mailbox
    // - for sender
    // - for primary recipient
    // - for cc recipient
    /** @test */
    public function marking_message_as_read_updates_read_status_and_shows_in_read_mailbox() {
        $data = $this->sendMessage();
        $message_element = $data['element'];

        $primary_user = $data['recipients']['primary']['user'];
        $primary_mailbox = $data['recipients']['primary']['mailbox'];
        $primary_recipient = $data['recipients']['primary']['recipient'];

        $secondary_user = $data['recipients']['secondary']['user'];
        $secondary_mailbox = $data['recipients']['secondary']['mailbox'];
        $secondary_recipient = $data['recipients']['secondary']['recipient'];

        $this->mockCurrentContext();

        $this->markReadFor($message_element, $primary_user);
        
        $this->assertTrue((bool) $primary_recipient->marked_as_read);

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_READ_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());

        $this->markReadFor($message_element, $secondary_user);

        $this->assertTrue((bool) $secondary_recipient->marked_as_read);

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_READ_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());
    }

    //marking as read updates read status and does not show in unread mailbox
    // - for sender
    // - for primary recipient
    // - for cc recipient
    /** @test */
    public function marking_as_read_updates_read_status_and_does_not_show_in_unread_mailbox() {
        $data = $this->sendMessage();
        $message_element = $data['element'];

        $primary_user = $data['recipients']['primary']['user'];
        $primary_mailbox = $data['recipients']['primary']['mailbox'];
        $primary_recipient = $data['recipients']['primary']['recipient'];

        $secondary_user = $data['recipients']['secondary']['user'];
        $secondary_mailbox = $data['recipients']['secondary']['mailbox'];
        $secondary_recipient = $data['recipients']['secondary']['recipient'];

        $this->mockCurrentContext();

        $this->markReadFor($message_element, $primary_user);
        
        $this->assertTrue((bool) $primary_recipient->marked_as_read);

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());

        $this->markReadFor($message_element, $secondary_user);

        $this->assertTrue((bool) $secondary_recipient->marked_as_read);

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());
    }

    //marking as unread updates unread status and shows in unread mailbox
    // - for sender
    // - for primary recipient
    // - for cc recipient
    /** @test */
    public function marking_message_as_unread_updates_read_status_and_shows_in_unread_mailbox() {
        $data = $this->sendMessage();
        $message_element = $data['element'];

        $primary_user = $data['recipients']['primary']['user'];
        $primary_mailbox = $data['recipients']['primary']['mailbox'];
        $primary_recipient = $data['recipients']['primary']['recipient'];

        $secondary_user = $data['recipients']['secondary']['user'];
        $secondary_mailbox = $data['recipients']['secondary']['mailbox'];
        $secondary_recipient = $data['recipients']['secondary']['recipient'];

        $this->mockCurrentContext();

        $this->markReadFor($message_element, $primary_user);
        $this->markReadFor($message_element, $secondary_user);

        $this->markUnreadFor($message_element, $primary_user);
        
        $this->assertFalse((bool) $primary_recipient->marked_as_read);

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());

        $this->markUnreadFor($message_element, $secondary_user);

        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());
    }

    //marking as unread updates unread status and does not show in read mailbox
    // - for sender
    // - for primary recipient
    // - for cc recipient
    /** @test */
    public function marking_message_as_unread_updates_read_status_and_does_not_show_in_read_mailbox() {
        $data = $this->sendMessage();
        $message_element = $data['element'];

        $primary_user = $data['recipients']['primary']['user'];
        $primary_mailbox = $data['recipients']['primary']['mailbox'];
        $primary_recipient = $data['recipients']['primary']['recipient'];

        $secondary_user = $data['recipients']['secondary']['user'];
        $secondary_mailbox = $data['recipients']['secondary']['mailbox'];
        $secondary_recipient = $data['recipients']['secondary']['recipient'];

        $this->mockCurrentContext();

        $this->markReadFor($message_element, $primary_user);
        $this->markReadFor($message_element, $secondary_user);

        $this->markUnreadFor($message_element, $primary_user);
        
        $this->assertFalse((bool) $primary_recipient->marked_as_read);

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_READ_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());

        $this->markUnreadFor($message_element, $secondary_user);

        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $search = new MailboxSearch($primary_user, MailboxSearch::FOLDER_READ_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);
        $this->assertEmpty($data_provider->getData());
    }
    
    //adding a comment marks message unread for each other related user, ie
    // - adding comment as sender marks unread for primary and cc
    // - adding comment as primary marks unread for sender and cc
    // - adding comment as cc marks unread for primary and sender
    public function adding_a_comment_marks_message_unread_for_the_other_related_users_of_the_comment()
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

        $this->markReadFor($element, $primary_user);
        $this->markReadFor($element, $secondary_user);

        $this->assertTrue((bool) $primary_recipient->marked_as_read);
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);

        $this->postCommentOn($element, $sender_user, $sender_mailbox);

        $primary_recipient->refresh();
        $secondary_recipient->refresh();

        $this->assertFalse((bool) $primary_recipient->marked_as_read);
        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $this->markReadFor($element, $secondary_user);

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);

        $this->assertCount(0, $data_provider->getData());
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);

        $this->postCommentOn($element, $primary_user, $primary_mailbox);

        $secondary_recipient->refresh();
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);

        $this->assertCount(0, $data_provider->getData());
        $this->assertFalse((bool) $secondary_recipient->marked_as_read);

        $this->postCommentOn($element, $secondary_user, $secondary_mailbox);

        $primary_recipient->refresh();
                $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);

        $this->assertTrue((bool) $primary_recipient->marked_as_read);
        $this->assertCount(0, $data_provider->getData());
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

        $this->postCommentOn($element, $primary_user, $primary_mailbox);

        $primary_recipient->refresh();
        $this->assertTrue((bool) $primary_recipient->marked_as_read);

        $this->postCommentOn($element, $secondary_user, $secondary_mailbox);

        $secondary_recipient->refresh();
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $this->assertCount(1, $data_provider->getData());

        $this->postCommentOn($element, $sender_user, $sender_mailbox);

        $data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $this->assertCount(0, $data_provider->getData());
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

        $this->postCommentOn($element, $sender_user, $sender_mailbox);

        $search = new MailboxSearch(null, MailboxSearch::FOLDER_UNREAD_ALL);
        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $primary_recipient->refresh();
        $secondary_recipient->refresh();

        $this->assertCount(0, $sender_data_provider->getData());
        $this->assertCount(1, $primary_data_provider->getData());
        $this->assertCount(0, $secondary_data_provider->getData());
        $this->assertFalse((bool) $primary_recipient->marked_as_read);
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);
    }

    /** @test */
    public function cc_recipients_can_mark_primary_replies_read()
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

        $this->postCommentOn($element, $primary_user, $primary_mailbox);

        $search = new MailboxSearch(null, MailboxSearch::FOLDER_UNREAD_ALL);
        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $secondary_data_provider = $search->retrieveMailboxContentsUsingSQL($secondary_user->id, [$secondary_mailbox->id]);

        $primary_recipient->refresh();
        $secondary_recipient->refresh();

        $this->assertCount(1, $sender_data_provider->getData());
        $this->assertCount(0, $primary_data_provider->getData());
        $this->assertCount(0, $secondary_data_provider->getData());
        $this->assertTrue((bool) $primary_recipient->marked_as_read);
        $this->assertTrue((bool) $secondary_recipient->marked_as_read);
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

        $search = new MailboxSearch($sender_user, MailboxSearch::FOLDER_UNREAD_ALL);
        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);

        $this->assertCount(0, $sender_data_provider->getData());
        $this->assertCount(1, $primary_data_provider->getData());
        $this->assertFalse((bool) $primary_recipient->marked_as_read);

        $this->postCommentOn($element, $primary_user, $primary_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);
        $primary_recipient->refresh();

        $this->assertCount(1, $sender_data_provider->getData());
        $this->assertCount(0, $primary_data_provider->getData());
        $this->assertTrue((bool) $primary_recipient->marked_as_read);

        $this->postCommentOn($element, $sender_user, $sender_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);

        $this->assertCount(0, $sender_data_provider->getData());
        $this->assertCount(1, $primary_data_provider->getData());

        $this->postCommentOn($element, $primary_user, $primary_mailbox);

        $sender_data_provider = $search->retrieveMailboxContentsUsingSQL($sender_user->id, [$sender_mailbox->id]);
        $primary_data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id, [$primary_mailbox->id]);

        $this->assertCount(1, $sender_data_provider->getData());
        $this->assertCount(0, $primary_data_provider->getData());
    }

    protected function sendMessage() {
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

    protected function markReadFor($message_element, $user)
    {
        $this->actingAs($user)
            ->get('/OphCoMessaging/default/markRead?id=' . $message_element->event_id);
    }

    protected function markUnreadFor($message_element, $user)
    {
        $this->actingAs($user)
            ->get('/OphCoMessaging/default/markUnread?id=' . $message_element->event_id);
    }

    protected function postCommentOn($message_element, $user, $mailbox, $text = null)
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
            );
    }
}
