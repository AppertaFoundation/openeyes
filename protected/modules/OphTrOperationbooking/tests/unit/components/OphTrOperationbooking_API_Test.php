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

class OphTrOperationbooking_API_Test extends OEDbTestCase
{
    private $api;

    public $fixtures = array(
        'event_types' => 'EventType',
        'events' => 'Event',
        'episodes' => 'Episode',
        'patients' => 'Patient',
        'el_o' => 'Element_OphTrOperationbooking_Operation',
        'el_d' => 'Element_OphTrOperationbooking_Diagnosis',
        'statuses' => 'OphTrOperationbooking_Operation_Status',
        'disorders' => 'Disorder',
        'bookings' => 'OphTrOperationbooking_Operation_Booking',
        'procs' => 'Procedure',
        'op_procedures' => 'OphTrOperationbooking_Operation_Procedures',
        'firms' => 'Firm',
        'sites' => 'Site',
        'theatres' => 'OphTrOperationbooking_Operation_Theatre',
        'anaesthetic_type' => 'OphTrOperationbooking_AnaestheticAnaestheticType',
        'sessions' => 'OphTrOperationbooking_Operation_Session',
    );

    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OphTrOperationbooking');
    }

    public function setUp(): void
    {
        parent::setUp();

        Yii::app()->session['selected_firm_id'] = 2;
        $this->api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
    }

    public function testGetLatestOperationBookingDiagnosis()
    {
        $this->assertEquals('Myopia', $this->api->getLatestCompletedOperationBookingDiagnosis($this->patients('patient3')));
    }

    /**
     * Case: the patient doesn't have any operation booking event
     * then the diagnosis is taken from the episode table
     *
     */
    public function testGetLatestOperationBookingDiagnosis_DefaultToEpisode()
    {
        $this->assertEquals('Left diabetes mellitus type 2', $this->api->getLatestCompletedOperationBookingDiagnosis($this->patients('patient5')));
    }

    public function testGetBookingsForEpisode()
    {
        $bookings = $this->api->getBookingsForEpisode(1);

        $this->assertCount(1, $bookings);
        $this->assertInstanceOf('OphTrOperationbooking_Operation_Booking', $bookings[0]);
        $this->assertEquals(1, $bookings[0]->id);
    }

    public function testGetOperationsForEpisode()
    {
        $operations = $this->api->getOperationsForEpisode($this->patients('patient3'));

        $this->assertCount(3, $operations);
        $this->assertInstanceOf('Element_OphTrOperationbooking_Operation', $operations[0]);
        $this->assertEquals(5, $operations[0]->id);
    }

    public function testGetScheduledOpenOperations()
    {
        $operations = $this->api->getScheduledOpenOperations($this->patients('patient2'));

        $this->assertCount(1, $operations);
        $this->assertEquals(8, $operations[0]->id);
    }

    public function testSetOpenOperations()
    {
        $operations = $this->api->getScheduledOpenOperations($this->patients('patient6'));

        $this->assertCount(3, $operations);
        $this->assertEquals(14, $operations[0]->id);
        $this->assertEquals(15, $operations[1]->id);
    }

    public function testGetOperationProcedures()
    {
        $procs = $this->api->getOperationProcedures(5);

        $this->assertCount(1, $procs);
        $this->assertEquals(1, $procs[0]->id);
    }

    public function testGetOperationForEvent()
    {
        $operation = $this->api->getOperationForEvent(11);

        $this->assertEquals(11, $operation->id);
    }

    public function testSetOperationStatus()
    {
        $eo = $this->el_o('eo5');

        foreach ($this->statuses as $status) {
            $this->api->setOperationStatus($eo->event_id, $status['name']);

            $this->assertEquals($status['name'], Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($eo->event_id))->status->name);
        }
    }

    public function testSetOperationStatus_ScheduledOrRescheduled_Scheduled()
    {
        $eo = $this->el_o('eo5');

        $this->api->setOperationStatus($eo->event_id, 'Scheduled or Rescheduled');

        $this->assertEquals('Scheduled', Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($eo->event_id))->status->name);
    }

    public function testSetOperationStatus_ScheduledOrRescheduled_Rescheduled()
    {
        $eo = $this->el_o('eo12');

        $this->api->setOperationStatus($eo->event_id, 'Scheduled or Rescheduled');

        $this->assertEquals('Rescheduled', Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($eo->event_id))->status->name);
    }

    public function testGetProceduresForOperation()
    {
        $procs = $this->api->getProceduresForOperation(5);

        $this->assertCount(1, $procs);
        $this->assertEquals(1, $procs[0]->id);
    }

    public function testGetEyeForOperation()
    {
        $eye = $this->api->getEyeForOperation(5);

        $this->assertInstanceOf('Eye', $eye);
        $this->assertEquals('Left', $eye->name);
    }

    public function testGetMostRecentBookingForEpisode()
    {
        $booking = $this->api->getMostRecentBookingForEpisode($this->episodes('episode6'));

        $this->assertEquals(7, $booking->id);
        $this->assertEquals(12, $booking->element_id);
    }

    public function testGetLetterProcedures()
    {
        $api = $this->getMockBuilder('OphTrOperationbooking_API')
            ->setMethods(array('getEventType'))
            ->getMock();

        $et = $this->event_types('event_type2');

        $api->expects($this->once())
            ->method('getEventType')
            ->will($this->returnValue($et));

        $this->assertEquals('left foobar procedure, left test procedure', $api->getLetterProcedures($this->patients('patient6')));
    }

    public function testGetLetterProceduresSameDay()
    {
        $this->assertEquals('left foobar procedure', $this->api->getLetterProceduresSameDay($this->patients('patient3')));
    }

    public function testGetAdmissionDate()
    {
        $this->assertEquals('26 Jun 2015', $this->api->getAdmissionDate($this->patients('patient6')));
    }

    public function testFindSiteForBookingEvent()
    {
        $site = $this->api->findSiteForBookingEvent($this->events('event1'));

        $this->assertInstanceOf('Site', $site);
        $this->assertEquals(1, $site->id);
        $this->assertEquals('City Road', $site->name);
    }

    public function testFindTheatreForBookingEvent()
    {
        $this->assertEquals(1, $this->api->findTheatreForBookingEvent($this->events('event1'))->id);
    }

    public function testCanUpdate()
    {
        $this->assertTrue($this->api->canUpdate($this->events('event7')->id));
        $this->assertTrue($this->api->canUpdate($this->events('event8')->id));
        $this->assertTrue($this->api->canUpdate($this->events('event9')->id));
        $this->assertTrue($this->api->canUpdate($this->events('event10')->id));
        $this->assertFalse($this->api->canUpdate($this->events('event11')->id));
        $this->assertFalse($this->api->canUpdate($this->events('event12')->id));
    }

    public function testShowDeleteIcon()
    {
        $this->assertTrue($this->api->showDeleteIcon($this->events('event7')->id));
        $this->assertTrue($this->api->showDeleteIcon($this->events('event8')->id));
        $this->assertTrue($this->api->showDeleteIcon($this->events('event9')->id));
        $this->assertTrue($this->api->showDeleteIcon($this->events('event10')->id));
        $this->assertFalse($this->api->showDeleteIcon($this->events('event11')->id));
        $this->assertFalse($this->api->showDeleteIcon($this->events('event12')->id));
    }

    public function testFindBookingByEventID()
    {
        $booking = $this->api->findBookingByEventID(7);

        $this->assertInstanceOf('OphTrOperationbooking_Operation_Booking', $booking);
        $this->assertEquals(2, $booking->id);
    }

    public function testAutoScheduleOperationBookings()
    {
        $result = $this->api->autoScheduleOperationBookings($this->episodes('episode5'));

        $this->assertTrue($result);
    }

    public function testAutoScheduleOperationBookings_noAvailableSessions()
    {
        $result = $this->api->autoScheduleOperationBookings($this->episodes('episode4'));

        $this->assertIsArray($result);
    }

    public function testGetLastNonCompleteStatus()
    {
        $this->assertEquals(2, $this->api->getLastNonCompleteStatus($this->events('event8')->id));
    }

    public function testGetLastNonCompleteStatus_Default()
    {
        $this->assertEquals(2, $this->api->getLastNonCompleteStatus($this->events('event2')->id));
    }
}
