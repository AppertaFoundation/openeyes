<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class BookingControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'sessions' => 'Session',
		'events' => 'Event',
		'procedures' => 'Procedure',
		'operations' => 'ElementOperation',
		'bookings' => 'Booking',
		'theatres' => 'Theatre',
		'cancellationReasons' => 'CancellationReason',
		'users' => 'User',
		'wards' => 'Ward',
		'sites' => 'Site'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new BookingController('BookingController');
		parent::setUp();
	}

	public function dataProvider_BookingData()
	{
		return array(
			array(array()),
			array(array('Booking' => array('foo')))
		);
	}

	public function testActionSchedule_InvalidOperationId_ThrowsException()
	{
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$this->setExpectedException('Exception', 'Operation id is invalid.');
		$mockController->actionSchedule();
	}
/*
	public function testActionSchedule_DateCheck_RendersPartial()
	{
		$lastTime = strtotime('-5 weeks');

		$event = $this->events('event1');
		$event->datetime = date('Y-m-d', $lastTime);
		$event->save();

		$siteId = 1;

		$operation = $this->operations('element1');

		$minDate = $operation->getMinDate();

		$sessions = $operation->getSessions(false);

		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));

		$_GET['operation'] = $operation->id;

		$firm = $this->firms('firm1');
		$site = $this->sites('site1');

		$_GET['siteId'] = $site->id;
		$_GET['firmId'] = $firm->id;

		$criteria = new CDbCriteria;
		$criteria->order = 'name ASC';
		$firmList = Firm::model()->findAll($criteria);

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_schedule',
				array('operation'=>$operation, 'date'=>$thisMonth,
					'sessions'=>$sessions, 'firm'=>$firm, 'firmList'=>$firmList));

		$mockController->actionSchedule();
	}

	public function testActionSchedule_ValidOperationId_RendersPartial()
	{
		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();
		$sessions = $operation->getSessions(false, 1);

		$_GET['operation'] = $operation->id;

		$firm = $this->firms('firm1');

		$_GET['firmId'] = $firm->id;

		$site = $this->sites('site1');

		$criteria = new CDbCriteria;
		$criteria->order = 'name ASC';
		$firmList = Firm::model()->findAll($criteria);

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_schedule',
				array('operation'=>$operation, 'date'=>$minDate,
					'sessions'=>$sessions, 'firm'=>$firm, 'firmList'=>$firmList));

		$mockController->actionSchedule();
	}
*/
	public function testActionReschedule_InvalidOperationId_ThrowsException()
	{
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$this->setExpectedException('Exception', 'Operation id is invalid.');
		$mockController->actionReschedule();
	}
