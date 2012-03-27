<?php
class PasObserver {

	/**
	 * Update patient from PAS
	 * @param array $params
	 */
	public function updatePatientFromPas($params) {
		$patient = $params['patient'];
		$assignment = PasAssignment::model()->findByInternal('Patient', $patient->id);
		if(!$assignment) {
			// Try to find PAS patient using hos_num
			$hos_num = sprintf('%07d',$patient->hos_num);
			$num_id_type = substr($hos_num,0,1);
			$number_id = substr($hos_num,1,6);
			$pas_patient_number = PAS_PatientNumber::model()->find('num_id_type = :num_id_type AND number_id = :number_id', array(
					':num_id_type' => $num_id_type,
					':number_id' => $number_id
			));
			if($pas_patient_number) {
				$assignment = new PasAssignment();
				$assignment->internal_id = $patient->id;
				$assignment->internal_type = 'Patient';
				$assignment->external_id = $pas_patient_number->RM_PATIENT_NO;
				$assignment->external_type = 'PAS_Patient';
			} else {
				throw new CException('Cannot map patient');
				// @TODO Push an alert that the patient cannot be mapped
			}
		}
		if(PasAssignment::is_stale('Patient', $patient->id)) {
			Yii::log('Patient details stale', 'trace');
			$pas_service = new PasService();
			if ($pas_service->available) {
				$pas_service->updatePatientFromPas($patient, $assignment);
			} else {
				// @TODO Push an alert onto the user's screen
			}
		}
	}

	/**
	 * Update GP from PAS
	 * @param array $params
	 */
	public function updateGpFromPas($params) {
		$gp = $params['gp'];
		if(strtotime($gp->last_modified_date) < (time() - PasService::PAS_CACHE_TIME)) {
			Yii::log('GP details stale', 'trace');
			$gp_service = new GpService($gp);
			$gp_service->loadFromPas();
			// @TODO Push an alert onto the user's screen if PAS is down
		}
	}

	/**
	 * Search PAS for patient
	 * @param array $params
	 */
	public function searchPas($params) {
		$pas_service = new PasService();
		if($pas_service->available) {
			$data = array();
			foreach(array('first_name','last_name','hos_num') as $param) {
				$data[$param] = $params['patient']->$param;
			}
			$data['sortBy'] = $params['params']['sortBy'];
			$data['sortDir'] = $params['params']['sortDir'];
			$params['criteria'] = $pas_service->search($data, $params['params']['pageSize'], $params['params']['currentPage']);
		} else {
			// @TODO Push an alert onto the user's screen
		}
	}

}
