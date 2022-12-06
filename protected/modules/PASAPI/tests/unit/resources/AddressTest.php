<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\tests\unit\resources;

/**
 * @group sample-data
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{
    public function test_saveModelInvalid()
    {
        $address = $this->getMockBuilder('Address')
            ->disableOriginalConstructor()
            ->setMethods(array('validate', 'getErrors'))
            ->getMock();

        $address->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(false));

        $address->expects($this->any())
            ->method('getErrors')
            ->will($this->returnValue(array('field' => array('Test Error'))));

        $test = new \OEModule\PASAPI\resources\Address('V1');
        $this->assertNull($test->saveModel($address));
        $this->assertEquals(array('field: Test Error'), $test->errors);
    }

    public function test_saveModelValid()
    {
        $address = $this->getMockBuilder('Address')
            ->disableOriginalConstructor()
            ->setMethods(array('validate', 'save'))
            ->getMock();

        $address->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(true));

        $address->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));

        $test = new \OEModule\PASAPI\resources\Address('V1');
        $this->assertTrue($test->saveModel($address));
    }
}
