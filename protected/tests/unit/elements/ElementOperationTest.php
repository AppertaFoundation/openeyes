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

class ElementOperationTest extends CDbTestCase
{
	public $user;
	public $firm;
	public $patient;
	public $element;

	public $fixtures = array(
		'users' => 'User',
		'firms' => 'Firm',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'eventTypes' => 'EventType',
		'events' => 'Event',
		'procedures' => 'Procedure',
		'services' => 'Service',
		'subsections' => 'SpecialtySubsection',
		'elements' => 'ElementOperation',
		'operationProcedures' => 'OperationProcedureAssignment',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'sessions' => 'Session',
		'operations' => 'ElementOperation',
		'bookings' => 'Booking',
		'theatres' => 'Theatre',
		'sites' => 'Site',
		'wards' => 'Ward',
		'reasons' => 'CancellationReason'
	);

	public function setUp()
	{
		parent::setUp();
		$this->user = $this->users('user1');
		$this->firm = $this->firms('firm1');
		$this->patient = $this->patients('patient1');
		$this->element = new ElementOperation();
		$this->element->setBaseOptions($this->firm->id, $this->patient->id, $this->user->id);
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$element = $this->element;
		$element->setAttributes($searchTerms);
		$results = $element->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->elements($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}

	public function testBasicCreate_NoTimeframe_SavesElement()
	{
		$element = $this->element;
		$element->isNewRecord = true;
		$element->setAttributes(array(
			'event_id' => '1',
			'eye' => ElementOperation::EYE_LEFT,
			'decision_date' => date('Y-m-d'),
		));

		$_POST['ElementDiagnosis']['eye'] = ElementDiagnosis::EYE_LEFT;

		$_POST['Procedures'] = array($this->procedures['procedure1']['id']);

		$this->assertTrue($element->save(true));
	}

	public function testBasicCreate_WithTimeframe_SavesElement()
	{
		$element = $this->element;
		$element->isNewRecord = true;
		$element->setAttributes(array(
			'event_id' => '1',
			'eye' => ElementOperation::EYE_LEFT,
			'decision_date' => date('Y-m-d'),
		));

		$_POST['ElementDiagnosis']['eye'] = ElementDiagnosis::EYE_LEFT;

		$_POST['schedule_timeframe2'] = ElementOperation::SCHEDULE_AFTER_2MO;

		$_POST['Procedures'] = array($this->procedures['procedure1']['id']);

		$this->assertTrue($element->save(true));
	}

	public function testBasicCreate_WithMismatchedDiagnosis_DoesNotSaveElement()
	{
		$element = $this->element;
		$element->isNewRecord = true;
		$element->setAttributes(array(
			'event_id' => '1',
			'eye' => ElementOperation::EYE_LEFT,
			'decision_date' => date('Y-m-d'),
		));

		$_POST['schedule_timeframe2'] = ElementOperation::SCHEDULE_AFTER_2MO;
		$_POST['ElementDiagnosis']['eye'] = ElementDiagnosis::EYE_RIGHT;
		$_POST['ElementOperation']['eye'] = ElementDiagnosis::EYE_LEFT;

		$_POST['Procedures'] = array($this->procedures['procedure1']['id']);

		$this->assertFalse($element->save(true));
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'event_id' => 'Event',
			'eye' => 'Eye(s)',
			'comments' => 'Comments',
			'total_duration' => 'Total Duration',
			'consultant_required' => 'Consultant Required',
			'decision_date' => 'Decision Date',
			'anaesthetist_required' => 'Anaesthetist Required',
			'anaesthetic_type' => 'Anaesthetic Type',
			'overnight_stay' => 'Overnight Stay',
			'schedule_timeframe' => 'Schedule Timeframe',
		);

		$this->assertEquals($expected, $this->element->attributeLabels());
	}

	public function testModel()
	{
		$this->assertEquals('ElementOperation', get_class(ElementOperation::model()));
	}

	public function testUpdate()
	{
		$element = $this->elements('element1');

		$element->eye = ElementOperation::EYE_RIGHT;
		$_POST['Procedures'] = array($this->procedures['procedure1']['id']);

		$this->assertTrue($element->save(true));
	}

	public function testGetEyeOptions()
	{
		$expected = array(
			ElementOperation::EYE_RIGHT => 'Right',
			ElementOperation::EYE_LEFT => 'Left',
			ElementOperation::EYE_BOTH => 'Both',
		);
		$this->assertEquals($expected, $this->element->getEyeOptions());
	}

	/**
	 * @dataProvider dataProvider_EyeText
	 */
	public function testGetEyeText($newEye, $expectedText)
	{
		$element = $this->elements('element1');
		$element->eye = $newEye;
		$element->save();

		$this->assertEquals($expectedText, $element->getEyeText());
	}

        public function testGetStatusText()
        {
                $element = $this->elements('element1');

                $this->assertEquals($element->getStatusText(), 'Scheduled');
        }

	public function testSetDefaultOptions_SetsCorrectOptions()
	{
		$this->element->consultant_required = ElementOperation::CONSULTANT_REQUIRED;
		$this->element->anaesthetic_type = ElementOperation::ANAESTHETIC_GENERAL;
		$this->element->overnight_stay = true;
		$this->element->total_duration = 10;

		$this->element->setDefaultOptions();
		$this->assertEquals(ElementOperation::CONSULTANT_NOT_REQUIRED, $this->element->consultant_required);
		$this->assertEquals(ElementOperation::ANAESTHETIC_TOPICAL, $this->element->anaesthetic_type);
		$this->assertEquals(0, $this->element->overnight_stay);
		$this->assertEquals(0, $this->element->total_duration);
	}

