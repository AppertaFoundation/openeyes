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

namespace OEModule\OphCoMessaging\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;

class Element_OphCoMessaging_MessageFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCoMessaging'),
            'message_type_id' => OphCoMessaging_Message_MessageType::factory()->useExisting(),
            'message_text' => $this->faker->sentence(128),
            'sender_mailbox_id' => Mailbox::factory()->withUsers()->isPersonal(),
            'created_user_id' => function ($attributes) {
                $mailbox = Mailbox::model()->findByPk($attributes['sender_mailbox_id']);
                return $mailbox ? $mailbox->getUserForPersonalMailbox() : null;
            },
            'urgent' => false,
        ];
    }

    /**
     * @param OphCoMessaging_Message_MessageType|OphCoMessaging_Message_MessageTypeFactory|int|string $type
     * @return Element_OphCoMessaging_MessageFactory
     */
    public function withMessageType($type): self
    {
        return $this->state([
            'message_type_id' => $type
        ]);
    }

    public function withReplyRequired(): self
    {
        return $this->state([
            'message_type_id' => OphCoMessaging_Message_MessageType::factory()->useExisting(['reply_required' => true])
        ]);
    }

    public function withReplyNotRequired(): self
    {
        return $this->state([
            'message_type_id' => OphCoMessaging_Message_MessageType::factory()->useExisting(['reply_required' => false])
        ]);
    }

    public function withMessageText(string $text): self
    {
        return $this->state([
            'message_text' => $text
        ]);
    }

    public function sentToSelf($marked_as_read = false): self
    {
        return $this->afterCreating(function ($message_element) use ($marked_as_read) {
            // Work around an indirect modification issue
            $recipients = $message_element->recipients;

            $recipients[] = OphCoMessaging_Message_Recipient::factory()
                          ->withElement($message_element)
                          ->asPrimary($message_element->sender)
                          ->create([
                            'marked_as_read' => $marked_as_read
                          ]);

            $message_element->recipients = $recipients;
        });
    }

    /**
     * @param User|UserFactory|int|string $sender_user
     * @param Mailbox|MailboxFactory|int|string $sender_mailbox
     * @return Element_OphCoMessaging_MessageFactory
     */
    public function withSender($sender_user, $sender_mailbox): self
    {
        return $this->state([
            'created_user_id' => $sender_user,
            'sender_mailbox_id' => $sender_mailbox
        ]);
    }

    public function urgent(): self
    {
        return $this->state([
            'urgent' => true
        ]);
    }

    public function notUrgent(): self
    {
        return $this->state([
            'urgent' => false
        ]);
    }

    /**
     * Add the primary recipient from a mailbox with its marked as read status
     *
     * @param Mailbox|MailboxFactory|int|string $mailbox
     * @param bool $marked_as_read
     * @return Element_OphCoMessaging_MessageFactory
     */
    public function withPrimaryRecipient($mailbox = null, bool $marked_as_read = false): self
    {
        return $this->afterCreating(function ($message_element) use ($mailbox, $marked_as_read) {
            if ($mailbox === null) {
                $mailbox = Mailbox::factory()->create();
            }

            // Work around an indirect modification issue
            $recipients = $message_element->recipients;

            $recipients[] = OphCoMessaging_Message_Recipient::factory()
                          ->withElement($message_element)
                          ->asPrimary($mailbox)
                          ->create([
                            'marked_as_read' => $marked_as_read
                          ]);

            $message_element->recipients = $recipients;
        });
    }

    /**
     * Add any CC'd recipients, each from a mailbox and marked as read status
     *
     * Array structure: [[Mailbox|MailboxFactory|int|string mailbox, bool marked_as_read], ...]
     *
     * @param array $mailboxes_and_marked_as_reads
     * @return Element_OphCoMessaging_MessageFactory
     */
    public function withCCRecipients($mailboxes_and_marked_as_reads): self
    {
        return $this
            ->afterCreating(function ($message_element) use ($mailboxes_and_marked_as_reads) {
            $recipients = $message_element->recipients;

            foreach ($mailboxes_and_marked_as_reads as $mailbox_and_marked_as_read) {
                list($mailbox, $marked_as_read) = $mailbox_and_marked_as_read;

                $recipients[] = OphCoMessaging_Message_Recipient::factory()
                              ->withElement($message_element)
                              ->asCC($mailbox)
                              ->create([
                                'marked_as_read' => $marked_as_read
                              ]);
            }

            $message_element->recipients = $recipients;

            //This is necessary due to changes to the element's beforeSave function
            // which cause cc_enabled to always be false when the element is created by a factory
            $message_element->cc_enabled = true;
            $message_element->save();
        });
    }

    /**
     * Override persistInstance to call save with its third parameter, $allow_overriding, set to true.
     * Setting that parameter to true ensures that the value of created_user_id we in the definition
     * or in withSender is not overridden by the save function.
     *
     * @param mixed $instance
     * @return bool
     */
    protected function persistInstance($instance): bool
    {
        return $instance->save(false, null, true);
    }
}
