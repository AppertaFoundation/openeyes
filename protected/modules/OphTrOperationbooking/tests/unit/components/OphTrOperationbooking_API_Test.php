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
require_once Yii::app()->basePath.'/modules/OphTrOperationbooking/components/OphTrOperationbooking_API.php';

class OphTrOperationbooking_API_Test extends CDbTestCase
{
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
    );

    public function testGetLatestOperationBookingDiagnosis()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $this->assertEquals('Myopia', $api->getLatestCompletedOperationBookingDiagnosis($this->patients('patient3')));
    }

    public function testGetLatestOperationBookingDiagnosis_DefaultToEpisode()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        Yii::app()->session['selected_firm_id'] = 2;

        $this->assertEquals('Left diabetes mellitus type 2', $api->getLatestCompletedOperationBookingDiagnosis($this->patients('patient5')));
    }

    public function testGetBookingsForEpisode()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $bookings = $api->getBookingsForEpisode(1);

        $this->assertCount(1, $bookings);
        $this->assertInstanceOf('OphTrOperationbooking_Operation_Booking', $bookings[0]);
        $this->assertEquals(1, $bookings[0]->id);
    }

    public function testGetOperationsForEpisode()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $operations = $api->getOperationsForEpisode(1);

        $this->assertCount(1, $operations);
        $this->assertInstanceOf('Element_OphTrOperationbooking_Operation', $operations[0]);
        $this->assertEquals(13, $operations[0]->id);
    }

    public function testGetOpenBookingsForEpisode()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $bookings = $api->getOpenBookingsForEpisode(6);

        $this->assertCount(2, $bookings);

        $this->assertInstanceOf('OphTrOperationbooking_Operation_Booking', $bookings[0]);
        $this->assertEquals(5, $bookings[0]->id);

        $this->assertInstanceOf('OphTrOperationbooking_Operation_Booking', $bookings[1]);
        $this->assertEquals(8, $bookings[1]->id);
    }

    public function testGetOperationProcedures()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $procs = $api->getOperationProcedures(5);

        $this->assertCount(1, $procs);
        $this->assertEquals(1, $procs[0]->id);
    }

    public function testSetOperationStatus()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $eo = $this->el_o('eo5');

        foreach ($this->statuses as $status) {
            $api->setOperationStatus($eo->event_id, $status['name']);

            $this->assertEquals($status['name'], Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($eo->event_id))->status->name);
        }
    }

    public function testSetOperationStatus_ScheduledOrRescheduled_Scheduled()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $eo = $this->el_o('eo5');

        $api->setOperationStatus($eo->event_id, 'Scheduled or Rescheduled');

        $this->assertEquals('Scheduled', Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($eo->event_id))->status->name);
    }

    public function testSetOperationStatus_ScheduledOrRescheduled_Rescheduled()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $eo = $this->el_o('eo12');

        $api->setOperationStatus($eo->event_id, 'Scheduled or Rescheduled');

        $this->assertEquals('Rescheduled', Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($eo->event_id))->status->name);
    }

    public function testGetProceduresForOperation()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $procs = $api->getProceduresForOperation(5);

        $this->assertCount(1, $procs);
        $this->assertEquals(1, $procs[0]->id);
    }

    public function testGetEyeForOperation()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $eye = $api->getEyeForOperation(5);

        $this->assertInstanceOf('Eye', $eye);
        $this->assertEquals('Left', $eye->name);
    }

    public function testGetMostRecentBookingForEpisode()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $booking = $api->getMostRecentBookingForEpisode($this->episodes('episode6'));

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

        Yii::app()->session['selected_firm_id'] = 2;

        $this->assertEquals('left foobar procedure, left test procedure', $api->getLetterProcedures($this->patients('patient6')));
    }

    public function testGetAdmissionDate()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        Yii::app()->session['selected_firm_id'] = 2;

        $this->assertEquals('26 Jun 2015', $api->getAdmissionDate($this->patients('patient6')));
    }

    public function testFindSiteForBookingEvent()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $site = $api->findSiteForBookingEvent($this->events('event1'));

        $this->assertInstanceOf('Site', $site);
        $this->assertEquals(1, $site->id);
        $this->assertEquals('City Road', $site->name);
    }

    public function testCanUpdate()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $this->assertTrue($api->canUpdate($this->events('event7')->id));
        $this->assertTrue($api->canUpdate($this->events('event8')->id));
        $this->assertTrue($api->canUpdate($this->events('event9')->id));
        $this->assertTrue($api->canUpdate($this->events('event10')->id));
        $this->assertFalse($api->canUpdate($this->events('event11')->id));
        $this->assertFalse($api->canUpdate($this->events('event12')->id));
    }

    public function testShowDeleteIcon()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $this->assertTrue($api->showDeleteIcon($this->events('event7')->id));
        $this->assertTrue($api->showDeleteIcon($this->events('event8')->id));
        $this->assertTrue($api->showDeleteIcon($this->events('event9')->id));
        $this->assertTrue($api->showDeleteIcon($this->events('event10')->id));
        $this->assertFalse($api->showDeleteIcon($this->events('event11')->id));
        $this->assertFalse($api->showDeleteIcon($this->events('event12')->id));
    }

    public function testFindBookingByEventID()
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        $booking = $api->findBookingByEventID(7);

        $this->assertInstanceOf('OphTrOperationbooking_Operation_Booking', $booking);
        $this->assertEquals(2, $booking->id);
    }
}