	public function testGetConsultantOptions_ReturnsCorrectData()
	{
		$expected = array(
			ElementOperation::CONSULTANT_REQUIRED => 'Yes',
			ElementOperation::CONSULTANT_NOT_REQUIRED => 'No',
		);

		$this->assertEquals($expected, $this->element->getConsultantOptions());
	}

	/**
	 * @dataProvider dataProvider_BooleanFields
	 */
	public function testGetBooleanText_ValidInput_ReturnsCorrectData($field, $value)
	{
		$this->element->$field = $value;

		$expected = ($value == 1) ? 'Yes' : 'No';

		$this->assertEquals($expected, $this->element->getBooleanText($field));
	}

	public function testGetAnaestheticOptions_ReturnsValidData()
	{
		$expected = array(
			ElementOperation::ANAESTHETIC_TOPICAL => 'Topical',
			ElementOperation::ANAESTHETIC_LOCAL => 'LA',
			ElementOperation::ANAESTHETIC_LOCAL_WITH_COVER => 'LAC',
			ElementOperation::ANAESTHETIC_LOCAL_WITH_SEDATION => 'LAS',
			ElementOperation::ANAESTHETIC_GENERAL => 'GA'
		);

		$this->assertEquals($expected, $this->element->getAnaestheticOptions());
	}

	/**
	 * @dataProvider dataProvider_AnaesteticText
	 */
	public function testGetAnaestheticText_ReturnsCorrectData($type, $text)
	{
		$this->element->anaesthetic_type = $type;

		$this->assertEquals($text, $this->element->getAnaestheticText());
	}

	/**
	 * @dataProvider dataProvider_AnaesteticAbbreviation
	 */
	public function testGetAnaestheticAbbreviation_ReturnsCorrectData($type, $text)
	{
		$this->element->anaesthetic_type = $type;

		$this->assertEquals($text, $this->element->getAnaestheticAbbreviation());
	}

	public function testGetOvernightOptions_ReturnsCorrectData()
	{
		$expected = array(
			1 => 'Yes',
			0 => 'No',
		);

		$this->assertEquals($expected, $this->element->getOvernightOptions());
	}

	public function testGetScheduleOptions_ReturnsCorrectData()
	{
		$expected = array(
			0 => 'As soon as possible',
			1 => 'Within timeframe specified by patient',
		);

		$this->assertEquals($expected, $this->element->getScheduleOptions());
	}

	public function testGetScheduleDelayOptions_ReturnsCorrectData()
	{
		$expected = array(
			ElementOperation::SCHEDULE_AFTER_1MO => 'After 1 Month',
			ElementOperation::SCHEDULE_AFTER_2MO => 'After 2 Months',
			ElementOperation::SCHEDULE_AFTER_3MO => 'After 3 Months',
		);

		$this->assertEquals($expected, $this->element->getScheduleDelayOptions());
	}

	/**
	 * @dataProvider dataProvider_ScheduleText
	 */
	public function testGetScheduleText_ReturnsCorrectData($timeframe, $text)
	{
		$this->element->schedule_timeframe = $timeframe;

		$this->assertEquals($text, $this->element->getScheduleText());
	}

	/**
	 * @dataProvider dataProvider_StatusText
	 */
	public function testGetStatusText_ReturnsCorrectData($status, $text)
	{
		$this->element->status = $status;

		$this->assertEquals($text, $this->element->getStatusText());
	}

