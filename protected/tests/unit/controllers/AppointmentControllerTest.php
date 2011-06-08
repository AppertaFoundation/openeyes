<?php
class AppointmentControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'sessions' => 'Session',
		'events' => 'Event',
		'operations' => 'ElementOperation',
		'appointments' => 'Appointment',
		'theatres' => 'Theatre'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new AppointmentController('AppointmentController');
		parent::setUp();
	}
	
	public function testActionSchedule_InvalidOperationId_ThrowsException()
	{
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
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
		
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/appointment/_schedule', 
				array('operation'=>$operation, 'date'=>$thisMonth, 'sessions'=>$sessions));
		
		$mockController->actionSchedule();
	}
	
	public function testActionSchedule_ValidOperationId_RendersPartial()
	{
		$operation = $this->operations('element1');
		$minDate = $operation->getMinDate();
		$sessions = $operation->getSessions();
		
		$_GET['operation'] = $operation->id;
		
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/appointment/_schedule', 
				array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions));
		
		$mockController->actionSchedule();
	}
	
	public function testActionSessions_InvalidOperationId_ThrowsException()
	{
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
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
		
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/appointment/_calendar', 
				array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions));
		
		$mockController->actionSessions();
	}
	
	public function testActionTheatres_InvalidOperationId_ThrowsExceptioN()
	{
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		
		$this->setExpectedException('Exception', 'Operation id is invalid.');
		$mockController->actionTheatres();
	}
	
	public function testActionTheatres_InvalidMonth_ThrowsException()
	{
		$operation = $this->operations('element1');
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		
		$_GET['operation'] = $operation->id;
		
		$this->setExpectedException('Exception', 'Month is required.');
		$mockController->actionTheatres();
	}
	
	public function testActionTheatres_InvalidDay_ThrowsException()
	{
		$operation = $this->operations('element1');
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
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
		
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/appointment/_theatre_times', 
				array('operation'=>$operation, 'date'=>$date, 'theatres'=>$theatres));
		
		$mockController->actionTheatres();
	}
	
	public function testActionList_InvalidOperationId_ThrowsExceptioN()
	{
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		
		$this->setExpectedException('Exception', 'Operation id is invalid.');
		$mockController->actionList();
	}
	
	public function testActionList_InvalidSession_ThrowsException()
	{
		$operation = $this->operations('element1');
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
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
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		
		$_GET['operation'] = $operation->id;
		
		$this->setExpectedException('Exception', 'Session id is invalid.');
		$mockController->actionList();
	}
	
	public function testActionList_ValidInputs_RendersPartial()
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
			'appointments' => 1,
			'appointments_duration' => 90,
			'duration' => 240,
			'time_available' => 150,
			'status' => 'available',
		);
		
		$_GET['operation'] = $operationId;
		$_GET['session'] = $sessionId;
		
		$appointments = Appointment::model()->findAllByAttributes(
			array('session_id'=>$sessionId));
		
		$operation = $this->operations('element1');
		
		$mockController = $this->getMock('AppointmentController', array('renderPartial'),
			array('AppointmentController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('/appointment/_list', 
				array('operation'=>$operation, 'session'=>$session, 'appointments'=>$appointments));
		
		$mockController->actionList();
	}
}