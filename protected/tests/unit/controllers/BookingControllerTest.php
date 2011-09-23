<?php
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
		'wards' => 'Ward'
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

	public function testActionSchedule_DateCheck_RendersPartial()
	{
		$lastTime = strtotime('-5 weeks');

		$event = $this->events('event1');
		$event->datetime = date('Y-m-d', $lastTime);
		$event->save();

		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();
		$sessions = $operation->getSessions();

		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));

		$_GET['operation'] = $operation->id;
		
		$firm = $this->firms('firm1');
		
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
		$sessions = $operation->getSessions();

		$_GET['operation'] = $operation->id;
		
		$firm = $this->firms('firm1');
		
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

	public function testActionReschedule_InvalidOperationId_ThrowsException()
	{
		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->never())
			->method('renderPartial');

		$this->setExpectedException('Exception', 'Operation id is invalid.');
		$mockController->actionReschedule();
	}

	public function testActionReschedule_DateCheck_RendersPartial()
	{
		$lastTime = strtotime('-5 weeks');

		$event = $this->events('event1');
		$event->datetime = date('Y-m-d', $lastTime);
		$event->save();

		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();
		$sessions = $operation->getSessions();

		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));

		$_GET['operation'] = $operation->id;

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_reschedule',
				array('operation'=>$operation, 'date'=>$thisMonth, 'sessions'=>$sessions));

		$mockController->actionReschedule();
	}

	public function testActionReschedule_ValidOperationId_RendersPartial()
	{
		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();
		$sessions = $operation->getSessions();

		$_GET['operation'] = $operation->id;

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_reschedule',
				array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions));

		$mockController->actionReschedule();
	}

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
		$sessions = $operation->getSessions();

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

		$date = date('Y-m-d', mktime(0,0,0,date('m'),$_GET['day'],date('Y')));

		$theatres = $operation->getTheatres($date);

		$mockController = $this->getMock('BookingController', array('renderPartial'),
			array('BookingController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/booking/_theatre_times',
				array('operation'=>$operation, 'date'=>$date, 'theatres'=>$theatres));

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
			'code' => '',
		);
		$status = 'available';

		$_GET['operation'] = $operationId;
		$_GET['session'] = $sessionId;

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
					'reschedule'=>false));

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
			'code' => '',
		);
		$status = 'overbooked';

		$_GET['operation'] = $operationId;
		$_GET['session'] = $sessionId;

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
					'reschedule'=>false));

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
				'element_operation_id' => $this->operations['element1']['id'],
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

	public function testActionCreate_ValidPostDataWithWardType_NoObservationWard_CreatesBooking()
	{
		$bookingCount = count($this->bookings);

		$_POST = array(
			'Booking' => array(
				'element_operation_id' => $this->operations['element1']['id'],
				'session_id' => $this->sessions[0]['id'],
			),
			'wardType' => true
		);

		$ward = $this->wards('ward3');
		
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
		$this->assertEquals($this->operations['element1']['id'], $booking->element_operation_id, 'Should have assigned the correct operation.');
		$this->assertEquals($this->sessions[0]['id'], $booking->session_id, 'Should have assigned the correct session.');
		$this->assertEquals($ward->id, $booking->ward_id, 'Should have assigned the correct ward.');
	}

	public function testActionCreate_ValidPostDataWithWardType_ObservationWard_CreatesBooking()
	{
		$bookingCount = count($this->bookings);

		$_POST = array(
			'Booking' => array(
				'element_operation_id' => $this->operations['element1']['id'],
				'session_id' => $this->sessions[0]['id'],
			),
			'wardType' => true
		);

		$ward = $this->wards('ward1');
		$ward->restriction = Ward::RESTRICTION_OBSERVATION;
		$ward->save();

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
		$this->assertEquals($this->operations['element1']['id'], $booking->element_operation_id, 'Should have assigned the correct operation.');
		$this->assertEquals($this->sessions[0]['id'], $booking->session_id, 'Should have assigned the correct session.');
		$this->assertEquals($ward->id, $booking->ward_id, 'Should have assigned the correct ward.');
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