	/**
	 * @dataProvider dataProvider_ScheduleText
	 */
	public function testGetMinDate_DifferentScheduleTimeframes_SelectsCorrectDate($timeframe, $text)
	{
		$element = $this->elements('element1');
		$element->schedule_timeframe = $timeframe;

		$date = strtotime($element->event->datetime);
		if ($timeframe != ElementOperation::SCHEDULE_IMMEDIATELY) {
			$interval = str_replace('After ', '+', $text);
			$date = strtotime($interval, $date);
		}

		$this->assertEquals($date, $element->getMinDate());
	}

//	@todo: rewrite these to be reliable
//	public function testGetSessions_NoDateSet_ReturnsCorrectData()
//	{
//		$firm = $this->firms('firm1');
//		$element = $this->elements('element1');
//		$patientId = $element->event->episode->patient_id;
//		$userId = $this->users['user1']['id'];
//		$viewNumber = 1;
//
//		$mockElement = $this->getMock('ElementOperation', array('getBookingService'),
//			array($firm, $patientId, $userId, $viewNumber));
//		$mockElement->setAttributes($this->elements['element1']);
//
//		$timestamp = strtotime($element->event->datetime);
//		$monthStart = date('Y-m-01', $timestamp);
//		$monthEnd = date('Y-m-t', $timestamp);
//		$minDate = date('Y-m-d', $timestamp);
//
//		$sessions = $dates = array();
//		foreach ($this->sessions as $name => $session) {
//			if ($session['date'] >= $monthStart) {
//				if ($session['date'] > $monthEnd) {
//					break;
//				}
//				$endTime = strtotime($session['end_time']);
//				$startTime = strtotime($session['start_time']);
//				$session['session_duration'] = '04:30:00';
//				$session['bookings_duration'] = 0;
//				$session['time_available'] = 270;
//				$sessions[] = $session;
//				$dates[] = $session['date'];
//			}
//		}
//
//		$expected = array();
//		$weekdayIndex = date('N', strtotime($sessions[0]['date']));
//		$weekday = $this->getWeekday($weekdayIndex);
//		$expected[$weekday] = array();
//
//		$timestamp = strtotime($monthStart);
//		$firstWeekday = strtotime(date('Y-m-01', $timestamp));
//		$lastMonthday = strtotime(date('Y-m-t', $timestamp));
//		while ($weekdayIndex != date('N', $firstWeekday)) {
//			$firstWeekday += 60 * 60 * 24;
//		}
//
//		for ($weekCounter = 1; $weekCounter < 6; $weekCounter++) {
//			$addDays = ($weekCounter - 1) * 7;
//			$selectedDay = date('Y-m-d', mktime(0,0,0, date('m', $firstWeekday), date('d', $firstWeekday)+$addDays, date('Y', $firstWeekday)));
//			if (in_array($selectedDay, $dates)) {
//				$totalSessions = 0;
//				$open = $full = 0;
//				foreach ($sessions as $session) {
//					if ($session['date'] == $selectedDay) {
//						$totalSessions++;
//
//						if ($session['time_available'] >= $mockElement->total_duration) {
//							$open++;
//						} else {
//							$full++;
//						}
//						unset($session['date'], $session['session_duration']);
//						$session['duration'] = 270;
//						$expected[$weekday][$selectedDay]['sessions'][] = $session;
//					}
//				}
//				if ($full == $totalSessions) {
//					$status = 'full';
//				} elseif ($full > 0 && $open > 0) {
//					$status = 'limited';
//				} elseif ($open == $totalSessions) {
//					$status = 'available';
//				}
//				$expected[$weekday][$selectedDay]['status'] = $status;
//			} else {
//				$status = 'closed';
//			}
//			$expected[$weekday][$selectedDay]['status'] = $status;
//		}
//
//		$service = $this->getMock('BookingService', array('findSessions'));
//		$service->expects($this->once())
//			->method('findSessions')
//			->with($monthStart, strtotime($minDate), $element->event->episode->firm_id)
//			->will($this->returnValue($sessions));
//
//		$mockElement->expects($this->once())
//			->method('getBookingService')
//			->will($this->returnValue($service));
//
//		$result = $mockElement->getSessions();
//
//		$this->assertEquals($expected, $result);
//	}
//
//	@todo: rewrite these to be reliable
//	public function testGetSessions_MinDateBeforeToday_ReturnsCorrectData()
//	{
//		$firm = $this->firms('firm1');
//		$element = $this->elements('element1');
//		$patientId = $element->event->episode->patient_id;
//		$userId = $this->users['user1']['id'];
//		$viewNumber = 1;
//
//		$mockElement = $this->getMock('ElementOperation', array('getBookingService'),
//			array($firm, $patientId, $userId, $viewNumber));
//		$mockElement->setAttributes($this->elements['element1']);
//
//		$previousDate = strtotime('-5 weeks');
//		$thisMonth = date('Y-m-01');
//
//		$event = $this->events('event1');
//		$event->datetime = date('Y-m-d', $previousDate);
//		$event->save();
//
//		$timestamp = strtotime($element->event->datetime);
//		$monthStart = date('Y-m-01', $timestamp);
//		$monthEnd = date('Y-m-t', $timestamp);
//		$minDate = date('Y-m-d', $timestamp);
//
//		$sessions = $dates = array();
//		foreach ($this->sessions as $name => $session) {
//			if ($session['date'] >= $monthStart) {
//				if ($session['date'] > $monthEnd) {
//					break;
//				}
//				$endTime = strtotime($session['end_time']);
//				$startTime = strtotime($session['start_time']);
//				$session['session_duration'] = '04:30:00';
//				$session['bookings_duration'] = 0;
//				$session['time_available'] = 270;
//				$sessions[] = $session;
//				$dates[] = $session['date'];
//			}
//		}
//
//		$expected = array();
//		$weekdayIndex = date('N', strtotime($sessions[0]['date']));
//		$weekday = $this->getWeekday($weekdayIndex);
//		$expected[$weekday] = array();
//
//		$timestamp = strtotime($monthStart);
//		$firstWeekday = strtotime(date('Y-m-01', $timestamp));
//		$lastMonthday = strtotime(date('Y-m-t', $timestamp));
//		while ($weekdayIndex != date('N', $firstWeekday)) {
//			$firstWeekday += 60 * 60 * 24;
//		}
//
//		for ($weekCounter = 1; $weekCounter < 6; $weekCounter++) {
//			$addDays = ($weekCounter - 1) * 7;
//			$selectedDay = date('Y-m-d', mktime(0,0,0, date('m', $firstWeekday), date('d', $firstWeekday)+$addDays, date('Y', $firstWeekday)));
//			if (in_array($selectedDay, $dates)) {
//				$totalSessions = 0;
//				$open = $full = 0;
//				foreach ($sessions as $session) {
//					if ($session['date'] == $selectedDay) {
//						$totalSessions++;
//
//						if ($session['time_available'] >= $mockElement->total_duration) {
//							$open++;
//						} else {
//							$full++;
//						}
//						unset($session['date'], $session['session_duration']);
//						$session['duration'] = 270;
//						$expected[$weekday][$selectedDay]['sessions'][] = $session;
//					}
//				}
//				if ($full == $totalSessions) {
//					$status = 'full';
//				} elseif ($full > 0 && $open > 0) {
//					$status = 'limited';
//				} elseif ($open == $totalSessions) {
//					$status = 'available';
//				}
//				$expected[$weekday][$selectedDay]['status'] = $status;
//			} else {
//				$status = 'closed';
//			}
//			$expected[$weekday][$selectedDay]['status'] = $status;
//		}
//
//		$service = $this->getMock('BookingService', array('findSessions'));
//		$service->expects($this->once())
//			->method('findSessions')
//			->with($monthStart, strtotime($thisMonth), $element->event->episode->firm_id)
//			->will($this->returnValue($sessions));
//
//		$mockElement->expects($this->once())
//			->method('getBookingService')
//			->will($this->returnValue($service));
//
//		$result = $mockElement->getSessions();
//
//		$this->assertEquals($expected, $result);
//	}
//
//	@todo: rewrite these to be reliable
//	public function testGetSessions_DateSet_ReturnsCorrectData()
//	{
//		$nextMonth = date('Y-m-01', strtotime('+1 month'));
//		$_GET['date'] = $nextMonth;
//
//		$firm = $this->firms('firm1');
//		$element = $this->elements('element1');
//		$patientId = $element->event->episode->patient_id;
//		$userId = $this->users['user1']['id'];
//		$viewNumber = 1;
//
//		$mockElement = $this->getMock('ElementOperation', array('getBookingService'),
//			array($firm, $patientId, $userId, $viewNumber));
//		$mockElement->setAttributes($this->elements['element1']);
//
//		$timestamp = strtotime($nextMonth);
//		$monthStart = date('Y-m-01', $timestamp);
//		$monthEnd = date('Y-m-t', $timestamp);
//		$minDate = date('Y-m-d', strtotime($element->event->datetime));
//
//		$sessions = array();
//		$dates = array();
//		$fullSession = false;
//		foreach ($this->sessions as $name => $session) {
//			if ($session['date'] >= $monthStart) {
//				if ($session['date'] > $monthEnd) {
//					break;
//				}
//				$endTime = strtotime($session['end_time']);
//				$startTime = strtotime($session['start_time']);
//				$session['session_duration'] = '04:30:00';
//				if (!$fullSession) {
//					$session['bookings_duration'] = 270;
//					$session['time_available'] = 0;
//					$fullSession = $session['date'];
//				} else {
//					$session['bookings_duration'] = 0;
//					$session['time_available'] = 270;
//				}
//				$sessions[] = $session;
//				$dates[] = $session['date'];
//			}
//		}
//
//		$expected = array();
//		$weekdayIndex = date('N', strtotime($fullSession));
//		$weekday = $this->getWeekday($weekdayIndex);
//		$expected[$weekday] = array();
//
//		$timestamp = strtotime($monthStart);
//		$firstWeekday = strtotime(date('Y-m-01', $timestamp));
//		$lastMonthday = strtotime(date('Y-m-t', $timestamp));
//		while ($weekdayIndex != date('N', $firstWeekday)) {
//			$firstWeekday += 60 * 60 * 24;
//		}
//
//		for ($weekCounter = 1; $weekCounter < 6; $weekCounter++) {
//			$addDays = ($weekCounter - 1) * 7;
//			$selectedDay = date('Y-m-d', mktime(0,0,0, date('m', $firstWeekday), date('d', $firstWeekday)+$addDays, date('Y', $firstWeekday)));
//			if (in_array($selectedDay, $dates)) {
//				$totalSessions = 0;
//				$open = $full = 0;
//				foreach ($sessions as $session) {
//					if ($session['date'] == $selectedDay) {
//						$totalSessions++;
//
//						if ($session['time_available'] >= $mockElement->total_duration) {
//							$open++;
//						} else {
//							$full++;
//						}
//						unset($session['date'], $session['session_duration']);
//						$session['duration'] = 270;
//						$expected[$weekday][$selectedDay]['sessions'][] = $session;
//					}
//				}
//				if ($full == $totalSessions) {
//					$status = 'full';
//				} elseif ($full > 0 && $open > 0) {
//					$status = 'limited';
//				} elseif ($open == $totalSessions) {
//					$status = 'available';
//				}
//				$expected[$weekday][$selectedDay]['status'] = $status;
//			} else {
//				$status = 'closed';
//			}
//			$expected[$weekday][$selectedDay]['status'] = $status;
//		}
//
//		$service = $this->getMock('BookingService', array('findSessions'));
//		$service->expects($this->once())
//			->method('findSessions')
//			->with($monthStart, strtotime($minDate), $element->event->episode->firm_id)
//			->will($this->returnValue($sessions));
//
//		$mockElement->expects($this->once())
//			->method('getBookingService')
//			->will($this->returnValue($service));
//
//		$result = $mockElement->getSessions();
//
//		$this->assertEquals($expected, $result);
//	}
//
//	@todo: rewrite these to be reliable
//	public function testGetSessions_LimitedSessionAvailable_ReturnsCorrectData()
//	{
//		$firm = $this->firms('firm1');
//		$element = $this->elements('element1');
//		$patientId = $element->event->episode->patient_id;
//		$userId = $this->users['user1']['id'];
//		$viewNumber = 1;
//
//		$mockElement = $this->getMock('ElementOperation', array('getBookingService'),
//			array($firm, $patientId, $userId, $viewNumber));
//		$mockElement->setAttributes($this->elements['element1']);
//
//		$timestamp = strtotime($element->event->datetime);
//		$monthStart = date('Y-m-01', $timestamp);
//		$monthEnd = date('Y-m-t', $timestamp);
//		$minDate = date('Y-m-d', $timestamp);
//
//		$sessions = array();
//		$fullSession = false;
//		$dates = array();
//		foreach ($this->sessions as $name => $session) {
//			if ($session['date'] >= $monthStart) {
//				if ($session['date'] > $monthEnd) {
//					break;
//				}
//				$endTime = strtotime($session['end_time']);
//				$startTime = strtotime($session['start_time']);
//				$session['session_duration'] = '04:30:00';
//				if (!$fullSession) {
//					$session['bookings_duration'] = 270;
//					$session['time_available'] = 0;
//
//					$extraSession = $session;
//					$extraSession['bookings_duration'] = 0;
//					$extraSession['time_available'] = 270;
//					$sessions[] = $extraSession;
//					$fullSession = $session['date'];
//					$dates[] = $session['date'];
//				} else {
//					$session['bookings_duration'] = 0;
//					$session['time_available'] = 270;
//				}
//				$sessions[] = $session;
//				$dates[] = $session['date'];
//			}
//		}
//
//		$expected = array();
//		$weekdayIndex = date('N', strtotime($fullSession));
//		$weekday = $this->getWeekday($weekdayIndex);
//		$expected[$weekday] = array();
//
//		$timestamp = strtotime($monthStart);
//		$firstWeekday = strtotime(date('Y-m-01', $timestamp));
//		$lastMonthday = strtotime(date('Y-m-t', $timestamp));
//		while ($weekdayIndex != date('N', $firstWeekday)) {
//			$firstWeekday += 60 * 60 * 24;
//		}
//
//		for ($weekCounter = 1; $weekCounter < 6; $weekCounter++) {
//			$addDays = ($weekCounter - 1) * 7;
//			$selectedDay = date('Y-m-d', mktime(0,0,0, date('m', $firstWeekday), date('d', $firstWeekday)+$addDays, date('Y', $firstWeekday)));
//			if (in_array($selectedDay, $dates)) {
//				$totalSessions = 0;
//				$open = $full = 0;
//				foreach ($sessions as $session) {
//					if ($session['date'] == $selectedDay) {
//						$totalSessions++;
//
//						if ($session['time_available'] >= $mockElement->total_duration) {
//							$open++;
//						} else {
//							$full++;
//						}
//						unset($session['date'], $session['session_duration']);
//						$session['duration'] = 270;
//						$expected[$weekday][$selectedDay]['sessions'][] = $session;
//					}
//				}
//				if ($full == $totalSessions) {
//					$status = 'full';
//				} elseif ($full > 0 && $open > 0) {
//					$status = 'limited';
//				} elseif ($open == $totalSessions) {
//					$status = 'available';
//				}
//				$expected[$weekday][$selectedDay]['status'] = $status;
//			} else {
//				$status = 'closed';
//			}
//			$expected[$weekday][$selectedDay]['status'] = $status;
//		}
//
//		$service = $this->getMock('BookingService', array('findSessions'));
//		$service->expects($this->once())
//			->method('findSessions')
//			->with($monthStart, strtotime($minDate), $element->event->episode->firm_id)
//			->will($this->returnValue($sessions));
//
//		$mockElement->expects($this->once())
//			->method('getBookingService')
//			->will($this->returnValue($service));
//
//		$result = $mockElement->getSessions();
//
//		$this->assertEquals($expected, $result);
//	}
//
//	@todo: rewrite these to be reliable
//	public function testGetSessions_OpenSessionAvailable_ReturnsCorrectData()
//	{
//		$firm = $this->firms('firm1');
//		$element = $this->elements('element1');
//		$patientId = $element->event->episode->patient_id;
//		$userId = $this->users['user1']['id'];
//		$viewNumber = 1;
//
//		$mockElement = $this->getMock('ElementOperation', array('getBookingService'),
//			array($firm, $patientId, $userId, $viewNumber));
//		$mockElement->setAttributes($this->elements['element1']);
//
//		$timestamp = strtotime($element->event->datetime);
//		$monthStart = date('Y-m-01', $timestamp);
//		$monthEnd = date('Y-m-t', $timestamp);
//		$minDate = date('Y-m-d', $timestamp);
//
//		$sessions = array();
//		$openSession = false;
//		$dates = array();
//		foreach ($this->sessions as $name => $session) {
//			if ($session['date'] >= $monthStart) {
//				if ($session['date'] > $monthEnd) {
//					break;
//				}
//				$endTime = strtotime($session['end_time']);
//				$startTime = strtotime($session['start_time']);
//				$session['session_duration'] = '04:30:00';
//				if (!$openSession) {
//					$session['bookings_duration'] = 0;
//					$session['time_available'] = 270;
//					$openSession = $session['date'];
//				} else {
//					$session['bookings_duration'] = 270;
//					$session['time_available'] = 0;
//				}
//				$sessions[] = $session;
//				$dates[] = $session['date'];
//			}
//		}
//
//		$expected = array();
//		$weekdayIndex = date('N', strtotime($openSession));
//		$weekday = $this->getWeekday($weekdayIndex);
//		$expected[$weekday] = array();
//
//		$timestamp = strtotime($monthStart);
//		$firstWeekday = strtotime(date('Y-m-01', $timestamp));
//		$lastMonthday = strtotime(date('Y-m-t', $timestamp));
//		while ($weekdayIndex != date('N', $firstWeekday)) {
//			$firstWeekday += 60 * 60 * 24;
//		}
//
//		for ($weekCounter = 1; $weekCounter < 6; $weekCounter++) {
//			$addDays = ($weekCounter - 1) * 7;
//			$selectedDay = date('Y-m-d', mktime(0,0,0, date('m', $firstWeekday), date('d', $firstWeekday)+$addDays, date('Y', $firstWeekday)));
//			if (in_array($selectedDay, $dates)) {
//				$totalSessions = 0;
//				$open = $full = 0;
//				foreach ($sessions as $session) {
//					if ($session['date'] == $selectedDay) {
//						$totalSessions++;
//
//						if ($session['time_available'] >= $mockElement->total_duration) {
//							$open++;
//						} else {
//							$full++;
//						}
//						unset($session['date'], $session['session_duration']);
//						$session['duration'] = 270;
//						$expected[$weekday][$selectedDay]['sessions'][] = $session;
//					}
//				}
//				if ($full == $totalSessions) {
//					$status = 'full';
//				} elseif ($full > 0 && $open > 0) {
//					$status = 'limited';
//				} elseif ($open == $totalSessions) {
//					$status = 'available';
//				}
//				$expected[$weekday][$selectedDay]['status'] = $status;
//			} else {
//				$status = 'closed';
//			}
//			$expected[$weekday][$selectedDay]['status'] = $status;
//		}
//
//		$service = $this->getMock('BookingService', array('findSessions'));
//		$service->expects($this->once())
//			->method('findSessions')
//			->with($monthStart, strtotime($minDate), $element->event->episode->firm_id)
//			->will($this->returnValue($sessions));
//
//		$mockElement->expects($this->once())
//			->method('getBookingService')
//			->will($this->returnValue($service));
//
//		$result = $mockElement->getSessions();
//
//		$this->assertEquals($expected, $result);
//	}

