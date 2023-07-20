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

namespace OEModule\OphCoMessaging\tests\unit\models;

use OE\factories\ModelFactory;
use OEModule\OphCoMessaging\components\OphCoMessaging_API;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\factories\models\MailboxFactory;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;

/**
 * class MailboxTest
 * @covers OEModule\OphCoMessaging\models\Mailbox
 * @covers OEModule\OphCoMessaging\models\MailboxUser
 * @covers OEModule\OphCoMessaging\models\MailboxTeam
 * @group shared-mailboxes
 * @group sample-data
 */
class MailboxTest extends \ModelTestCase
{
    use \WithTransactions;

    protected $element_cls = Mailbox::class;

    /** @test */
    public function gather_all_mailboxes_for_user()
    {
        $user = \User::factory()->create();
        $personal_mailbox = Mailbox::factory()->personalFor($user)->create();

        $mailboxes = Mailbox::factory()
                   ->count(3)
                   ->withUsers([$user])
                   ->create();

        $mailboxes = array_merge([$personal_mailbox], $mailboxes);
        $mailboxes_for_user = Mailbox::model()->forUser($user->id)->findAll();

        $this->assertModelArraysMatch($mailboxes, $mailboxes_for_user);
    }

    /** @test */
    public function is_personal_has_boolean_validation()
    {
        $mailbox = Mailbox::model();
        $is_personal_rules = $this->getRulesForAttribute($mailbox, 'is_personal');
        $this->assertContains('boolean', $is_personal_rules);

    }

    /** @test */
    public function received_messages_relationship()
    {
        $mailbox = Mailbox::factory()->withUsers()->create();

        $expected = Element_OphCoMessaging_Message::factory()
            ->withPrimaryRecipient($mailbox)
            ->count(3)
            ->create();

        // sent messages not part of relationship even with reply
        $element = Element_OphCoMessaging_Message::factory()
            ->withSender($mailbox->users[0], $mailbox)
            ->create();

        OphCoMessaging_Message_Comment::factory()
            ->withElement($element)
            ->create();

        $this->assertModelArraysMatch($expected, $mailbox->received_messages);
    }

    /** @test */
    public function for_message_recipients_scope()
    {
        $expected = Mailbox::factory()->withUsers()->count(2)->create();
        // others to be ignored
        Mailbox::factory()->withUsers()->count(2)->create();

        $message = Element_OphCoMessaging_Message::factory()
            ->withPrimaryRecipient($expected[0])
            ->withCCRecipients([[$expected[1], false]])
            ->create();

        $this->assertModelArraysMatch($expected, Mailbox::model()->forMessageRecipients($message->id)->findAll());
    }

    protected function getRulesForAttribute($instance, $attribute): array
    {
        return array_map(
            function ($definition) {
                return $definition[1];
            },
            array_filter($instance->rules(), function ($definition) use ($attribute) { return preg_match("/\b$attribute\b/", $definition[0]) !== false; })
        );
    }
}
