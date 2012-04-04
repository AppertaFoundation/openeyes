<?php
class PasObserver {

	/**
	 * Update patient from PAS
	 * @param array $params
	 */
	public function updatePatientFromPas($params) {
		$pas_service = new PasService();
		if ($pas_service->available) {
			$patient = $params['patient'];
			$assignment = PasAssignment::model()->findByInternal('Patient', $patient->id);
			if(!$assignment) {
				Yii::log('Creating new patient assignment', 'trace');
				// Assignment doesn't exist yet, try to find PAS patient using hos_num
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
			if($assignment->isStale()) {
				Yii::log('Patient details stale', 'trace');
				$pas_service->updatePatientFromPas($patient, $assignment);
			}
		} else {
			Yii::log('PAS is not available', 'trace');
			// @TODO Push an alert onto the user's screen
		}
	}

	/**
	 * Update GP from PAS
	 * @param array $params
	 */
	public function updateGpFromPas($params) {
		$pas_service = new PasService();
		if ($pas_service->available) {
			$gp = $params['gp'];
			$assignment = PasAssignment::model()->findByInternal('Gp', $gp->id);
			if(!$assignment) {
				Yii::log('Creating new Gp assignment', 'trace');
				// Assignment doesn't exist yet, try to find PAS gp using obj_prof
				$obj_prof = $gp->obj_prof;
				$pas_gp = PAS_Gp::model()->find('obj_prof = :obj_prof', array(
						':obj_prof' => $obj_prof,
				));
				if($pas_gp) {
					$assignment = new PasAssignment();
					$assignment->internal_id = $gp->id;
					$assignment->internal_type = 'Gp';
					$assignment->external_id = $pas_gp->OBJ_PROF;
					$assignment->external_type = 'PAS_Gp';
				} else {
					throw new CException('Cannot map gp');
					// @TODO Push an alert that the patient cannot be mapped
				}
			}
			if($assignment->isStale()) {
				Yii::log('Gp details stale', 'trace');
				$pas_service->updateGpFromPas($gp, $assignment);
			}
		} else {
			Yii::log('PAS is not available', 'trace');
			// @TODO Push an alert onto the user's screen
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
			Yii::log('PAS is not available', 'trace');
			// @TODO Push an alert onto the user's screen
		}
	}

	/**
	 * Fetch referral from PAS
	 * @param unknown_type $params
	 * @todo This method is currently disabled until the referral code is fixed
	 */
	public function fetchReferralFromPas($params) {
		return false;
		$pas_service = new PasService();
		if($pas_service->available) {
			$pas_service->fetchReferral($params['episode']);
		} else {
			Yii::log('PAS is not available', 'trace');
			// @TODO Push an alert onto the user's screen
		}
	}
	
}