	public function testGetTheatres_EmptyDate_ThrowsException()
	{
		$this->setExpectedException('Exception', 'Date is required.');
		$this->element->getTheatres(false);
	}

	public function testGetTheatres_Available_ReturnsCorrectData()
	{
		$date = date('Y-m-d', strtotime('+1 day'));

		$firm = $this->firms('firm1');
		$element = $this->elements('element1');
		$patientId = $element->event->episode->patient_id;
		$userId = $this->users['user1']['id'];
		$viewNumber = 1;

		$mockElement = $this->getMock('ElementOperation', array('getBookingService'),
			array($firm, $patientId, $userId, $viewNumber));
		$mockElement->setAttributes($this->elements['element1']);

		$sessionList = Session::model()->findAllByAttributes(array('date' => $date));

		$theatre = $this->theatres['theatre1'];
		$sessions = array();
		foreach ($sessionList as $session) {
			$bookings = Booking::model()->findAllByAttributes(
				array('session_id' => $session['id']));
			$bookingCount = $bookingTime = 0;
			foreach ($bookings as $appt) {
				$bookingCount++;
				$operation = ElementOperation::model()->findByPk($appt['element_operation_id']);
				$bookingTime += $operation->total_duration;
			}
			$site = Site::model()->findByPk($theatre['site_id']);
			$sessions[] = array(
				'id' => $theatre['id'],
				'name' => $theatre['name'] . ' (' . $site->name . ')',
				'site_id' => $theatre['site_id'],
				'start_time' => $session['start_time'],
				'end_time' => $session['end_time'],
				'session_id' => $session['id'],
				'session_duration' => '04:30:00',
				'bookings' => $bookingCount,
				'bookings_duration' => $bookingTime,
			);
		}

		foreach ($sessions as $session) {
			$site = Site::model()->findByPk($theatre['site_id']);
			$name = $session['name'] . ' (' . $site['name'] . ')';
			$sessionTime = explode(':', $session['session_duration']);
			$session['duration'] = ($sessionTime[0] * 60) + $sessionTime[1];
			$session['time_available'] = $session['duration'] - $session['bookings_duration'];
			$session['id'] = $session['session_id'];
			unset($session['session_duration'], $session['date'], $session['name']);

			if ($session['time_available'] <= 0) {
				$status = 'full';
			} else {
				$status = 'available';
			}
			$session['status'] = $status;

			$expected[$name][] = $session;
			$names[] = $name;
		}

		$service = $this->getMock('BookingService', array('findTheatres'));
		$service->expects($this->once())
			->method('findTheatres')
			->with($date, $element->event->episode->firm_id)
			->will($this->returnValue($sessions));

		$mockElement->expects($this->once())
			->method('getBookingService')
			->will($this->returnValue($service));

		$result = $mockElement->getTheatres($date, $firm['id']);

		$this->assertEquals($expected, $result);
	}