/*
	public function testActionReschedule_DateCheck_RendersPartial()
	{
		$lastTime = strtotime('-5 weeks');

		$event = $this->events('event1');
		$event->datetime = date('Y-m-d', $lastTime);
		$event->save();

		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();
		$sessions = $operation->getSessions(false, 1);

		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));

		$firm = $this->firms('firm1');

		$_GET['firmId'] = $firm->id;

		$site = $this->sites('site1');

		$criteria = new CDbCriteria;
		$criteria->order = 'name ASC';
		$firmList = Firm::model()->findAll($criteria);

		$_GET['operation'] = $operation->id;

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_reschedule',
				array('operation'=>$operation, 'date'=>$thisMonth, 'sessions'=>$sessions, 'firmId'=>null,
					'firmList'=>$firmList, 'firm'=>$firm));

		$mockController->actionReschedule();
	}

	public function testActionReschedule_ValidOperationId_RendersPartial()
	{
		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();
		$sessions = $operation->getSessions(false, 1);

		$_GET['operation'] = $operation->id;

		$firm = $this->firms('firm1');
		$_GET['firmId'] = $firm->id;

		$site = $this->sites('site1');

		$criteria = new CDbCriteria;
		$criteria->order = 'name ASC';
		$firmList = Firm::model()->findAll($criteria);

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_reschedule',
				array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions, 'firmId'=>null,
					'firmList'=>$firmList, 'firm'=>$firm));

		$mockController->actionReschedule();
	}
*/
	public function testActionSessions_InvalidOperationId_ThrowsException()
	{
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$this->setExpectedException('Exception', 'Operation id is invalid.');
		$mockController->actionSessions();
	}

	public function testActionSessions_ValidOperationId_RendersPartial()
	{
		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();
		$sessions = $operation->getSessions(false, 1);

		$_GET['operation'] = $operation->id;
		$firm = $this->firms('firm1');

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_calendar',
				array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions, 'firmId'=>$firm->id));

		$mockController->actionSessions();
	}

	public function testActionTheatres_InvalidOperationId_ThrowsExceptioN()
	{
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$this->setExpectedException('Exception', 'Operation id is invalid.');
		$mockController->actionTheatres();
	}

	public function testActionTheatres_InvalidMonth_ThrowsException()
	{
		$operation = $this->operations('element1');
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$_GET['operation'] = $operation->id;

		$this->setExpectedException('Exception', 'Month is required.');
		$mockController->actionTheatres();
	}

	public function testActionTheatres_InvalidDay_ThrowsException()
	{
		$operation = $this->operations('element1');
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$_GET['operation'] = $operation->id;
		$_GET['month'] = date('F Y');

		$this->setExpectedException('Exception', 'Day is required.');
		$mockController->actionTheatres();
	}

	public function testActionTheatres_ValidInputs_RendersPartial()
	{
		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();

		$_GET['operation'] = $operation->id;
		$_GET['month'] = date('F Y');
		$_GET['day'] = 10;
		$_GET['firm'] = false;

		$date = date('Y-m-d', mktime(0,0,0,date('m'),$_GET['day'],date('Y')));

		$theatres = $operation->getTheatres($date);

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_theatre_times',
				array('operation'=>$operation, 'date'=>$date, 'theatres'=>$theatres, 'reschedule'=>0));

		$mockController->actionTheatres();
	}

	public function testActionList_InvalidOperationId_ThrowsExceptioN()
	{
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$this->setExpectedException('Exception', 'Operation id is invalid.');
		$mockController->actionList();
	}

	public function testActionList_InvalidSession_ThrowsException()
	{
		$operation = $this->operations('element1');
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$_GET['operation'] = $operation->id;
		$_GET['session'] = 8275027957;

		$this->setExpectedException('Exception', 'Session id is invalid.');
		$mockController->actionList();
	}

	public function testActionList_MissingSession_ThrowsException()
	{
		$operation = $this->operations('element1');
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$_GET['operation'] = $operation->id;

		$this->setExpectedException('Exception', 'Session id is invalid.');
		$mockController->actionList();
	}

	public function testActionList_ValidInputs_Available_RendersPartial()
	{
		$operationId = $this->operations['element1']['id'];
		$operation = ElementOperation::model()->findByPk($operationId);
		$minDate = $operation->getMinDate();
		$sessionData = $this->sessions[0];
		$sessionId = $sessionData['id'];
		$theatre = $this->theatres['theatre1'];
		$session = array(
			'id' => $theatre['id'],
			'site_id' => $theatre['site_id'],
			'start_time' => $sessionData['start_time'],
			'end_time' => $sessionData['end_time'],
			'date' => $sessionData['date'],
			'bookings' => 1,
			'bookings_duration' => 90,
			'duration' => 240,
			'time_available' => 150,
			'status' => 'available',
			'code' => null,
			'comments' => null
		);
		$status = 'available';

		$_GET['operation'] = $operationId;
		$_GET['session'] = $sessionId;

		$site = $this->sites('site1');

		$bookings = Booking::model()->findAllByAttributes(
			array('session_id'=>$sessionId));

		$operation = $this->operations('element1');

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_list',
				array('operation'=>$operation, 'session'=>$session,
					'bookings'=>$bookings, 'minutesStatus' => $status,
					'reschedule'=>0, 'site'=>$site));

		$mockController->actionList();
	}

	public function testActionList_ValidInputs_Overbooked_RendersPartial()
	{
		$operationId = $this->operations['element1']['id'];
		$operation = ElementOperation::model()->findByPk($operationId);
		$minDate = $operation->getMinDate();
		$sessionData = $this->sessions[0];
		$sessionId = $sessionData['id'];
		$theatre = $this->theatres['theatre1'];

		$_POST['Procedures'] = array($this->procedures['procedure1']['id']);
		$operation->total_duration = 260;
		$operation->save();

		$session = array(
			'id' => $theatre['id'],
			'site_id' => $theatre['site_id'],
			'start_time' => $sessionData['start_time'],
			'end_time' => $sessionData['end_time'],
			'date' => $sessionData['date'],
			'bookings' => 1,
			'bookings_duration' => 260,
			'duration' => 240,
			'time_available' => -20,
			'status' => 'full',
			'code' => null,
			'comments' => null
		);
		$status = 'overbooked';

		$_GET['operation'] = $operationId;
		$_GET['session'] = $sessionId;

		$bookings = Booking::model()->findAllByAttributes(
			array('session_id'=>$sessionId));

		$operation = $this->operations('element1');

		$site = $this->sites('site1');

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_list',
				array('operation'=>$operation, 'session'=>$session,
					'bookings'=>$bookings, 'minutesStatus' => $status,
					'reschedule'=>false, 'site'=>$site, 'reschedule'=>0));

		$mockController->actionList();
	}

	/**
	 * @dataProvider dataProvider_BookingData
	 */
	public function testActionCreate_InvalidPostData_DoesNothing($data)
	{
		$_POST = $data;

		$mockController = $this->getMock('BookingController',
			array('redirect'), array('BookingController'));

		$mockController->expects($this->never())
			->method('redirect');

		$mockController->actionCreate();
	}

	public function testActionCreate_ValidPostData_CreatesBooking()
	{
		$bookingCount = count($this->bookings);

		$_POST = array(
			'Booking' => array(
				'element_operation_id' => $this->operations['element3']['id'],
				'session_id' => $this->sessions[0]['id'],
			),
		);

		$mockController = $this->getMock('BookingController',
			array('redirect'), array('BookingController'));

		$mockController->expects($this->once())
			->method('redirect');

		$mockController->actionCreate();

		$newBookingCount = Booking::model()->count();
		$this->assertEquals($bookingCount + 1, $newBookingCount);
	}

	public function testActionCreate_ValidPostData_FemaleWard_CreatesBooking()
	{
		$bookingCount = count($this->bookings);

		$_POST = array(
			'Booking' => array(
				'element_operation_id' => $this->operations['element4']['id'],
				'session_id' => $this->sessions[0]['id'],
			),
		);

		TheatreWardAssignment::model()->deleteAll();

		$mockController = $this->getMock('BookingController',
			array('redirect'), array('BookingController'));

		$mockController->expects($this->once())
			->method('redirect');

		$mockController->actionCreate();

		$newBookingCount = Booking::model()->count();
		$this->assertEquals($bookingCount + 1, $newBookingCount);

		$criteria = new CDbCriteria;
		$criteria->order = 'id DESC';
		$booking = Booking::model()->find($criteria);
		$this->assertEquals($this->operations['element4']['id'], $booking->element_operation_id, 'Should have assigned the correct operation.');
		$this->assertEquals($this->sessions[0]['id'], $booking->session_id, 'Should have assigned the correct session.');
		$this->assertEquals(4, $booking->ward_id, 'Should have assigned the correct ward.');
	}

	public function testActionCreate_ValidPostDataWithWardType_MaleWard_CreatesBooking()
	{
		$bookingCount = count($this->bookings);

		$_POST = array(
			'Booking' => array(
				'element_operation_id' => $this->operations['element3']['id'],
				'session_id' => $this->sessions[0]['id'],
			),
			'wardType' => true
		);

		$mockController = $this->getMock('BookingController',
			array('redirect'), array('BookingController'));

		$mockController->expects($this->once())
			->method('redirect');

		$mockController->actionCreate();

		$newBookingCount = Booking::model()->count();
		$this->assertEquals($bookingCount + 1, $newBookingCount);

		$criteria = new CDbCriteria;
		$criteria->order = 'id DESC';
		$booking = Booking::model()->find($criteria);
		$this->assertEquals($this->operations['element3']['id'], $booking->element_operation_id, 'Should have assigned the correct operation.');
		$this->assertEquals($this->sessions[0]['id'], $booking->session_id, 'Should have assigned the correct session.');
		$this->assertEquals(3, $booking->ward_id, 'Should have assigned the correct ward.');
	}

	/**
	 * @dataProvider dataProvider_BookingData
	 */
	public function testActionUpdate_InvalidPostData_DoesNothing($data)
	{
		$_POST = $data;

		$mockController = $this->getMock('BookingController',
			array('redirect'), array('BookingController'));

		$mockController->expects($this->never())
			->method('redirect');

		$mockController->actionUpdate();
	}

	public function testActionUpdate_ValidPostData_UpdatesBooking()
	{
		CancelledBooking::model()->deleteAll();

		$userInfo = $this->users['user1'];
		$identity = new UserIdentity('JoeBloggs', 'secret');
		$identity->authenticate();
		Yii::app()->user->login($identity);

		$bookingCount = count($this->bookings);

		$booking = $this->bookings('0');

		$sessionId = $this->sessions[1]['id'];

		$bookingId = $booking->id;

		$_POST['booking_id'] = $bookingId;
		$_POST['cancellation_reason'] = $this->cancellationReasons['reason1']['id'];
		$_POST['cancellation_comment'] = 'Cancellation comment';
		$_POST['Booking'] = array(
			'element_operation_id' => $this->operations['element1']['id'],
			'session_id' => $sessionId,
			'display_order' => $booking->display_order,
		);

		$this->assertNotEquals($sessionId, $booking->session_id);
		$this->assertEquals(0, CancelledBooking::model()->count(), 'Should not be any cancelled bookings yet.');

		$mockController = $this->getMock('BookingController',
			array('redirect'), array('BookingController'));

		$mockController->expects($this->once())
			->method('redirect');

		$mockController->actionUpdate();

		$booking = Booking::model()->findByPk($bookingId);

		$this->assertEquals($sessionId, $booking->session_id);

		$newBookingCount = Booking::model()->count();
		$this->assertEquals($bookingCount, $newBookingCount, 'Number of bookings should not have changed.');
		$this->assertEquals(1, CancelledBooking::model()->count(), 'Should now be a cancelled booking.');
	}
}
