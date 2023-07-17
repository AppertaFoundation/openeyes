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

namespace OEModule\OphCoMessaging\tests\traits;

use Event;
use MakesApplicationRequests;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\Mailbox;
use User;

trait MakesMessagingRequests
{
    use MakesApplicationRequests;

    protected function markMessageReadWithRequest(Element_OphCoMessaging_Message $message, $user, ?Mailbox $for_mailbox = null)
    {
        $this->actingAs($user)
            ->get($this->urlToMarkMessageRead($message), $for_mailbox);
    }

    protected function urlToMarkMessageRead(Event|Element_OphCoMessaging_Message $message, ?Mailbox $for_mailbox = null)
    {
        $event_id = $message instanceof Event ? $message->id : $message->event_id;

        $base_url = "/OphCoMessaging/default/markRead?id=$event_id";

        if (!$for_mailbox) {
            return $base_url;
        }

        return $base_url . "&mailbox_id={$for_mailbox->id}";
    }

    protected function markMessageUnreadWithRequest(Element_OphCoMessaging_Message $message, $user, ?Mailbox $for_mailbox = null)
    {
        $this->actingAs($user)
            ->get($this->urlToMarkMessageUnread($message, $for_mailbox));
    }

    protected function urlToMarkMessageUnread(Event|Element_OphCoMessaging_Message $message, ?Mailbox $for_mailbox = null)
    {
        $event_id = $message instanceof Event ? $message->id : $message->event_id;

        $base_url = "/OphCoMessaging/default/markUnread?id=$event_id";

        if (!$for_mailbox) {
            return $base_url;
        }

        return $base_url . "&mailbox_id={$for_mailbox->id}";
    }

    protected function postCommentWithRequestOn(
        Element_OphCoMessaging_Message $message,
        User $user,
        ?Mailbox $mailbox = null,
        ?string $text = null
    ) {
        $this->actingAs($user)
            ->post(
                $this->urlToPostComment($message),
                $this->generateCommentPostData($mailbox ?? $user->personalMailbox, $text)
            )
            ->assertRedirect();
    }

    protected function urlToPostComment(Event|Element_OphCoMessaging_Message $message): string
    {
        $event_id = $message instanceof Event ? $message->id : $message->event_id;

        return "/OphCoMessaging/default/addComment?id=$event_id";
    }

    protected function generateCommentPostData(Mailbox $from_mailbox, ?string $text = null): array
    {
        return [
            'mailbox_id' => $from_mailbox->id,
            'OEModule_OphCoMessaging_models_OphCoMessaging_Message_Comment' => [
                'comment_text' => $text ?? $this->faker->sentence()
            ]
        ];
    }

}
