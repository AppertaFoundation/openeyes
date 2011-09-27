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
         * @param int $firmId
         * @return array
         */
        public function getWaitingList($firmId, $serviceId, $status)
        {
		$whereSql = array('ep.end_date IS NULL', '(eo.status = :status1 OR eo.status = :status2)');
		$whereParams = array(
                                        'status1' => ElementOperation::STATUS_PENDING,
                                        'status2' => ElementOperation::STATUS_NEEDS_RESCHEDULING
                                );

		if (!empty($firmId)) {
			array_push($whereSql, 'AND f.id = :f_id');
			$whereParams['f_id'] = $firmId;
		} elseif (!empty($serviceId)) {
			array_push($whereSql, 'AND ssa.service_id = :sr_id');
			$whereParams['sr_id'] = $serviceId;
		}

		if (!empty($status)) {
			switch ($status) {
                                case ElementOperation::LETTER_INVITE:
                                        array_push($whereSql, 'datetime >= (NOW() - interval 14 day)');
                                        break;
				case ElementOperation::LETTER_REMINDER_1:
					array_push($whereSql, '(datetime < (NOW() - interval 14 day) AND datetime >= (NOW() - interval 28 day))');
					break;
                                case ElementOperation::LETTER_REMINDER_2:
                                        array_push($whereSql, '(datetime < (NOW() - interval 28 day) AND datetime >= (NOW() - interval 42 day))');
                                        break;
                                case ElementOperation::LETTER_GP:
                                        array_push($whereSql, '(datetime < (NOW() - interval 42 day) AND datetime >= (NOW() - interval 56 day))');
                                        break;
                                case ElementOperation::LETTER_REMOVAL:
                                        array_push($whereSql, 'datetime < (NOW() - interval 56 day)');
                                        break;
				default:
					break;
			}
		}

                $waitingList =  Yii::app()->db->createCommand()
                                ->select('
					eo.id AS eoid,
					ev.id AS evid,
					ep.id AS epid,
					pat.id AS pid,
					pat.first_name,
					pat.last_name,
					pat.hos_num,
					GROUP_CONCAT(p.term SEPARATOR ", ") AS List
				')
                                ->from('element_operation eo')
                                ->join('event ev', 'eo.event_id = ev.id')
                                ->join('episode ep', 'ev.episode_id = ep.id')
                                ->join('firm f', 'ep.firm_id = f.id')
                                ->join('service_specialty_assignment ssa', 'f.service_specialty_assignment_id = ssa.id')
                                ->join('patient pat', 'ep.patient_id = pat.id')
				->join('operation_procedure_assignment opa', 'opa.operation_id = eo.id')
				->join('procedure p', 'opa.procedure_id = p.id')
                                ->where(implode(' AND ', $whereSql), $whereParams)
				->group('opa.operation_id')
                                ->queryAll();

		return $waitingList;
        }
}
