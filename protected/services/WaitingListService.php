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

class WaitingListService
{
	/**
	 * Gets the list of operations in need of a booking, i.e. the waiting list.
	 *
	 * Can be filtered by (service_id OR firm_id) and/or type of letter.
	 * Letter types are invitation, 1st reminder, 2nd reminder, gp referral, removal.
	 *
	 * Letter status is based on the 'datetime' field for operations to be booked and the 'cancelled_date' field for operations
	 *	to be rescheduled.
	 *
	 * @param int $firmId
	 * @param int $subspecialtyId
	 * @param int $status
	 * @return array
	 */
	public function getWaitingList($firmId, $subspecialtyId, $status, $hos_num=false, $site_id=false)
	{
		$whereSql = '';
		// intval() for basic data sanitising
		if (!empty($firmId)) {
			$whereSql .= ' AND f.id = ' . intval($firmId).' ';
		} elseif (!empty($subspecialtyId)) {
			$whereSql .= ' AND ssa.subspecialty_id = ' . intval($subspecialtyId).' ';
		}

		if ($hos_num && ctype_digit($hos_num)) {
			$whereSql .= " AND pat.hos_num REGEXP '^[0]*$hos_num$' ";
		}

		if ($site_id && ctype_digit($site_id)) {
			$whereSql .= " AND eo.site_id = $site_id ";
		}

		$whereSql2 = $whereSql;

		/*
		if (!empty($status)) {
			switch ($status) {
				case ElementOperation::LETTER_INVITE:
					$whereSql .= ' AND datetime >= (NOW() - interval 14 day)';
					$whereSql2 .= ' AND cancelled_date >= (NOW() - interval 14 day)';
					break;
				case ElementOperation::LETTER_REMINDER_1:
					$whereSql .= ' AND (datetime < (NOW() - interval 14 day) AND datetime >= (NOW() - interval 28 day))';
					$whereSql2 .= ' AND (cancelled_date < (NOW() - interval 14 day) AND cancelled_date >= (NOW() - interval 28 day))';
					break;
				case ElementOperation::LETTER_REMINDER_2:
					$whereSql .= ' AND (datetime < (NOW() - interval 28 day) AND datetime >= (NOW() - interval 42 day))';
					$whereSql2 .= ' AND (cancelled_date < (NOW() - interval 28 day) AND cancelled_date >= (NOW() - interval 42 day))';
					break;
				case ElementOperation::LETTER_GP:
					$whereSql .= ' AND (datetime < (NOW() - interval 42 day) AND datetime >= (NOW() - interval 56 day))';
					$whereSql2 .= ' AND (cancelled_date < (NOW() - interval 42 day) AND cancelled_date >= (NOW() - interval 56 day))';
					break;
				case ElementOperation::LETTER_REMOVAL:
					$whereSql .= ' AND datetime < (NOW() - interval 56 day)';
					$whereSql2 .= ' AND cancelled_date < (NOW() - interval 56 day)';
					break;
				default:
					break;
			}
		}
		*/

		$sql = '
			SELECT
				eo.id AS eoid,
				eo.decision_date as decision_date,
				ev.id AS evid,
				ep.id AS epid,
				pat.id AS pid,
				co.first_name,
				co.last_name,
				pat.hos_num,
				pat.gp_id,
				pat.practice_id,
				pad.id AS practice_address_id,
				GROUP_CONCAT(p.short_format SEPARATOR ", ") AS List
			FROM element_operation eo
			JOIN event ev ON eo.event_id = ev.id
			JOIN episode ep ON ev.episode_id = ep.id
			JOIN firm f ON ep.firm_id = f.id
			JOIN service_subspecialty_assignment ssa ON f.service_subspecialty_assignment_id = ssa.id
			JOIN patient pat ON ep.patient_id = pat.id
			JOIN contact co ON co.parent_id = pat.id AND co.parent_class = \'Patient\'
			JOIN operation_procedure_assignment opa ON opa.operation_id = eo.id
			JOIN proc p ON opa.proc_id = p.id
			LEFT JOIN address pad ON pad.parent_id = pat.practice_id AND pad.parent_class = \'Practice\'
			WHERE
				ep.end_date IS NULL
			AND
				eo.status = ' . ElementOperation::STATUS_PENDING . '
			' . $whereSql . '
			AND
				ev.deleted = 0
			GROUP BY
				opa.operation_id
		UNION
			SELECT
				eo.id AS eoid,
				eo.decision_date as decision_date,
				ev.id AS evid,
				ep.id AS epid,
				pat.id AS pid,
				co.first_name,
				co.last_name,
				pat.hos_num,
				pat.gp_id,
				pat.practice_id,
				pad.id AS practice_address_id,
				GROUP_CONCAT(p.short_format SEPARATOR ", ") AS List
			FROM element_operation eo
			JOIN event ev ON eo.event_id = ev.id
			JOIN episode ep ON ev.episode_id = ep.id
			JOIN firm f ON ep.firm_id = f.id
			JOIN service_subspecialty_assignment ssa ON f.service_subspecialty_assignment_id = ssa.id
			JOIN patient pat ON ep.patient_id = pat.id
			JOIN contact co ON co.parent_id = pat.id AND co.parent_class = \'Patient\'
			JOIN operation_procedure_assignment opa ON opa.operation_id = eo.id
			JOIN proc p ON opa.proc_id = p.id
			LEFT JOIN address pad ON pad.parent_id = pat.practice_id AND pad.parent_class = \'Practice\'
			WHERE
				ep.end_date IS NULL
			AND
				eo.status = ' . ElementOperation::STATUS_NEEDS_RESCHEDULING . '
			' . $whereSql2 . '
			AND
				ev.deleted = 0
			GROUP BY
				opa.operation_id
			ORDER BY decision_date ASC
		';

		return Yii::app()->db->createCommand($sql)->query();
	}
}
