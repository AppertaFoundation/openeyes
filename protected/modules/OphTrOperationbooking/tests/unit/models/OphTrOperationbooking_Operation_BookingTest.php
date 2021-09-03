<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrOperationbooking_Operation_BookingTest  extends ActiveRecordTestCase
{
    public function getModel()
    {
        return OphTrOperationbooking_Operation_Booking::model();
    }

    protected array $columns_to_skip = [
        'admission_time',
        'session_date',
        'session_start_time',
        'session_end_time'
    ];

    public function testgetProcedureCount()
    {
        $test = new OphTrOperationbooking_Operation_Booking();
        $op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
                ->disableOriginalConstructor()
                ->setMethods(array('getProcedureCount'))
                ->getMock();

        $op->expects($this->once())
            ->method('getProcedureCount')
            ->will($this->returnValue(3));

        $test->operation = $op;

        $this->assertEquals($test->getProcedureCount(), 3);
    }

    public function testbeforeValidate_noDisplayOrder()
    {
        $test = $this->getMockBuilder('OphTrOperationbooking_Operation_Booking')
                ->disableOriginalConstructor()
                ->setMethods(array('calculateDefaultDisplayOrder', 'getIsNewRecord'))
                ->getMock();

        $test->expects($this->once())
            ->method('getIsNewRecord')
            ->will($this->returnValue(true));

        $test->expects($this->once())
            ->method('calculateDefaultDisplayOrder')
            ->will($this->returnValue('3'));

        $test->session = new OphTrOperationbooking_Operation_Session();

        $test->validate();
        $this->assertEquals(3, $test->display_order);
    }

    public function testbeforeValidate_DisplayOrder()
    {
        $test = $this->getMockBuilder('OphTrOperationbooking_Operation_Booking')
                ->disableOriginalConstructor()
                ->setMethods(array('calculateDefaultDisplayOrder'))
                ->getMock();

        $test->expects($this->never())
                ->method('calculateDefaultDisplayOrder');

        $test->session = new OphTrOperationbooking_Operation_Session();

        $test->display_order = 5;
        $test->validate();
        $this->assertEquals(5, $test->display_order);
    }

    public function testbeforeValidate_DisplayOrder_Zero()
    {
        $test = $this->getMockBuilder('OphTrOperationbooking_Operation_Booking')
                ->disableOriginalConstructor()
                ->setMethods(array('calculateDefaultDisplayOrder'))
                ->getMock();

        $test->expects($this->never())
                ->method('calculateDefaultDisplayOrder');

        $test->session = new OphTrOperationbooking_Operation_Session();

        $test->display_order = 0;
        $test->validate();
        $this->assertEquals(0, $test->display_order);
    }
}
