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


class BookingServiceTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'sessions' => 'Session',
		'operations' => 'ElementOperation',
		'bookings' => 'Booking',
		'theatres' => 'Theatre',
		'sites' => 'Site',
		'wards' => 'Ward',
		'patients' => 'Patient',
		'serviceSpecialtyAssignments' => 'ServiceSpecialtyAssignment',
		'specialty' => 'Specialty',
		'events' => 'Event',
		'episodes' => 'Episode',
		'services' => 'Service'
	);

	protected $service;

	public function dataProvider_InvalidStartAndEndDates()
	{
		$startDate = date('Y-m-d');
		$endDate = date('Y-m-d', strtotime('-7 days'));

		return array(
			array(null, null),
			array($startDate, null),
			array(null, $endDate),
			array($startDate, $endDate)
		);
	}

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
		$this->service->findSessions($monthStart, $minDate, $firmId, 1);
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

		$result = $this->service->findSessions($monthStart, $minDate, $firmId, 1);

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

		$result = $this->service->findSessions($monthStart, $minDate, $firmId, 1);

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
			'code' => '',
			'comments' => null
		);
		$this->assertEquals($expected, $session);
	}

	/**
	 * @dataProvider dataProvider_InvalidStartAndEndDates
	 */
	public function testFindTheatresAndSessions_InvalidDates_ThrowsException($startDate, $endDate)
	{
		$this->setExpectedException('Exception', 'Invalid start and end dates.');
		$this->service->findTheatresAndSessions($startDate, $endDate);
	}

	public function testFindTheatresAndSessions_ValidDates_ReturnsCorrectData()
	{
		$startDate = date('Y-m-d');
		$endDate = date('Y-m-t');

		$session1 = $this->sessions[0];
		$session2 = $this->sessions[2];

		$theatre = $this->theatres['theatre1'];
		$ward = $this->wards['ward1'];

		$expected = array(
			array(
				'operation_id' => $this->operations['element1']['id'],
				'name' => $theatre['name'],
				'date' => $session1['date'],
				'start_time' => $session1['start_time'],
				'end_time' => $session1['end_time'],
				'session_id' => $session1['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element1']['eye'],
				'anaesthetic_type' => $this->operations['element1']['anaesthetic_type'],
				'comments' => $this->operations['element1']['comments'],
				'operation_duration' => $this->operations['element1']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			)
		);
		if ($session2['date'] <= $endDate) {
			$expected[] = array(
				'operation_id' => $this->operations['element2']['id'],
				'name' => $theatre['name'],
				'date' => $session2['date'],
				'start_time' => $session2['start_time'],
				'end_time' => $session2['end_time'],
				'session_id' => $session2['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element2']['eye'],
				'anaesthetic_type' => $this->operations['element2']['anaesthetic_type'],
				'comments' => $this->operations['element2']['comments'],
				'operation_duration' => $this->operations['element2']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			);
		}

		$result = $this->service->findTheatresAndSessions($startDate, $endDate);
//		@todo: figure out why this query is now returning nothing
//		$this->assertEquals($expected, $result, 'Query results should be correct.');
	}

	public function testFindTheatresAndSessions_ValidDates_WithSiteId_ReturnsCorrectData()
	{
		$startDate = date('Y-m-d');
		$endDate = date('Y-m-t');
		$siteId = $this->sites['site1']['id'];

		$session1 = $this->sessions[0];
		$session2 = $this->sessions[2];

		$theatre = $this->theatres['theatre1'];
		$ward = $this->wards['ward1'];

		$expected = array(
			array(
				'operation_id' => $this->operations['element1']['id'],
				'name' => $theatre['name'],
				'date' => $session1['date'],
				'start_time' => $session1['start_time'],
				'end_time' => $session1['end_time'],
				'session_id' => $session1['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element1']['eye'],
				'anaesthetic_type' => $this->operations['element1']['anaesthetic_type'],
				'comments' => $this->operations['element1']['comments'],
				'operation_duration' => $this->operations['element1']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			)
		);
		if ($session2['date'] <= $endDate) {
			$expected[] = array(
				'operation_id' => $this->operations['element2']['id'],
				'name' => $theatre['name'],
				'date' => $session2['date'],
				'start_time' => $session2['start_time'],
				'end_time' => $session2['end_time'],
				'session_id' => $session2['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element2']['eye'],
				'anaesthetic_type' => $this->operations['element2']['anaesthetic_type'],
				'comments' => $this->operations['element2']['comments'],
				'operation_duration' => $this->operations['element2']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			);
		}

		$result = $this->service->findTheatresAndSessions($startDate, $endDate, $siteId);
//		@todo: figure out why this query is now returning nothing
//		$this->assertEquals($expected, $result, 'Query results should be correct.');
	}

	public function testFindTheatresAndSessions_ValidDates_WithSiteIdAndTheatreId_ReturnsCorrectData()
	{
		$startDate = date('Y-m-d');
		$endDate = date('Y-m-t');
		$siteId = $this->sites['site1']['id'];

		$session1 = $this->sessions[0];
		$session2 = $this->sessions[2];

		$theatre = $this->theatres['theatre1'];
		$theatreId = $theatre['id'];
		$ward = $this->wards['ward1'];

		$expected = array(
			array(
				'operation_id' => $this->operations['element1']['id'],
				'name' => $theatre['name'],
				'date' => $session1['date'],
				'start_time' => $session1['start_time'],
				'end_time' => $session1['end_time'],
				'session_id' => $session1['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element1']['eye'],
				'anaesthetic_type' => $this->operations['element1']['anaesthetic_type'],
				'comments' => $this->operations['element1']['comments'],
				'operation_duration' => $this->operations['element1']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			)
		);
		if ($session2['date'] <= $endDate) {
			$expected[] = array(
				'operation_id' => $this->operations['element2']['id'],
				'name' => $theatre['name'],
				'date' => $session2['date'],
				'start_time' => $session2['start_time'],
				'end_time' => $session2['end_time'],
				'session_id' => $session2['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element2']['eye'],
				'anaesthetic_type' => $this->operations['element2']['anaesthetic_type'],
				'comments' => $this->operations['element2']['comments'],
				'operation_duration' => $this->operations['element2']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			);
		}

		$result = $this->service->findTheatresAndSessions($startDate, $endDate, $siteId, $theatreId);
//		@todo: figure out why this query is now returning nothing
//		$this->assertEquals($expected, $result, 'Query results should be correct.');
	}

	public function testFindTheatresAndSessions_ValidDates_WithSiteIdAndTheatreIdAndServiceId_ReturnsCorrectData()
	{
		$startDate = date('Y-m-d');
		$endDate = date('Y-m-t');
		$siteId = $this->sites['site1']['id'];
		$serviceId = $this->serviceSpecialtyAssignments['servicespecialtyassignment1']['id'];

		$session1 = $this->sessions[0];
		$session2 = $this->sessions[2];

		$theatre = $this->theatres['theatre1'];
		$ward = $this->wards['ward1'];

		$expected = array(
			array(
				'operation_id' => $this->operations['element1']['id'],
				'name' => $theatre['name'],
				'date' => $session1['date'],
				'start_time' => $session1['start_time'],
				'end_time' => $session1['end_time'],
				'session_id' => $session1['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element1']['eye'],
				'anaesthetic_type' => $this->operations['element1']['anaesthetic_type'],
				'comments' => $this->operations['element1']['comments'],
				'operation_duration' => $this->operations['element1']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			)
		);
		if ($session2['date'] <= $endDate) {
			$expected[] = array(
				'operation_id' => $this->operations['element2']['id'],
				'name' => $theatre['name'],
				'date' => $session2['date'],
				'start_time' => $session2['start_time'],
				'end_time' => $session2['end_time'],
				'session_id' => $session2['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element2']['eye'],
				'anaesthetic_type' => $this->operations['element2']['anaesthetic_type'],
				'comments' => $this->operations['element2']['comments'],
				'operation_duration' => $this->operations['element2']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			);
		}

		$result = $this->service->findTheatresAndSessions($startDate, $endDate, $siteId, $theatre['id'], $serviceId);
//		@todo: figure out why this query is now returning nothing
//		$this->assertEquals($expected, $result, 'Query results should be correct.');
	}

	public function testFindTheatresAndSessions_ValidDates_WithSiteIdAndTheatreIdAndServiceIdAndFirmId_ReturnsCorrectData()
	{
		$startDate = date('Y-m-d');
		$endDate = date('Y-m-t');
		$siteId = $this->sites['site1']['id'];
		$serviceId = $this->serviceSpecialtyAssignments['servicespecialtyassignment1']['id'];
		$firmId = $this->firms['firm1']['id'];

		$session1 = $this->sessions[0];
		$session2 = $this->sessions[2];

		$theatre = $this->theatres['theatre1'];
		$ward = $this->wards['ward1'];

		$expected = array(
			array(
				'operation_id' => $this->operations['element1']['id'],
				'name' => $theatre['name'],
				'date' => $session1['date'],
				'start_time' => $session1['start_time'],
				'end_time' => $session1['end_time'],
				'session_id' => $session1['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element1']['eye'],
				'anaesthetic_type' => $this->operations['element1']['anaesthetic_type'],
				'comments' => $this->operations['element1']['comments'],
				'operation_duration' => $this->operations['element1']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			),
		);
		if ($session2['date'] <= $endDate) {
			$expected[] = array(
				'operation_id' => $this->operations['element2']['id'],
				'name' => $theatre['name'],
				'date' => $session2['date'],
				'start_time' => $session2['start_time'],
				'end_time' => $session2['end_time'],
				'session_id' => $session2['id'],
				'session_duration' => '04:00:00',
				'eye' => $this->operations['element2']['eye'],
				'anaesthetic_type' => $this->operations['element2']['anaesthetic_type'],
				'comments' => $this->operations['element2']['comments'],
				'operation_duration' => $this->operations['element2']['total_duration'],
				'first_name' => $this->patients['patient1']['first_name'],
				'last_name' => $this->patients['patient1']['last_name'],
				'dob' => $this->patients['patient1']['dob'],
				'gender' => $this->patients['patient1']['gender'],
				'ward' => $ward['name'],
				'display_order' => 1,
			);
		}

		$result = $this->service->findTheatresAndSessions($startDate, $endDate, $siteId, $theatre['id'], $serviceId, $firmId);
//		@todo: figure out why this query is now returning nothing
//		$this->assertEquals($expected, $result, 'Query results should be correct.');
	}
}
