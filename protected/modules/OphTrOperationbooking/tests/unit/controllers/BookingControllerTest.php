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
Yii::import('application.modules.OphTrOperationbooking.controllers.*');
Yii::import('application.modules.OphTrOperationbooking.helpers.*');

/**
 * Class BookingControllerTest.
 *
 * @group controllers
 */
class BookingControllerTest extends OEDbTestCase
{
    private $controller;
    private $audit;
    public $fixtures = array(
        'patient' => 'Patient',
        'episode' => 'Episode',
        'event' => 'Event',
        'et_ophtroperationbooking_operation' => 'Element_OphTrOperationbooking_Operation',
        'et_ophtroperationbooking_diagnosis' => 'Element_OphTrOperationbooking_Diagnosis',
        'et_ophtroperationbooking_scheduleope' => 'Element_OphTrOperationbooking_ScheduleOperation',
        'firm' => 'Firm',
        'ophtroperationbooking_operation_theatre' => 'OphTrOperationbooking_Operation_Theatre',
        'ophtroperationbooking_operation_sequence' => 'OphTrOperationbooking_Operation_Sequence',
        'ophtroperationbooking_operation_session' => 'OphTrOperationbooking_Operation_Session',
        'ophtroperationbooking_operation_booking' => 'OphTrOperationbooking_Operation_Booking',
        'ophtroperationbooking_operation_ward' => 'OphTrOperationbooking_Operation_Ward',
        'ophtroperationbooking_operation_procedures_procedures' => 'OphTrOperationbooking_Operation_Procedures',
    );

    public function setUp(): void
    {
        parent::setUp();

        $module = new BaseEventTypeModule('OphTrOperationbooking', null);

        $this->controller = $this->getMockBuilder('_WrapperBookingController')
            ->setConstructorArgs(array('_WrapperBookingController', $module))
            ->setMethods(array('redirect', 'processJsVars'))
            ->getMock();

        $this->audit = $this->getMockBuilder('Audit')
            ->disableOriginalConstructor()
            ->getMock();

        Yii::app()->session['selected_firm_id'] = 1;

        $this->controller->firm = Firm::model()->findByPk(1);
    }

    public function tearDown(): void
    {
        $_GET = array();
        unset(Yii::app()->session['selected_firm_id']);
        parent::tearDown();
    }

    public function testActionScheduleCancelledOperation()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 1;
        $this->controller->initAction('schedule');

        $this->controller->expects($this->once())->method('redirect')->with(array('default/view/1'));