	public function testGetTheatres_MultipleTheatres_ReturnsCorrectData()
	{
		$date = date('Y-m-d', strtotime('+1 day'));

		$firm = $this->firms('firm1');
		$element = $this->elements('element1');
		$patientId = $element->event->episode->patient_id;
		$userId = $this->users['user1']['id'];
		$viewNumber = 1;

		$mockElement = $this->getMock('ElementOperation', array('getBookingService'),
			array($firm, $patientId, $userId, $viewNumber));
		$mockElement->setAttributes($this->elements['element1']);

		$sessionList = Session::model()->findAllByAttributes(array('date' => $date));

		$theatre = $this->theatres['theatre1'];
		$theatre2 = new Theatre;
		$theatre2->name = $theatre['name'] . ' v2';
		$theatre2->site_id = $theatre['site_id'];
		$theatre2->save();

		$sessions = array();
		foreach ($sessionList as $session) {
			$bookings = Booking::model()->findAllByAttributes(
				array('session_id' => $session['id']));
			$bookingCount = $bookingTime = 0;
			foreach ($bookings as $appt) {
				$bookingCount++;
				$operation = ElementOperation::model()->findByPk($appt['element_operation_id']);
				$bookingTime += $operation->total_duration;
			}
			$sessions[] = array(
				'id' => $theatre['id'],
				'name' => $theatre['name'],
				'site_id' => $theatre['site_id'],
				'start_time' => $session['start_time'],
				'end_time' => $session['end_time'],
				'session_id' => $session['id'],
				'session_duration' => '04:30:00',
				'bookings' => $bookingCount,
				'bookings_duration' => $bookingTime,
			);
			$sessions[] = array(
				'id' => $theatre2['id'],
				'name' => $theatre2['name'],
				'site_id' => $theatre2['site_id'],
				'start_time' => $session['start_time'],
				'end_time' => $session['end_time'],
				'session_id' => $session['id'],
				'session_duration' => '04:30:00',
				'bookings' => $bookingCount,
				'bookings_duration' => $bookingTime,
			);
		}

		foreach ($sessions as $session) {
			$site = Site::model()->findByPk($theatre['site_id']);
                        $name = $session['name'] . ' (' . $site['name'] . ')';
			$sessionTime = explode(':', $session['session_duration']);
			$session['duration'] = ($sessionTime[0] * 60) + $sessionTime[1];
			$session['time_available'] = $session['duration'] - $session['bookings_duration'];
			$session['id'] = $session['session_id'];
			unset($session['session_duration'], $session['date'], $session['name']);

			if ($session['time_available'] <= 0) {
				$status = 'full';
			} else {
				$status = 'available';
			}
			$session['status'] = $status;

			$expected[$name][] = $session;
			$names[] = $name;
		}

		$service = $this->getMock('BookingService', array('findTheatres'));
		$service->expects($this->once())
			->method('findTheatres')
			->with($date, $element->event->episode->firm_id)
			->will($this->returnValue($sessions));

		$mockElement->expects($this->once())
			->method('getBookingService')
			->will($this->returnValue($service));

		$result = $mockElement->getTheatres($date, $firm['id']);

		$this->assertEquals($expected, $result);
	}

