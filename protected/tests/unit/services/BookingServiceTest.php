<?php

class BookingServiceTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'sessions' => 'Session',
		'operations' => 'ElementOperation',
		'appointments' => 'Appointment',
	);

	protected $service;

	protected function setUp()
	{
		$this->service = new BookingService;
		parent::setUp();
	}
	
	public function testFindSessions_InvalidFirmId_ThrowsException()
	{
		$firmId = 9278589;
		$monthStart = date('Y-m-01');
		$minDate = $monthStart;
		
		$this->setExpectedException('Exception', 'Firm id is invalid.');
		$this->service->findSessions($monthStart, $minDate, $firmId);
	}

	public function testFindSessions_MonthStartEqualsMinDate_ReturnsCorrectData()
	{
		$firmId = $this->firms['firm1']['id'];
		$monthStart = date('Y-m-01');
		$minDate = $monthStart;
		
		$result = $this->service->findSessions($monthStart, $minDate, $firmId);
		
		$this->assertEquals('CDbDataReader', get_class($result));
		$this->assertEquals(6, $result->count());
	}

	public function testFindSessions_MonthStartBeforeMinDate_ReturnsCorrectData()
	{
		$firmId = $this->firms['firm1']['id'];
		$monthStart = date('Y-m-01');
		$minDate = date('Y-m-01', strtotime('+1 month'));
		
		$result = $this->service->findSessions($monthStart, $minDate, $firmId);
		
		$this->assertEquals('CDbDataReader', get_class($result));
		$this->assertEquals(6, $result->count());
	}
	
	public function testFindTheatres_InvalidFirmId_ThrowsException()
	{
		$firmId = 9278589;
		$monthStart = date('Y-m-01');
		$minDate = $monthStart;
		
		$this->setExpectedException('Exception', 'Firm id is invalid.');
		$this->service->findTheatres($monthStart, $firmId);
	}
	
	public function testFindTheatres_ValidInputs_ReturnsCorrectData()
	{
		$firmId = $this->firms['firm1']['id'];
		$date = date('Y-m-d', strtotime('+1 day'));
		
		$result = $this->service->findTheatres($date, $firmId);
		
		$this->assertEquals('CDbDataReader', get_class($result));
		$this->assertEquals(1, $result->count());
	}
	
	public function testFindSession_InvalidId_ReturnsCorrectData()
	{
		$sessionId = 9278589;
		
		$result = $this->service->findSession($sessionId);
		
		$this->assertEquals('CDbDataReader', get_class($result));
		$this->assertEquals(1, $result->count());
		
		$session = $result->read();
		$expected = array(
			'id' => null,
			'name' => null,
			'site_id' => null,
			'date' => null,
			'start_time' => null,
			'end_time' => null,
			'session_duration' => null,
			'appointments' => 0,
			'appointments_duration' => null,
		);
		$this->assertEquals($expected, $session);
	}
}
