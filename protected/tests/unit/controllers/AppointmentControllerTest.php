<?php
class AppointmentControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'sessions' => 'Session',
		'operations' => 'ElementOperation',
		'appointments' => 'Appointment',
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
}