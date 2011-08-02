<?php

class BookingService
{
	/**
	 * Search sequences that match booking requirements, and figure out how 
	 * full the respective sessions would be
	 *
	 * @return CDbReader
	 */
	public function findSessions($monthStart, $minDate, $firmId)
	{
		$firm = Firm::model()->findByPk($firmId);
		if (empty($firm)) {
			throw new Exception('Firm id is invalid.');
		}
		
		if (substr($minDate,0,8) == substr($monthStart,0,8)) {
			$startDate = $minDate;
		} else {
			$startDate = $monthStart;
		}
		$monthEnd = substr($monthStart,0,8) . date('t', strtotime($monthStart));
		
		// @todo: Figure out a nice Yii way of doing the union of these two queries
		$sql = "SELECT s.*, TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				COUNT(a.id) AS bookings, 
				SUM(o.total_duration) AS bookings_duration
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id
			JOIN `booking` `a` ON s.id = a.session_id
			JOIN `element_operation` `o` ON a.element_operation_id = o.id
			WHERE s.date BETWEEN CAST('" . $startDate . "' AS DATE) AND 
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
				s.date BETWEEN CAST('" . $startDate . "' AS DATE) AND 
				CAST('" . $monthEnd . "' AS DATE) AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)
		ORDER BY WEEKDAY( DATE ) ASC";
		
		$sessions = Yii::app()->db->createCommand($sql)->query();
		
		return $sessions;
	}
	
	/**
	 * Search theatres that match booking requirements, and find their 
	 * associated session data
	 * 
	 * @param string $date (YYYY-MM-DD format)
	 * @param integer $firmId firm ID
	 * @return CDbReader
	 */
	public function findTheatres($date, $firmId)
	{
		$firm = Firm::model()->findByPk($firmId);
		if (empty($firm)) {
			throw new Exception('Firm id is invalid.');
		}
		
		// @todo: Figure out a nice Yii way of doing the union of these two queries
		$sql = "SELECT t.*, s.start_time, s.end_time, s.id AS session_id, 
				TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				COUNT(a.id) AS bookings, 
				SUM(o.total_duration) AS bookings_duration 
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id
			JOIN `booking` `a` ON s.id = a.session_id
			JOIN `element_operation` `o` ON a.element_operation_id = o.id 
			JOIN `theatre` `t` ON q.theatre_id = t.id 
			WHERE s.date = '" . $date . "' AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)
			GROUP BY s.id 
		UNION 
			SELECT t.*, s.start_time, s.end_time, s.id AS session_id, 
				TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				0 AS bookings, 0 AS bookings_duration
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id 
			JOIN `theatre` `t` ON q.theatre_id = t.id 
			WHERE s.id NOT IN (SELECT DISTINCT (session_id) FROM booking) AND 
				s.date = '" . $date . "' AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)";
		
		$sessions = Yii::app()->db->createCommand($sql)->query();
		
		return $sessions;
	}
	
	/**
	 * Search sessions by ID and find their associated data
	 * 
	 * @param integer $sessionId session ID
	 * @return CDbReader
	 */
	public function findSession($sessionId)
	{
		$sql = "SELECT t.*, s.start_time, s.end_time, s.date, 
				TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				COUNT(a.id) AS bookings, 
				SUM(o.total_duration) AS bookings_duration 
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `booking` `a` ON s.id = a.session_id
			JOIN `element_operation` `o` ON a.element_operation_id = o.id 
			JOIN `theatre` `t` ON q.theatre_id = t.id 
			WHERE s.id = '" . $sessionId . "'";
		
		$sessions = Yii::app()->db->createCommand($sql)->query();
		
		return $sessions;
	}
	
	/**
	 * Search for theatres/sessions, filtered by site/service/firm/theatre
	 * 
	 * @param string  $startDate (YYYY-MM-DD)
	 * @param string  $endDate   (YYYY-MM-DD)
	 * @param integer $siteId
	 * @param integer $theatreId
	 * @param integer $serviceId
	 * @param integer $firmId 
	 * @return CDbReader
	 */
	public function findTheatresAndSessions($startDate, $endDate, $siteId = null, $theatreId = null, $serviceId = null, $firmId = null)
	{
		if (empty($startDate) || empty($endDate) || 
			(strtotime($endDate) < strtotime($startDate))) {
			throw new Exception('Invalid start and end dates.');
		}
		
		$whereSql = 's.date BETWEEN :start AND :end';
		$whereParams = array(':start' => $startDate, ':end' => $endDate);
		
		if (!empty($siteId)) {
			$whereSql .= ' AND t.site_id = :siteId';
			$whereParams[':siteId'] = $siteId;
		}
		if (!empty($theatreId)) {
			$whereSql .= ' AND t.id = :theatreId';
			$whereParams[':theatreId'] = $theatreId;
		}
		if (!empty($serviceId)) {
			$whereSql .= ' AND ser.id = :serviceId';
			$whereParams[':serviceId'] = $serviceId;
		}
		if (!empty($firmId)) {
			$whereSql .= ' AND f.id = :firmId';
			$whereParams[':firmId'] = $firmId;
		}
		
		$command = Yii::app()->db->createCommand()
			->select('t.id, t.name, s.date, s.start_time, s.end_time, s.id AS session_id, 
				TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				o.eye, o.anaesthetic_type, o.comments, 
				o.total_duration AS operation_duration, p.first_name, 
				p.last_name, p.dob, p.gender, o.id AS operation_id, 
				a.display_order')
			->from('session s')
			->join('sequence q', 's.sequence_id = q.id')
			->join('theatre t', 't.id = q.theatre_id')
			->join('booking a', 'a.session_id = s.id')
			->join('element_operation o', 'o.id = a.element_operation_id')
			->join('event e', 'e.id = o.event_id')
			->join('episode ep', 'ep.id = e.episode_id')
			->join('patient p', 'p.id = ep.patient_id')
			->join('sequence_firm_assignment sfa', 'sfa.sequence_id = q.id')
			->join('firm f', 'f.id = sfa.firm_id')
			->join('service_specialty_assignment ssa', 'ssa.id = f.service_specialty_assignment_id')
			->join('service ser', 'ser.id = ssa.service_id')
			->where($whereSql, $whereParams)
			->order('t.name ASC, s.date ASC, a.display_order ASC');
		
		return $command->queryAll();
	}
}
