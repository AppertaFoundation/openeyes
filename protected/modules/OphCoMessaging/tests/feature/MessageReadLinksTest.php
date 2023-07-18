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

use OEDbTestCase;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\tests\traits\MakesMessagingRequests;
use WithFaker;
use WithTransactions;

/**
 * class MessageReadLinksTest
 * @covers OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message
 * @group shared-mailboxes
 * @group sample-data
 */
class MessageReadLinksTest extends OEDbTestCase
{
    use MakesMessagingRequests;
    use WithFaker;
    use WithTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockCurrentContext();
    }

    /** @test */
    public function recepient_view_offers_mark_as_read_link()
    {
        list($sender, $sender_mailbox) = $this->createMailboxUser();
        list($recipient, $recipient_mailbox) = $this->createMailboxUser();

        $message = Element_OphCoMessaging_Message::factory()
            ->withReplyRequired()
            ->withPrimaryRecipient($recipient_mailbox, false)
            ->create([
                'sender_mailbox_id' => $sender_mailbox
            ]);

        $response = $this->actingAs($recipient)
            ->get($this->urlToViewMessage($message));

        $this->assertResponseProvidesMarkAsReadAction($response, $message);
    }

    /** @test */
    public function recepient_view_offers_mark_as_unread_link()
    {
        list($sender, $sender_mailbox) = $this->createMailboxUser();
        list($recipient, $recipient_mailbox) = $this->createMailboxUser();

        $message = Element_OphCoMessaging_Message::factory()
                 ->withReplyRequired()
                 ->withPrimaryRecipient($recipient_mailbox, true)
                 ->create([
                     'sender_mailbox_id' => $sender_mailbox
                 ]);

        $response = $this->actingAs($recipient)
                         ->get($this->urlToViewMessage($message));

        $this->assertResponseProvidesMarkAsUnreadAction($response, $message);
    }

    /** @test */
    public function recepient_view_offers_comment_form_action()
    {
        list($sender, $sender_mailbox) = $this->createMailboxUser();
        list($recipient, $recipient_mailbox) = $this->createMailboxUser();

        $message = Element_OphCoMessaging_Message::factory()
                 ->withReplyRequired()
                 ->withPrimaryRecipient($recipient_mailbox, true)
                 ->create([
                     'sender_mailbox_id' => $sender_mailbox
                 ]);

        $response = $this->actingAs($recipient)
                         ->get($this->urlToViewMessage($message));

        $this->assertResponseProvidesPostCommentAction($response, $message);
    }

    protected function assertResponseProvidesMarkAsReadAction($response, $message)
    {
        $filtered_to_action = $response->filter('[data-test="mark-as-read-btn"]');
        $this->assertTrue(count($filtered_to_action) > 0, 'action not found in response');
        $this->assertStringContainsStringIgnoringCase(
            $this->urlToMarkMessageRead($message),
            $filtered_to_action->first()->attr('href'),
            'action route is incorrect'
        );
    }

    protected function assertResponseProvidesMarkAsUnreadAction($response, $message)
    {
        $filtered_to_action = $response->filter('[data-test="mark-as-unread-btn"]');
        $this->assertTrue(count($filtered_to_action) > 0, 'action not found in response');
        $this->assertStringContainsStringIgnoringCase(
            $this->urlToMarkMessageUnread($message),
            $filtered_to_action->first()->attr('href'),
            'action route is incorrect'
        );
    }

    protected function assertResponseProvidesPostCommentAction($response, $message)
    {
        $filtered_to_action = $response->filter('[data-test="message-comment-form"]');
        $this->assertTrue(count($filtered_to_action) > 0, 'action not found in response');
        $this->assertStringContainsStringIgnoringCase(
            $this->urlToPostComment($message),
            $filtered_to_action->first()->attr('action'),
            'action route is incorrect'
        );
    }
}
