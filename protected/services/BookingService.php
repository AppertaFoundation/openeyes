<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class BookingService
{
	/**
	 * Search sessions that match booking requirements, and figure out how
	 * full they would be
	 *
	 * @return CDbReader
	 */
	public function findSessions($monthStart, $minDate, $firmId)
	{
		if ($firmId !== null) {
			$firm = Firm::model()->findByPk($firmId);
			if (empty($firm)) {
				throw new Exception('Firm id is invalid.');
			}
		}
		if (substr($minDate,0,8) == substr($monthStart,0,8)) {
			$startDate = $minDate;
		} else {
			$startDate = $monthStart;
		}
		$monthEnd = substr($monthStart,0,8) . date('t', strtotime($monthStart));

		if ($firmId === null) {
			$firmSql = 'f.id IS NULL';
		} else {
			$firmSql = "f.firm_id = $firmId";
		}

		$sql = "
			SELECT s.*, TIMEDIFF(s.end_time, s.start_time) AS session_duration,
				COUNT(a.id) AS bookings,
				SUM(o.total_duration) AS bookings_duration
			FROM `session` `s`
			JOIN `theatre` `t` ON s.theatre_id = t.id
			LEFT JOIN `session_firm_assignment` `f` ON s.id = f.session_id
			LEFT JOIN `booking` `a` ON s.id = a.session_id
			LEFT JOIN `element_operation` `o` ON a.element_operation_id = o.id
			LEFT JOIN `event` `e` ON `o`.event_id = `e`.id
			WHERE s.status != " . Session::STATUS_UNAVAILABLE . " AND
				s.date BETWEEN CAST('$startDate' AS DATE) AND
				CAST('$monthEnd' AS DATE) AND $firmSql
			GROUP BY s.id
			ORDER BY WEEKDAY(DATE) ASC
		";
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
		if ($firmId === null) {
			$firmSql = 'f.id IS NULL';
		} else {
			$firm = Firm::model()->findByPk($firmId);
			if (empty($firm)) {
				throw new Exception('Firm id is invalid.');
			}
			$firmSql = "f.firm_id = $firmId";
		}

		$sql = "
			SELECT t.*, s.start_time, s.end_time, s.id AS session_id,
				s.consultant, s.anaesthetist, s.paediatric, s.general_anaesthetic,
				TIMEDIFF(s.end_time, s.start_time) AS session_duration,
				COUNT(a.id) AS bookings,
				SUM(o.total_duration) AS bookings_duration
			FROM `session` `s`
			JOIN `theatre` `t` ON s.theatre_id = t.id
			LEFT JOIN `session_firm_assignment` `f` ON s.id = f.session_id
			LEFT JOIN `booking` `a` ON s.id = a.session_id
			LEFT JOIN `element_operation` `o` ON a.element_operation_id = o.id
			LEFT JOIN `event` `e` ON `o`.event_id = `e`.id
			WHERE s.status != " . Session::STATUS_UNAVAILABLE . "
				AND s.date = '$date' AND $firmSql
				AND `e`.hidden = 0
			GROUP BY s.id
			ORDER BY s.start_time
		";
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
		$sql = "SELECT t.*, s.start_time, s.end_time, s.date, s.comments,
				TIMEDIFF(s.end_time, s.start_time) AS session_duration,
				COUNT(a.id) AS bookings,
				SUM(o.total_duration) AS bookings_duration, site.id AS site_id
			FROM `session` `s`
			JOIN `booking` `a` ON s.id = a.session_id
			JOIN `element_operation` `o` ON a.element_operation_id = o.id
			JOIN `event` `e` ON `o`.event_id = `e`.id
			JOIN `theatre` `t` ON s.theatre_id = t.id
			JOIN `site` ON site.id = t.site_id
			WHERE s.id = '" . $sessionId . "'
			AND `e`.hidden = 0";

		$sessions = Yii::app()->db->createCommand($sql)->query();

		return $sessions;
	}

	/**
	 * Get the date of the next session for the firm
	 *
	 * @param int $firmId
	 * @return string
	 */
	public function getNextSessionDate($firmId)
	{
		$date = Yii::app()->db->createCommand()
			->select('date')
			->from('session s')
			->join('session_firm_assignment ssa', 'ssa.session_id = s.id')
			->where('firm_id = :fid AND date >= CURDATE()', array(':fid' => $firmId))
			->order('date ASC')
			->limit(1)
			->queryRow();

		if (empty($date)) {
			// No sessions, return today
			return date('Y-m-d');
		} else {
			return $date['date'];
		}
	}

	/**
	 * Search for theatres/sessions, filtered by site/specialty/firm/theatre
	 *
	 * @param string  $startDate (YYYY-MM-DD)
	 * @param string  $endDate   (YYYY-MM-DD)
	 * @param integer $siteId
	 * @param integer $theatreId
	 * @param integer $specialtyId
	 * @param integer $firmId
	 * @param integer $wardId
	 * @return CDbReader
	 */
	public function findTheatresAndSessions(
		$startDate,
		$endDate,
		$siteId = null,
		$theatreId = null,
		$specialtyId = null,
		$firmId = null,
		$wardId = null,
		$emergencyList = null
	) {
		if (empty($startDate) || empty($endDate) ||
			(strtotime($endDate) < strtotime($startDate))) {
			throw new Exception('Invalid start and end dates.');
		}

		$whereSql = 's.date BETWEEN :start AND :end';
		$whereParams = array(':start' => $startDate, ':end' => $endDate);

		if (empty($emergencyList)) {
			if (!empty($siteId)) {
				$whereSql .= ' AND t.site_id = :siteId';
				$whereParams[':siteId'] = $siteId;
			}
			if (!empty($theatreId)) {
				$whereSql .= ' AND t.id = :theatreId';
				$whereParams[':theatreId'] = $theatreId;
			}
			if (!empty($specialtyId)) {
				$whereSql .= ' AND spec.id = :specialtyId';
				$whereParams[':specialtyId'] = $specialtyId;
			}
			if (!empty($firmId)) {
				$whereSql .= ' AND f.id = :firmId';
				$whereParams[':firmId'] = $firmId;
			}
			if (!empty($wardId)) {
				$whereSql .= ' AND w.id = :wardId';
				$whereParams[':wardId'] = $wardId;
			}

			$whereSql .= ' AND e.hidden = 0';

			$command = Yii::app()->db->createCommand()
				// TODO: References to sequences need to be removed when possible
				->select('DISTINCT(o.id) AS operation_id, t.name, i.short_name as site_name, s.date, s.start_time, s.end_time, s.id AS session_id, s.sequence_id,
					TIMEDIFF(s.end_time, s.start_time) AS session_duration, s.comments AS session_comments,
					s.consultant as session_consultant, s.anaesthetist as session_anaesthetist, s.paediatric as session_paediatric, s.general_anaesthetic as session_general_anaesthetic,
					f.name AS firm_name, spec.name AS specialty_name,
					o.eye, o.anaesthetic_type, o.comments, b.admission_time,
					o.consultant_required, o.overnight_stay,
					e.id AS eventId, ep.id AS episodeId, p.id AS patientId,
					o.total_duration AS operation_duration, p.first_name,
					p.last_name, p.dob, p.gender, p.hos_num, w.name AS ward, b.display_order, b.confirmed, o.urgent, s.status, mu.first_name AS mu_fn, mu.last_name AS mu_ln, cu.first_name as cu_fn, cu.last_name as cu_ln, s.last_modified_date, su.first_name as session_first_name, su.last_name as session_last_name')
				->from('session s')
				->join('theatre t', 't.id = s.theatre_id')
				->leftJoin('site i', 'i.id = t.site_id')
				->leftJoin('booking b', 'b.session_id = s.id')
				->leftJoin('element_operation o', 'o.id = b.element_operation_id')
				->leftJoin('event e', 'e.id = o.event_id')
				->leftJoin('episode ep', 'ep.id = e.episode_id')
				->leftJoin('patient p', 'p.id = ep.patient_id')
				->join('session_firm_assignment sfa', 'sfa.session_id = s.id')
				->join('firm f', 'f.id = sfa.firm_id')
				->join('service_specialty_assignment ssa', 'ssa.id = f.service_specialty_assignment_id')
				->join('specialty spec', 'spec.id = ssa.specialty_id')
				->leftJoin('user mu','b.last_modified_user_id = mu.id')
				->leftJoin('user cu','b.created_user_id = cu.id')
				->leftJoin('user su','s.last_modified_user_id = su.id')
				->leftJoin('ward w', 'w.id = b.ward_id')
				->where($whereSql, $whereParams)
				->order('t.name ASC, s.date ASC, s.start_time ASC, s.end_time ASC, b.display_order ASC');
		} else {
			$whereSql .= ' AND sfa.id IS NULL';

			$command = Yii::app()->db->createCommand()
				// TODO: References to sequences need to be removed when possible
				->select('DISTINCT(o.id) AS operation_id, t.name, i.short_name as site_name, s.date, s.start_time, s.end_time, s.id AS session_id, s.sequence_id,
					TIMEDIFF(s.end_time, s.start_time) AS session_duration, s.comments AS session_comments,
					s.consultant as session_consultant, s.anaesthetist as session_anaesthetist, s.paediatric as session_paediatric, s.general_anaesthetic as session_general_anaesthetic,
					o.eye, o.anaesthetic_type, o.comments, b.admission_time,
					o.consultant_required, o.overnight_stay,
					e.id AS eventId, ep.id AS episodeId, p.id AS patientId,
					o.total_duration AS operation_duration, p.first_name,
					p.last_name, p.dob, p.gender, p.hos_num, w.name AS ward, b.display_order, b.confirmed, o.urgent, s.status, mu.first_name AS mu_fn, mu.last_name AS mu_ln, cu.first_name as cu_fn, cu.last_name as cu_ln, s.last_modified_date, su.first_name as session_first_name, su.last_name as session_last_name')
				->from('session s')
				->join('theatre t', 't.id = s.theatre_id')
				->leftJoin('site i', 'i.id = t.site_id')
				->leftJoin('booking b', 'b.session_id = s.id')
				->leftJoin('element_operation o', 'o.id = b.element_operation_id')
				->leftJoin('event e', 'e.id = o.event_id')
				->leftJoin('episode ep', 'ep.id = e.episode_id')
				->leftJoin('patient p', 'p.id = ep.patient_id')
				->leftJoin('session_firm_assignment sfa', 'sfa.session_id = s.id')
				->leftJoin('ward w', 'w.id = b.ward_id')
				->leftJoin('user mu','b.last_modified_user_id = mu.id')
				->leftJoin('user cu','b.created_user_id = cu.id')
				->leftJoin('user su','s.last_modified_user_id = su.id')
				->where($whereSql, $whereParams)
				->order('t.name ASC, s.date ASC, s.start_time ASC, s.end_time ASC, b.display_order ASC');
		}

		return $command->queryAll();
	}
}