	public function testGetTheatres_Full_ReturnsCorrectData()
	{
		$date = date('Y-m-d', strtotime('+1 day'));

		$firm = $this->firms('firm1');
		$element = $this->elements('element1');
		$patientId = $element->event->episode->patient_id;
		$userId = $this->users['user1']['id'];
		$viewNumber = 1;

		$mockElement = $this->getMock('ElementOperation', array('getBookingService'),
			array($firm, $patientId, $userId, $viewNumber));
		$mockElement->setAttributes($this->elements['element1']);

		$sessionList = Session::model()->findAllByAttributes(array('date' => $date));

		$theatre = $this->theatres['theatre1'];
		$sessions = array();
		foreach ($sessionList as $session) {
			$sessions[] = array(
				'id' => $theatre['id'],
				'name' => $theatre['name'],
				'site_id' => $theatre['site_id'],
				'start_time' => $session['start_time'],
				'end_time' => $session['end_time'],
				'session_id' => $session['id'],
				'session_duration' => '04:30:00',
				'bookings' => 1,
				'bookings_duration' => 270,
			);
		}

		foreach ($sessions as $session) {
			$site = Site::model()->findByPk($theatre['site_id']);
                        $name = $session['name'] . ' (' . $site['name'] . ')';
			$sessionTime = explode(':', $session['session_duration']);
			$session['duration'] = ($sessionTime[0] * 60) + $sessionTime[1];
			$session['time_available'] = $session['duration'] - $session['bookings_duration'];
			$session['id'] = $session['session_id'];
			unset($session['session_duration'], $session['date'], $session['name']);

			if ($session['time_available'] <= 0) {
				$status = 'full';
			} else {
				$status = 'available';
			}
			$session['status'] = $status;

			$expected[$name][] = $session;
			$names[] = $name;
		}

		$service = $this->getMock('BookingService', array('findTheatres'));
		$service->expects($this->once())
			->method('findTheatres')
			->with($date, $element->event->episode->firm_id)
			->will($this->returnValue($sessions));

		$mockElement->expects($this->once())
			->method('getBookingService')
			->will($this->returnValue($service));

		$result = $mockElement->getTheatres($date, $firm['id']);

		$this->assertEquals($expected, $result);
	}

