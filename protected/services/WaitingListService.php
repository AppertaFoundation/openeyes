<?php

class WaitingListService
{
        /**
         * Gets the list of operations in need of a booking, i.e. the waiting list.
         *
         * @param int $firmId
         * @return array
         */
        public function getWaitingList($firmId)
        {
                $firm = Firm::model()->findByPk($firmId);

                return Yii::app()->db->createCommand()
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
                                ->where('ssa.service_id = :ssa_id AND (eo.status = :status1 OR eo.status = :status2)', array(
					'ssa_id' => $firm->serviceSpecialtyAssignment->service_id,
					'status1' => ElementOperation::STATUS_PENDING,
					'status2' => ElementOperation::STATUS_NEEDS_RESCHEDULING
				))
				->group('opa.operation_id')
                                ->queryAll();
        }
}
