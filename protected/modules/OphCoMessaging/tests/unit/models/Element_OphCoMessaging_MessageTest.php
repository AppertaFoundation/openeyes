<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoMessaging\tests\unit\models;

use Event;
use Mailer;
use OE\factories\models\EventFactory;
use OEModule\OphCoMessaging\factories\models\OphCoMessaging_Message_CommentFactory;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;
use WithTransactions;

/**
 * @group sample-data
 * @group messaging
 */
class Element_OphCoMessaging_MessageTest extends \ModelTestCase
{
    use WithTransactions;

    protected $element_cls = Element_OphCoMessaging_Message::class;
    protected $instance;

    /** @test */
    public function event_subtype_is_set_from_message_type()
    {
        $message_type = $this->getMessageType(true);

        $element = $this->makeElementForSaving([
            'message_type_id' => $message_type->id
        ]);

        $element->save();

        $this->assertEquals($message_type->event_subtype, $element->event->firstEventSubtypeItem->event_subtype);
    }

    /** @test */
    public function event_subtype_is_removed_from_message_type()
    {
        $message_type = $this->getMessageType(true);
        $element = Element_OphCoMessaging_Message::factory()
            ->withPrimaryRecipient()
            ->create([
                'message_type_id' => $message_type->id
            ]);

        $event = Event::model()->findByPk($element->event_id);
        $this->assertNotNull($event->firstEventSubtypeItem);

        // create new message type without a subtype
        $message_type2 = $this->getMessageType(false);
        $element->message_type_id = $message_type2->id;

        $element->save();

        // reload event
        $event->refresh();
        $this->assertNull($event->firstEventSubtypeItem);
    }

    /** @test */
    public function event_subtype_is_updated_by_changed_message_type()
    {
        $message_type = $this->getMessageType(true);
        $element = Element_OphCoMessaging_Message::factory()
            ->withPrimaryRecipient()
            ->create([
                'message_type_id' => $message_type->id
            ]);

        $event = Event::model()->findByPk($element->event_id);
        $this->assertNotNull($event->firstEventSubtypeItem);

        // create new message type with a subtype
        $message_type2 = $this->getMessageType(true);
        $element->message_type_id = $message_type2->id;

        $element->save();

        $event->refresh();

        $this->assertEquals($message_type2->event_subtype, $event->firstEventSubtypeItem->event_subtype);
    }

    /** @test */
    public function message_can_be_marked_as_read_when_sent_to_self()
    {
        $message = Element_OphCoMessaging_Message::factory()
            ->sentToSelf()
            ->create();

        $message->setReadStatusForMailbox($message->sender, true);

        $recipient = OphCoMessaging_Message_Recipient::model()->findByAttributes(['element_id' => $message->id]);

        $this->assertTrue((bool) $recipient->marked_as_read);
    }

    /** @test */
    public function message_can_be_marked_as_read_for_recipient()
    {
        $recipient_mailbox = Mailbox::factory()->create();
        $message = Element_OphCoMessaging_Message::factory()
            ->withPrimaryRecipient($recipient_mailbox)
            ->create();

        $message->setReadStatusForMailbox($recipient_mailbox, true);

        $recipient = OphCoMessaging_Message_Recipient::model()
            ->findByAttributes(['element_id' => $message->id, 'mailbox_id' => $recipient_mailbox->id]);

        $this->assertTrue((bool) $recipient->marked_as_read);
    }

    /** @test */
    public function message_can_be_marked_as_read_for_thread()
    {
        $recipient_mailbox = Mailbox::factory()->create();
        $message = Element_OphCoMessaging_Message::factory()
            ->withReplyRequired()
            ->withPrimaryRecipient($recipient_mailbox)
            ->create();

        $comment = OphCoMessaging_Message_Comment::factory()
            ->withElement($message)
            ->withSender($recipient_mailbox)
            ->create();

        $message->setReadStatusForMailbox($message->sender, true);

        $comment->refresh();

        $this->assertTrue((bool) $comment->marked_as_read);
    }

    public function getMessageType(bool $has_event_subtype = false)
    {
        $factory = OphCoMessaging_Message_MessageType::factory();
        if ($has_event_subtype) {
            $factory = $factory->withEventSubtype();
        }

        return $factory->create();
    }

    public function generateElementAttributes(array $attributes = [])
    {
        return array_merge([
            'message_text' => $this->faker->paragraph()
        ], $attributes);
    }

