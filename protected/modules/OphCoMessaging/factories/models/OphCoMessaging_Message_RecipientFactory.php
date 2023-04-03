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

use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;

class OphCoMessaging_Message_RecipientFactory extends ModelFactory
{
    /**
     * Defaults to being a primary recipient, marked unread
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'element_id' => ModelFactory::factoryFor(Element_OphCoMessaging_Message::class),
            'mailbox_id' => ModelFactory::factoryFor(Mailbox::class)->useExisting(),
            'marked_as_read' => false,
            'primary_recipient' => true
        ];
    }

    /**
     * @param Element_OphCoMessaging_Message|Element_OphCoMessaging_MessageFactory|int|string $mailbox
     * @return OphCoMessaging_Message_RecipientFactory
     */
    public function withElement($element): self
    {
        return $this->state([
            'element_id' => $element
        ]);
    }

    /**
     * @param Mailbox|MailboxFactory|int|string $mailbox
     * @return OphCoMessaging_Message_RecipientFactory
     */
    public function asPrimary($mailbox = null): self
    {
        return $this->state([
            'mailbox_id' => $mailbox,
            'primary_recipient' => true
        ]);
    }

    /**
     * @param Mailbox|MailboxFactory|int|string $mailbox
     * @return OphCoMessaging_Message_RecipientFactory
     */
    public function asCC($mailbox): self
    {
        return $this->state([
            'mailbox_id' => $mailbox,
            'primary_recipient' => false
        ]);
    }
}
