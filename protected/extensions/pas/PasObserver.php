<?php
class PasObserver {
	
	/**
	 * Update from PAS if available
	 */
	public function updateFromPas($params) {
		$patient = $params['patient'];
		if(PasPatientAssignment::isStale($patient->id)) {
			Yii::log('Patient details stale', 'trace');
			$patient_service = new PatientService($patient);
			if (!$patient_service->down) {
				$patient_service->loadFromPas();
			}
		}
	}
	
	public function searchPas($params) {
		$patient_service = new PatientService();
		if(!$patient_service->down) {
			$data = array();
			foreach(array('first_name','last_name','hos_num') as $param) {
				$data[$param] = $params['patient']->$param;
			}
			$data['order'] = $params['params']['order'];
			$data['sort_by'] = '';
			$data['sort_dir'] = '';
			$params['criteria'] = $patient_service->search($data, $params['params']['items_per_page'], $_GET['page_num']);
		}
	}
	
}
