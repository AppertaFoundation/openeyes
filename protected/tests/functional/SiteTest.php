<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class SiteTest.
 *
 * @group functional
 */
class SiteTest extends WebTestCase
{
    public $fixtures = array(
        'users' => 'User',
    );

    public function testIndex()
    {
        $this->open('');
        $this->assertTextPresent('Test patient');
    }

    public function testLoginLogout()
    {
        $this->open('');
        // ensure the user is logged out
        if ($this->isTextPresent('Logout')) {
            $this->clickAndWait('link=Logout');
        }

        // test login process, including validation
        $this->clickAndWait('link=Login');
        $this->assertElementPresent('name=LoginForm[username]');
        $this->type('name=LoginForm[username]', 'admin');
        $this->clickAndWait("//input[@value='Login']");
        $this->assertTextPresent('Password cannot be blank.');
        $this->type('name=LoginForm[password]', 'admin');
        $this->clickAndWait("//input[@value='Login']");
        $this->assertTextNotPresent('Password cannot be blank.');
        $this->assertTextPresent('Logout');

        // test logout process
        $this->assertTextNotPresent('Login');
        $this->clickAndWait('link=Logout (admin)');
        $this->assertTextPresent('Login');
    }
}