    protected function makeElementForSaving(array $attributes = [])
    {
        $event = EventFactory::forModule('OphCoMessaging')->create();
        $element = Element_OphCoMessaging_Message::factory()->make(
            array_merge(
                ['event_id' => $event->id],
                $attributes
            )
        );
        // attach a recipient to pass validation
        $element->recipients = [OphCoMessaging_Message_Recipient::factory()->make([
            'element_id' => null
        ])];

        return $element;
    }

    /** @test */
    public function primary_recipients_is_required()
    {
        $element = Element_OphCoMessaging_Message::factory()->make(['event_id' => null]);

        $this->assertAttributeInvalid($element, 'recipients', 'must be sent to');

        $mailbox = Mailbox::factory()->useExisting()->create();

        $element->recipients = [
            OphCoMessaging_Message_Recipient::factory()->asCC($mailbox)->make([
                'element_id' => null
            ])
        ];

        $this->assertAttributeInvalid($element, 'recipients', 'must be sent to');

        $element->recipients = [
            OphCoMessaging_Message_Recipient::factory()->asPrimary($mailbox)->make([
                'element_id' => null
            ])
        ];

        $this->assertAttributeValid($element, 'recipients');
    }

    /** @test */
    public function for_the_attention_of_falls_back_to_cc()
    {
        $mailbox = Mailbox::factory()->useExisting()->create();

        $element = Element_OphCoMessaging_Message::factory()->withCCRecipients([
            [$mailbox, false]
        ])->create();

        $this->assertNotNull($element->for_the_attention_of);
        $this->assertModelIs($mailbox, $element->for_the_attention_of->mailbox);
    }

    /** @test */
    public function for_the_attention_of_is_the_primary_recipient()
    {
        $cc = Mailbox::factory()->useExisting()->create();
        $primary = Mailbox::factory()->create();

        $element = Element_OphCoMessaging_Message::factory()
            ->withCCRecipients([
                [$cc, false]
            ])
            ->withPrimaryRecipient($primary, false)
            ->create();

        $this->assertCount(2, $element->recipients);
        $this->assertModelIs($primary, $element->for_the_attention_of->mailbox);
    }

    /** @test */
    public function cannot_change_recipients_when_updating()
    {
        $primary = Mailbox::factory()->create();
        list($cc1, $cc2) = Mailbox::factory()->count(2)->create();

        $element = Element_OphCoMessaging_Message::factory()
            ->withCCRecipients([
                [$cc1, false],
                [$cc2, false]
            ])
            ->withPrimaryRecipient($primary, false)
            ->create();

        $element->scenario = 'update';

        // Recipients still matches what the element was created with and should be valid
        $element->recipients = OphCoMessaging_Message_Recipient::model()->findAllByAttributes(['element_id' => $element->id]);
        $this->assertAttributeValid($element, 'recipients');

        // Recipients does not match what the element was created with so should be invalid
        $element->recipients = [];
        $this->assertAttributeInvalid($element, 'recipients', 'cannot have recipients changed');
    }

    /** @test */
    public function cc_enabled_set_on_save_when_message_has_cc_recipients()
    {
        $primary = Mailbox::factory()->create();
        list($cc1, $cc2) = Mailbox::factory()->count(2)->create();

        $element_without_cc = Element_OphCoMessaging_Message::factory()
            ->withPrimaryRecipient($primary, false)
            ->create();

        $element_with_cc = Element_OphCoMessaging_Message::factory()
            ->withCCRecipients([
                [$cc1, false],
                [$cc2, false]
            ])
            ->withPrimaryRecipient($primary, false)
            ->create();

        $element_without_cc->save(false);
        $element_with_cc->save(false);

        $this->assertFalse($element_without_cc->cc_enabled);
        $this->assertTrue($element_with_cc->cc_enabled);
    }

    /** @test */
    public function read_by_includes_sender_when_sender_has_read_latest_comment()
    {
        $receiver = Mailbox::factory()->create();
        $message = Element_OphCoMessaging_Message::factory()
            ->withReplyRequired()
            ->withPrimaryRecipient($receiver, true)
            ->create();

        $comment = OphCoMessaging_Message_Comment::factory()
            ->withElement($message)
            ->withSender($receiver)
            ->create([
                // marked as read indicates sender of original message has read this comment
                'marked_as_read' => false
            ]);

        $this->assertStringNotContainsString($message->sender->name, $message->getReadByLine());

        $comment->marked_as_read = true;
        $comment->save();

        $message->refresh();

        $this->assertStringContainsString($message->sender->name, $message->getReadByLine(), 'comment read tracking should indicate when sender has read the thread.');
    }
}
