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

use OE\factories\ModelFactory;

use \OEModule\OphCoMessaging\models\Mailbox;

/**
 * class UserOutOfOfficeTest
 * @covers UserOutOfOffice
 * @group shared-mailboxes
 * @group sample-data
 */
class UserOutOfOfficeTest extends ModelTestCase
{
    use WithTransactions;
    use \WithFaker;

    protected $element_cls = UserOutOfOffice::class;

    /** @test */
    public function model_must_be_completed_if_enabled()
    {
        $empty_instance = UserOutOfOffice::factory()
                ->make([
                    'user_id' => User::factory()->useExisting(),
                    'enabled' => true
                ]);

        $this->assertAttributeInvalid($empty_instance, 'From', 'From cannot be blank');
        $this->assertAttributeHasError($empty_instance, 'To', 'To cannot be blank');
        $this->assertAttributeHasError($empty_instance, 'Alternate User', 'Alternate User cannot be blank');
    }

    /** @test */
    public function end_date_must_be_after_start_date()
    {
        $instance = UserOutOfOffice::factory()
               ->enabled()
               ->withDates('2020-08-31', '2020-07-31')
               ->create();

        $this->assertAttributeInvalid($instance, 'Out of office duration', 'To date cannot be before');
    }

    /** @test */
    public function mailbox_out_of_office()
    {
        $out_user = \User::factory()->create();
        $out_mailbox = Mailbox::factory()->personalFor($out_user)->create();
        $alternate_user = \User::factory()->create();
        Mailbox::factory()->personalFor($alternate_user)->create();

        $from = $this->faker->dateTimeBetween('-1 week', '-1 day')->format('Y-m-d');
        $to = $this->faker->dateTimeBetween('+1 day', '+1 week')->format('Y-m-d');

        $out_of_office = UserOutOfOffice::factory()
            ->withUser($out_user)
            ->enabled()
            ->withDates($from, $to, $alternate_user)
            ->create();

        $out_mailbox = Mailbox::model()->forPersonalMailbox($out_user->id)->find();

        $response = $out_of_office->checkUserOutOfOfficeViaMailbox($out_mailbox->id);

        $this->assertNotNull($response, 'There should be a message for the out of office user');
        $this->assertStringContainsString($out_user->getFullnameAndTitle(), $response, 'Out of office user not mentioned in OoO message');
        $this->assertStringContainsString($alternate_user->getFullnameAndTitle(), $response, 'Alternative user not mentioned in OoO message');
    }
}