        $this->controller->actionSchedule();
    }

    public function testActionSchedule()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 2;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        foreach (array('event', 'operation', 'firm', 'firmList', 'date', 'selectedDate', 'sessions', 'theatres', 'session', 'bookings', 'bookable', 'errors') as $key) {
            $this->assertArrayHasKey($key, $this->controller->renderParams);
        }

        $this->assertEquals($this->controller->renderParams['event']->id, 2);
        $this->assertEquals($this->controller->renderParams['operation']->id, 2);
        $this->assertEquals($this->controller->renderParams['firm']->id, 1);
        $this->assertEquals($this->controller->renderParams['firmList'], Firm::model()->listWithSpecialties);
        $this->assertEquals($this->controller->renderParams['date'], Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array(2))->minDate);
        $this->assertNull($this->controller->renderParams['selectedDate']);
        $this->assertEquals($this->controller->renderParams['sessions'], array());
        $this->assertNull($this->controller->renderParams['theatres']);
        $this->assertNull($this->controller->renderParams['session']);
        $this->assertNull($this->controller->renderParams['bookings']);
        $this->assertNull($this->controller->renderParams['bookable']);
        $this->assertNull($this->controller->renderParams['errors']);
    }

    public function testActionScheduleDatePassedAsParameter()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 2;
        $_GET['date'] = '201201';

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertArrayHasKey('date', $this->controller->renderParams);
        $this->assertEquals($this->controller->renderParams['date'], mktime(0, 0, 0, 1, 1, 2012));
    }

    public function testActionScheduleEmergencyFirm()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 2;
        $_GET['firm_id'] = 'EMG';

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertArrayHasKey('firm', $this->controller->renderParams);
        $this->assertEquals($this->controller->renderParams['firm']->name, 'Emergency List');
    }

    public function testActionSchedulePassFirmByParameter()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 2;
        $_GET['firm_id'] = 2;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertArrayHasKey('firm', $this->controller->renderParams);
        $this->assertEquals($this->controller->renderParams['firm']->id, 2);
    }

    public function testActionScheduleDayParamTheatres()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 2;
        $_GET['date'] = date('Ym');
        $_GET['day'] = date('j');
        $_GET['firm_id'] = 2;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertArrayHasKey('theatres', $this->controller->renderParams);
        $this->assertNotEmpty($this->controller->renderParams['theatres']);
        $this->assertCount(1, $this->controller->renderParams['theatres']);
        $this->assertEquals($this->controller->renderParams['theatres'][0]->id, 1);
        $this->assertEquals($this->controller->renderParams['theatres'][0]->name, 'Theatre 1');
        $this->assertNotEmpty($this->controller->renderParams['theatres'][0]->sessions);
    }

    public function testActionScheduleDayParamTheatresWithSession()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 2;
        $_GET['date'] = date('Ym');
        $_GET['day'] = date('j');
        $_GET['firm_id'] = 2;
        $_GET['session_id'] = 1;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertTrue($this->controller->renderParams['bookable']);

        foreach (array(
                'Booking' => array('admission_time', 'ward_id'),
                'Session' => array('comments'),
                'Operation' => array('comments', 'comments_rtt'),
            ) as $key => $_keys) {
            $this->assertArrayHasKey($key, $_POST);

            foreach ($_keys as $subKey) {
                $this->assertArrayHasKey($subKey, $_POST[$key]);
            }
        }

        $this->assertEquals($_POST['Booking']['admission_time'], '08:00');
        $this->assertEquals($_POST['Operation']['comments'], 'Test comments');
    }

    public function testActionScheduleUnbookableConsultantRequired()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 2;
        $_GET['date'] = date('Ym');
        $_GET['day'] = date('j');
        $_GET['firm_id'] = 2;
        $_GET['session_id'] = 2;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertFalse($this->controller->renderParams['bookable']);
    }

    public function testActionScheduleUnbookableAnaesthetistRequired()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 3;
        $_GET['date'] = date('Ym');
        $_GET['day'] = date('j');
        $_GET['firm_id'] = 2;
        $_GET['session_id'] = 3;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertFalse($this->controller->renderParams['bookable']);
    }

    public function testActionScheduleUnbookablePaediatricRequired()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 4;
        $_GET['date'] = date('Ym');
        $_GET['day'] = date('j');
        $_GET['firm_id'] = 2;
        $_GET['session_id'] = 4;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertFalse($this->controller->renderParams['bookable']);
    }

    public function testActionScheduleUnbookableGeneralAnaestheticRequired()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 5;
        $_GET['date'] = date('Ym');
        $_GET['day'] = date('j');
        $_GET['firm_id'] = 2;
        $_GET['session_id'] = 5;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertFalse($this->controller->renderParams['bookable']);
    }

    public function testActionScheduleBookingsList()
    {
        $this->markTestIncomplete();
        $_GET['id'] = 5;
        $_GET['date'] = date('Ym');
        $_GET['day'] = date('j');
        $_GET['firm_id'] = 2;
        $_GET['session_id'] = 5;

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->actionSchedule();

        $this->assertNotEmpty($this->controller->renderParams['bookings']);
    }

    public function scheduleOperation($params)
    {
        $_GET['id'] = $params['event_id'];
        $_GET['date'] = $params['date'];
        $_GET['day'] = $params['day'];
        $_GET['firm_id'] = $params['firm_id'];
        $_GET['session_id'] = $params['session_id'];

        $eo = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($params['event_id']));

        $_POST = array(
            'Booking' => array(
                'element_id' => $eo->id,
                'admission_time' => '09:14',
                'ward_id' => 1,
                'session_id' => $params['session_id'],
            ),
            'Operation' => array(
                'comments' => 'Test 123',
                'comments_rtt' => 'Test 456',
            ),
            'Session' => array(
                'comments' => 'Test 789',
            ),
        );

        $this->controller->initAction('schedule');
        $this->controller->expects($this->once())->method('processJsVars');
        $this->controller->expects($this->once())->method('redirect')->with(array('default/view/5'));
        $this->controller->actionSchedule();
    }

    public function undoLastBooking()
    {
        $booking = OphTrOperationbooking_Operation_Booking::model()->find(array('order' => 'id desc'));

        $eo = Element_OphTrOperationbooking_Operation::model()->findByPk($booking->element_id);

        $eo->status_id = 1;
        $eo->latest_booking_id = null;
        $eo->comments = null;
        $eo->comments_rtt = null;

        if (!$eo->save()) {
            throw new Exception('Unable to save operation element: '.print_r($eo->getErrors(), true));
        }

        $session = $booking->session;

        if (!$booking->delete()) {
            throw new Exception('Unable to delete booking: '.print_r($booking->getErrors(), true));
        }

        $session->comments = null;

        if (!$session->save()) {
            throw new Exception('Unable to save session: '.print_r($session->getErrors(), true));
        }
    }

    public function testActionScheduleOperation()
    {
        $this->markTestIncomplete();
        $this->scheduleOperation(array(
            'event_id' => 5,
            'firm_id' => 2,
            'session_id' => 6,
            'date' => date('Ym'),
            'day' => date('j'),
        ));

        $eo = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array(5));

        $booking = OphTrOperationbooking_Operation_Booking::model()->find(array(
            'condition' => 'element_id = :eoid',
            'params' => array(
                ':eoid' => $eo->id,
            ),
            'order' => 'id desc',
        ));

        $this->assertInstanceOf('OphTrOperationbooking_Operation_Booking', $booking);

        $this->assertEquals(6, $booking->session_id);
        $this->assertEquals('09:14:00', $booking->admission_time);
        $this->assertEquals('08:00:00', $booking->session_start_time);
        $this->assertEquals('13:00:00', $booking->session_end_time);
        $this->assertEquals('2014-01-27', $booking->session_date);
        $this->assertEquals(1, $booking->session_theatre_id);
        $this->assertEquals(1, $booking->ward_id);

        $this->assertEquals('Test 123', $eo->comments);
        $this->assertEquals('Test 456', $eo->comments_rtt);
        $this->assertEquals('Test 789', $booking->session->comments);

        $this->assertEquals(1, $eo->site_id);
        $this->assertEquals(2, $eo->status_id);

        $this->undoLastBooking();
    }
}

class _WrapperBookingController extends BookingController
{
    public $renderParams = array();

    public function initAction($action)
    {
        return parent::initAction($action);
    }

    public function render($template, $params = null, $return = false)
    {
        $this->renderParams = $params;
    }
}