	public function testGetSession_MissingSessionId_ThrowsException()
	{
		$this->setExpectedException('Exception', 'Session id is invalid.');
		$this->element->getSession(false);
	}

	public function testGetSession_InvalidSessionId_ReturnsCorrectData()
	{
		$this->assertFalse($this->element->getSession(5725895672));
	}

	public function testGetSession_Available_ReturnsCorrectData()
	{
		$session = $this->sessions[0];
		$theatre = $this->theatres['theatre1'];

		$result = $this->element->getSession($session['id']);

		$bookings = Booking::model()->findAllByAttributes(
			array('session_id' => $session['id']));
		$bookingCount = $bookingTime = 0;
		foreach ($bookings as $appt) {
			$bookingCount++;
			$operation = ElementOperation::model()->findByPk($appt['element_operation_id']);
			$bookingTime += $operation->total_duration;
		}

		$expected = array(
			'bookings' => $bookingCount,
			'bookings_duration' => $bookingTime,
			'date' => $session['date'],
			'duration' => 240,
			'start_time' => $session['start_time'],
			'end_time' => $session['end_time'],
			'id' => $theatre['id'],
			'site_id' => $theatre['site_id'],
			'time_available' => 150,
			'status' => 'available',
			'code' => '',
			'comments' => ''
		);

		$this->assertEquals($expected, $result);
	}

