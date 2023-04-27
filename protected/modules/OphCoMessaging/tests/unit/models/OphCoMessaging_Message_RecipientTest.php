<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoMessaging\tests\unit\models;

use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\Mailbox;

/**
 * class OphCoMessaging_Message_RecipientTest
 *
 * @covers OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient
 * @group shared-mailboxes
 * @group sample-data
 */
class OphCoMessaging_Message_RecipientTest extends \ModelTestCase
{
    use \WithTransactions;

    protected $element_cls = OphCoMessaging_Message_Recipient::class;

    /** @test */
    public function only_one_primary_recipient()
    {
        $message = Element_OphCoMessaging_Message::factory()
                 ->withPrimaryRecipient(Mailbox::factory(), false)
                 ->create();

        $second_primary_recipient = OphCoMessaging_Message_Recipient::factory()
                                  ->withElement($message)
                                  ->asPrimary(Mailbox::factory())
                                  ->create();

        $this->assertAttributeInvalid($second_primary_recipient, 'mailbox_id', 'primary recipient');
    }
}
