<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
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
	 * @param int $specialtyId
	 * @param int $status
	 * @return array
	 */
	public function getWaitingList($firmId, $specialtyId, $status, $hos_num=false, $site_id=false)
	{
		$whereSql = '';
		// intval() for basic data sanitising
		if (!empty($firmId)) {
			$whereSql .= ' AND f.id = ' . intval($firmId).' ';
		} elseif (!empty($specialtyId)) {
			$whereSql .= ' AND ssa.specialty_id = ' . intval($specialtyId).' ';
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
				pat.hash as phash,
				pat.first_name,
				pat.last_name,
				pat.hos_num,
				pat.gp_id,
				GROUP_CONCAT(p.short_format SEPARATOR ", ") AS List
			FROM
				element_operation eo,
				event ev,
				episode ep,
				firm f,
				service_specialty_assignment ssa,
				patient pat,
				operation_procedure_assignment opa,
				proc p
			WHERE
				eo.event_id = ev.id
			AND
				ev.episode_id = ep.id
			AND
				ep.firm_id = f.id
			AND
				f.service_specialty_assignment_id = ssa.id
			AND
				ep.patient_id = pat.id
			AND
				opa.operation_id = eo.id
			AND
				opa.proc_id = p.id
			AND
				ep.end_date IS NULL
			AND
				eo.status = ' . ElementOperation::STATUS_PENDING . '
			' . $whereSql . '
			GROUP BY
				opa.operation_id
		UNION
			SELECT
				eo.id AS eoid,
				eo.decision_date as decision_date,
				ev.id AS evid,
				ep.id AS epid,
				pat.id AS pid,
				pat.hash as phash,
				pat.first_name,
				pat.last_name,
				pat.hos_num,
				pat.gp_id,
			GROUP_CONCAT(p.short_format SEPARATOR ", ") AS List
			FROM
				element_operation eo,
				event ev,
				episode ep,
				firm f,
				service_specialty_assignment ssa,
				patient pat,
				operation_procedure_assignment opa,
				proc p,
				cancelled_booking cb
			WHERE
				eo.event_id = ev.id
			AND
				ev.episode_id = ep.id
			AND
				ep.firm_id = f.id
			AND
				f.service_specialty_assignment_id = ssa.id
			AND
				ep.patient_id = pat.id
			AND
				opa.operation_id = eo.id
			AND
				opa.proc_id = p.id
			AND
				ep.end_date IS NULL
			AND
				eo.status = ' . ElementOperation::STATUS_NEEDS_RESCHEDULING . '
			AND
				cb.element_operation_id = eo.id
			' . $whereSql2 . '
			GROUP BY
				opa.operation_id
			ORDER BY decision_date ASC
		';

		return Yii::app()->db->createCommand($sql)->query();
	}
}
