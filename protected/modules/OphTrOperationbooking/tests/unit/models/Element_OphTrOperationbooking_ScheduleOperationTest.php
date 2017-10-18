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
class Element_OphTrOperationbooking_ScheduleOperationTest extends CDbTestCase
{
    public $fixtures = array(
        'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason',
    );

    public function testUnavailableDatesCantOverlap()
    {
        $unavailable1 = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $unavailable1->attributes = array(
            'start_date' => '2014-04-02',
            'end_date' => '2014-04-10',
            'reason_id' => 1,
        );
        $unavailable2 = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $unavailable2->attributes = array(
                'start_date' => '2014-04-07',
                'end_date' => '2014-04-15',
                'reason_id' => 1,
        );
        $test = new Element_OphTrOperationbooking_ScheduleOperation();
        $test->event_id = 1;
        $test->patient_unavailables = array($unavailable1, $unavailable2);
        $test->schedule_options_id = 1;

        $this->assertFalse($test->validate());
        // ensure patient_unavailables is only attribute with errors when rest is valid
        $errs = $test->getErrors();
        $this->assertArrayHasKey('patient_unavailables', $errs);
        $this->assertEquals(count(array_keys($errs)), 1);
    }

    public function testUnavailablesAreValidated()
    {
        $unavailable = $this->getMockBuilder('OphTrOperationbooking_ScheduleOperation_PatientUnavailable')
                    ->disableOriginalConstructor()
                    ->setMethods(array('validate'))
                    ->getMock();
        $unavailable->expects($this->once())
                ->method('validate')
                ->will($this->returnValue(true));

        $test = new Element_OphTrOperationbooking_ScheduleOperation();
        $test->event_id = 1;
        $test->patient_unavailables = array($unavailable);
        $test->schedule_options_id = 1;

        $test->validate();
    }

    public function testIsPatientUnavailable()
    {
        $unavailables_data = array(
                array(
                        'start_date' => '2014-04-03',
                        'end_date' => '2014-04-03',
                ),
                array(
                        'start_date' => '2014-04-12',
                        'end_date' => '2014-05-03',
                ),
        );

        $unavailables = array();
        foreach ($unavailables_data as $data) {
            $u = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
            $u->attributes = $data;
            $unavailables[] = $u;
        }

        $test1 = new Element_OphTrOperationbooking_ScheduleOperation();
        $test1->patient_unavailables = array($unavailables[0]);
        $this->assertFalse($test1->isPatientAvailable('2014-04-03'));
        $this->assertTrue($test1->isPatientAvailable('2014-04-04'));

        $test2 = new Element_OphTrOperationbooking_ScheduleOperation();
        $test2->patient_unavailables = array($unavailables[0], $unavailables[1]);
        $this->assertTrue($test2->isPatientAvailable('2014-04-02'));
        $this->assertFalse($test2->isPatientAvailable('2014-04-03'));
        $this->assertTrue($test2->isPatientAvailable('2014-04-09'));
        $this->assertFalse($test2->isPatientAvailable('2014-04-12'));
        $this->assertFalse($test2->isPatientAvailable('2014-04-19'));
        $this->assertFalse($test2->isPatientAvailable('2014-05-03'));
        $this->assertTrue($test2->isPatientAvailable('2014-05-04'));

        // check it works when no unavailable data
        $test3 = new Element_OphTrOperationbooking_ScheduleOperation();
        $this->assertTrue($test3->isPatientAvailable('2041-04-02'));
    }

    public function testCantSetUnavailableCoveringBookedOperationDate()
    {
        $u = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $u->start_date = '2014-04-03';
        $u->end_date = '2014-04-03';
        $u->reason_id = 1;

        $test1 = $this->getMockBuilder('Element_OphTrOperationbooking_ScheduleOperation')
                ->disableOriginalConstructor()
                ->setMethods(array('getCurrentBooking'))
                ->getMock();
        $test1->patient_unavailables = array($u);
        $test1->schedule_options_id = 1;

        $b = new OphTrOperationbooking_Operation_Booking();
        $b->session_date = '2014-04-03';

        $test1->expects($this->once())
            ->method('getCurrentBooking')
            ->will($this->returnValue($b));

        $this->assertFalse($test1->validate());
        $errs = $test1->getErrors();
        $this->assertArrayHasKey('patient_unavailables', $errs);
        $this->assertEquals(count(array_keys($errs)), 1);
    }
}
