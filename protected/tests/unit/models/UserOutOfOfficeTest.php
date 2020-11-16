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

class UserOutOfOfficeTest extends ActiveRecordTestCase
{
    public $model;
    public $fixtures = array(
        'user_out_of_office' => 'UserOutOfOffice',
    );

    public function getModel()
    {
        return UserOutOfOffice::model();
    }

    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->user_out_of_office('user1')->validate());
        $this->assertTrue($this->user_out_of_office('user3')->validate());
    }

    public function testRequiredIfEnabledError()
    {
        $user_out_of_office = new UserOutOfOffice();
        $user_out_of_office->setAttributes($this->user_out_of_office('user2')->getAttributes());
        $user_out_of_office->save();
        $errors = $user_out_of_office->getErrors();
        $this->assertArrayHasKey('From', $errors);
        $this->assertEquals($errors['From'][0], 'From cannot be blank');
        $this->assertArrayHasKey('To', $errors);
        $this->assertEquals($errors['To'][0], 'To cannot be blank');
        $this->assertArrayHasKey('Alternate User', $errors);
        $this->assertEquals($errors['Alternate User'][0], 'Alternate User cannot be blank');
    }

    public function testOutOfOfficeDurationValidator()
    {
        $user_out_of_office = new UserOutOfOffice();
        $user_out_of_office->setAttributes($this->user_out_of_office('user4')->getAttributes());
        $user_out_of_office->save();
        $errors = $user_out_of_office->getErrors();
        $this->assertArrayHasKey('Out of office duration', $errors);
        $this->assertEquals($errors['Out of office duration'][0], 'To date cannot be before 31 Aug 2020');
    }
}
