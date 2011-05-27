<?php

class BookingService
{
	/**
	 * Search sequences that match appointment requirements, and figure out how 
	 * full the respective sessions would be
	 *
	 * @return eventId => int || false
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
				COUNT(a.id) AS appointments, 
				SUM(o.total_duration) AS appointments_duration
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id
			JOIN `appointment` `a` ON s.id = a.session_id
			JOIN `element_operation` `o` ON a.element_operation_id = o.id
			WHERE s.date BETWEEN CAST('" . $startDate . "' AS DATE) AND 
				CAST('" . $monthEnd . "' AS DATE) AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)
			GROUP BY s.id 
		UNION 
			SELECT s.*, TIMEDIFF(s.end_time, s.start_time) AS session_duration, 
				0 AS appointments, 0 AS appointments_duration
			FROM `session` `s` 
			JOIN `sequence` `q` ON s.sequence_id = q.id
			JOIN `sequence_firm_assignment` `f` ON q.id = f.sequence_id
			WHERE s.id NOT IN (SELECT DISTINCT (session_id) FROM appointment) AND 
				s.date BETWEEN CAST('" . $startDate . "' AS DATE) AND 
				CAST('" . $monthEnd . "' AS DATE) AND 
				(f.firm_id = " . $firmId . " OR f.id IS NULL)
		ORDER BY WEEKDAY( DATE ) ASC";
		
		$sessions = Yii::app()->db->createCommand($sql)->query();
		
		return $sessions;
	}
}