	public function testGetSession_Full_ReturnsCorrectData()
	{
		$session = $this->sessions[0];
		$theatre = $this->theatres['theatre1'];

		$bookings = Booking::model()->findAllByAttributes(
			array('session_id' => $session['id']));
		$bookingCount = $bookingTime = 0;
		foreach ($bookings as $appt) {
			$bookingCount++;
			$operation = ElementOperation::model()->findByPk($appt['element_operation_id']);
			$_POST['Procedures'] = array($this->procedures['procedure1']['id']);
			$operation->total_duration = 240;
			$operation->save();
			$bookingTime += $operation->total_duration;
		}

		$expected = array(
			'bookings' => $bookingCount,
			'bookings_duration' => $bookingTime,
			'date' => $session['date'],
			'duration' => 240,
			'start_time' => $session['start_time'],
			'end_time' => $session['end_time'],
			'id' => $theatre['id'],
			'site_id' => $theatre['site_id'],
			'time_available' => 0,
			'status' => 'full',
			'code' => '',
			'comments' => ''
		);

		$result = $this->element->getSession($session['id']);

		$this->assertEquals($expected, $result);
	}

	public function testGetBookingService_ReturnsNewInstance()
	{
		$service = new BookingService;

		$this->assertEquals($service, $this->element->getBookingService());
	}

	/**
	 * @dataProvider dataProvider_Weekdays
	 */
	public function testGetWeekdayText($index, $weekday)
	{
		$this->assertEquals($weekday, $this->element->getWeekdayText($index));
	}

	/**
	 * @dataProvider dataProvider_EyeLabels
	 */
	public function testGetEyeLabelText($eye, $label)
	{
		$this->element->eye = $eye;

		$this->assertEquals($label, $this->element->getEyeLabelText());
	}

	public function testGetWardOptions_MissingSiteId_ThrowsException()
	{
		$this->setExpectedException('Exception', 'Site id is required.');
		$this->element->getWardOptions(null);
	}

	/**
	 * @dataProvider dataProvider_WardOptions
	 */
	public function testGetWardOptions_ValidSiteId_ReturnsCorrectWard($patientAge, $patientGender, $wardList)
	{
		Yii::app()->params['pseudonymise_patient_details'] = false;

		$operation = $this->elements('element1');

		$patient = $this->patients('patient1');

		$patient->dob = date('Y-m-d', strtotime("-{$patientAge} years"));
		$patient->gender = $patientGender;
		$patient->save(false);

		$siteId = 1;

		$expected = array();
		foreach ($wardList as $wardKey) {
			$ward = $this->wards($wardKey);
			$expected[$ward->id] = $ward->name;
		}

		$this->assertEquals($expected, $operation->getWardOptions($siteId));
	}

	/**
	 * @dataProvider dataProvider_WardOptions
	 */
	public function testGetWardOptions_IncludeTheatreIdWithoutWard_ReturnsCorrectWard($patientAge, $patientGender, $wardList)
	{
		Yii::app()->params['pseudonymise_patient_details'] = false;

		$operation = $this->elements('element1');

		$patient = $this->patients('patient1');

		$patient->dob = date('Y-m-d', strtotime("-{$patientAge} years"));
		$patient->gender = $patientGender;
		$patient->save(false);

		$siteId = 1;
		$theatreId = 1;

		TheatreWardAssignment::model()->deleteAll();

		$expected = array();
		foreach ($wardList as $wardKey) {
			$ward = $this->wards($wardKey);
			$expected[$ward->id] = $ward->name;
		}

		$this->assertEquals($expected, $operation->getWardOptions($siteId, $theatreId));
	}

	public function testGetWardOptions_IncludeTheatreIdWithWard_ReturnsCorrectWard()
	{
		Yii::app()->params['pseudonymise_patient_details'] = false;

		$operation = $this->elements('element1');

		$patient = $this->patients('patient1');

		$patient->dob = date('Y-m-d', strtotime("-20 years"));
		$patient->gender = 'F';
		$patient->save(false);

		$siteId = 1;
		$theatreId = 1;
		$ward = $this->wards('ward1');

		$assignment = new TheatreWardAssignment;
		$assignment->theatre_id = $theatreId;
		$assignment->ward_id = $ward->id;
		$assignment->save();

		$expected = array($ward->id => $ward->name); // ordinarily would go in ward 4

		$this->assertEquals($expected, $operation->getWardOptions($siteId, $theatreId));
	}

	public function testGetCancellationText_NoCancellation_ReturnsEmptyString()
	{
		$operation = $this->elements('element1');
		CancelledOperation::model()->deleteAll();
		$this->assertEquals('', $operation->getCancellationText(), 'Cancellation text should be blank.');
	}

	public function testGetCancellationText_ValidCancellation_ReturnsCorrectText()
	{
		$operation = $this->elements('element1');
		$cancelledTime = strtotime('-1 day');
		$cancelledDate = date('Y-m-d', $cancelledTime);
		$user = $this->users['user1'];
		$reason = $this->reasons['reason1'];

		$cancel = new CancelledOperation;
		$cancel->element_operation_id = $operation->id;
		$cancel->cancelled_date = $cancelledDate;
		$cancel->user_id = $user['id'];
		$cancel->cancelled_reason_id = $reason['id'];
		$cancel->save();

		$expected = "Operation Cancelled: By {$user['first_name']} " .
			"{$user['last_name']} on " . date('F j, Y', $cancelledTime) .
			" [{$reason['text']}]";

		$this->assertEquals($expected, $operation->getCancellationText(), 'Cancellation text should match.');
	}

	protected function getWeekday($index)
	{
		switch($index) {
			case 1:
				$weekday = 'Monday';
				break;
			case 2:
				$weekday = 'Tuesday';
				break;
			case 3:
				$weekday = 'Wednesday';
				break;
			case 4:
				$weekday = 'Thursday';
				break;
			case 5:
				$weekday = 'Friday';
				break;
			case 6:
				$weekday = 'Saturday';
				break;
			case 7:
				$weekday = 'Sunday';
				break;
		}
		return $weekday;
	}
}
