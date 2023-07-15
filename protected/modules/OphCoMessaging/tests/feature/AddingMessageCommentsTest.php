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
    use WithTransactions;

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

        $data_provider = $search->retrieveMailboxContentsUsingSQL($primary_user->id);
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

    protected function getMailboxUser()
    {
        $user = \User::factory()->withAuthItems([
            'User',
            'Edit',
            'View clinical'
        ])->create();

        return [$user, Mailbox::factory()->withUsers([$user])->create()];
    }

    protected function markReadFor($message_element, $user)
    {
        $this->actingAs($user)
            ->get('/OphCoMessaging/default/markRead?id=' . $message_element->event_id);
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
