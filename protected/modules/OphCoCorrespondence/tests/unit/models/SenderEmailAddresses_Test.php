<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class SenderEmailAddresses_Test extends ActiveRecordTestCase
{
    /**
     * @var SenderEmailAddresses
     */
    protected $model;
    public $fixtures = array(
        'ophcocorrespondence_sender_email_addresses' => 'SenderEmailAddresses',
    );

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new SenderEmailAddresses();
    }

    /**
     * @covers SenderEmailAddresses::model
     */
    public function testModel()
    {
        $this->assertEquals('SenderEmailAddresses', get_class(SenderEmailAddresses::model()), 'Class name should match model.');
    }

    /**
     * @covers SenderEmailAddresses::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('ophcocorrespondence_sender_email_addresses', $this->model->tableName());
    }

    /**
     * @covers SenderEmailAddresses::rules
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->ophcocorrespondence_sender_email_addresses('address1')->validate());
        $this->assertEmpty($this->ophcocorrespondence_sender_email_addresses('address1')->errors);
        $this->assertTrue($this->ophcocorrespondence_sender_email_addresses('address2')->validate());
        $this->assertEmpty($this->ophcocorrespondence_sender_email_addresses('address2')->errors);
    }

    /**
     * @covers \SenderEmailAddresses::rules
     */
    public function testDomainAttributeErrorMsg()
    {
        $this->ophcocorrespondence_sender_email_addresses('address3')->validate();
        $errorMessage = $this->ophcocorrespondence_sender_email_addresses('address3')->getErrors('domain');
        $this->assertEquals($errorMessage, array("domain should only contain either * or @domain.com"));
    }
}
