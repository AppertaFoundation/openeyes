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

	public function testGetSessions_MonthStartEqualsMinDate_ReturnsCorrectData()
	{
		$firmId = $this->firms['firm1']['id'];
		$monthStart = date('Y-m-01');
		$minDate = $monthStart;
		
		$result = $this->service->findSessions($monthStart, $minDate, $firmId);
		
		$this->assertEquals('CDbDataReader', get_class($result));
		$this->assertEquals(2, $result->count());
	}

	public function testGetSessions_MonthStartBeforeMinDate_ReturnsCorrectData()
	{
		$firmId = $this->firms['firm1']['id'];
		$monthStart = date('Y-m-01');
		$minDate = date('Y-m-01', strtotime('+1 month'));
		
		$result = $this->service->findSessions($monthStart, $minDate, $firmId);
		
		$this->assertEquals('CDbDataReader', get_class($result));
		$this->assertEquals(2, $result->count());
	}
}
