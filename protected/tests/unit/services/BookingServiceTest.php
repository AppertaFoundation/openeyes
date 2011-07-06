<?php

class BookingServiceTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'sessions' => 'Session',
		'operations' => 'ElementOperation',
		'bookings' => 'Booking',
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

	// @todo: do this in a better way than just re-running the query
	public function testFindSessions_MonthStartEqualsMinDate_ReturnsCorrectData()
	{
		$firmId = $this->firms['firm1']['id'];
		$monthStart = date('Y-m-01');
		$minDate = $monthStart;
		$monthEnd = date('Y-m-t');
		
		$sql = "SELECT s.*, TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				COUNT(a.id) AS bookings, 
				SUM(o.total_duration) AS bookings_duration
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id
			JOIN `booking` `a` ON s.id = a.session_id
			JOIN `element_operation` `o` ON a.element_operation_id = o.id
			WHERE s.date BETWEEN CAST('" . $monthStart . "' AS DATE) AND 
				CAST('" . $monthEnd . "' AS DATE) AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)
			GROUP BY s.id 
		UNION 
			SELECT s.*, TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				0 AS bookings, 0 AS bookings_duration
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id
			WHERE s.id NOT IN (SELECT DISTINCT (session_id) FROM booking) AND 
				s.date BETWEEN CAST('" . $monthStart . "' AS DATE) AND 
				CAST('" . $monthEnd . "' AS DATE) AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)
		ORDER BY WEEKDAY( DATE ) ASC";
		
		$command = Yii::app()->db->createCommand($sql);
		$reader = $command->query();
		
		$result = $this->service->findSessions($monthStart, $minDate, $firmId);
		
		$this->assertEquals('CDbDataReader', get_class($result));
		$this->assertEquals($reader->rowCount, $result->count());
	}

	// @todo: do this in a better way than just re-running the query
	public function testFindSessions_MonthStartBeforeMinDate_ReturnsCorrectData()
	{
		$firmId = $this->firms['firm1']['id'];
		$monthStart = date('Y-m-01');
		$minDate = date('Y-m-01', strtotime('+1 month'));
		$monthEnd = date('Y-m-t');
		
		$sql = "SELECT s.*, TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				COUNT(a.id) AS bookings, 
				SUM(o.total_duration) AS bookings_duration
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id
			JOIN `booking` `a` ON s.id = a.session_id
			JOIN `element_operation` `o` ON a.element_operation_id = o.id
			WHERE s.date BETWEEN CAST('" . $monthStart . "' AS DATE) AND 
				CAST('" . $monthEnd . "' AS DATE) AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)
			GROUP BY s.id 
		UNION 
			SELECT s.*, TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				0 AS bookings, 0 AS bookings_duration
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id
			WHERE s.id NOT IN (SELECT DISTINCT (session_id) FROM booking) AND 
				s.date BETWEEN CAST('" . $monthStart . "' AS DATE) AND 
				CAST('" . $monthEnd . "' AS DATE) AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)
		ORDER BY WEEKDAY( DATE ) ASC";
		
		$command = Yii::app()->db->createCommand($sql);
		$reader = $command->query();
		
		$result = $this->service->findSessions($monthStart, $minDate, $firmId);
		
		$this->assertEquals('CDbDataReader', get_class($result));
		$this->assertEquals($reader->rowCount, $result->count());
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
			'bookings' => 0,
			'bookings_duration' => null,
		);
		$this->assertEquals($expected, $session);
	}
}
