<?php

class RightsService
{
        public $userId;

        /**
         * Create a new instance of the service
         *
         * @param model $userId
         */
        public function __construct($userId)
        {
        	$this->userId = $userId;
        }

	/**
	 * Perform a search based on the patient pas key
	 *
	 * @param int $pasKey
	 */
	public function loadRights()
	{
		$results = Yii::app()->db->createCommand()
			->select('id, name')
			->from('service')
			->order('id')
			->queryAll();

		$rights = array();

		foreach ($results as $result) {
			$rights[$result['id']] = array(
				'id' => $result['id'],
				'name' => $result['name'],
				'label' => 'Rights[service][' . $result['id'] . ']',
				'firms' => array(),
				'checked' => false
			);
		}

		$results = Yii::app()->db->createCommand()
			->select('service_id')
			->from('user_service_rights')
			->where('user_id = :u', array(':u' => $this->userId))
			->queryAll();

		foreach ($results as $result) {
			$rights[$result['service_id']]['checked'] = true;
		}

		$results = Yii::app()->db->createCommand()
			->select('f.id, name, service_id')
			->from('firm f')
			->join('service_specialty_assignment ssa', 'f.service_specialty_assignment_id = ssa.id')
			->order('ssa.id, f.name')
			->queryAll();

		foreach ($results as $result) {
			$rights[$result['service_id']]['firms'][$result['id']] = array(
				'id' => $result['id'],
				'name' => $result['name'],
				'label' => 'Rights[firm][' . $result['id'] . ']',
				'checked' => false
			);
		}

		$results = Yii::app()->db->createCommand()
			->select('f.id AS fid, service_id')
			->from('user_firm_rights ufr')
			->join('firm f', 'f.id = ufr.firm_id')
			->join('service_specialty_assignment ssa', 'f.service_specialty_assignment_id = ssa.id')
			->where('user_id = :u', array(':u' => $this->userId))
			->queryAll();

		foreach ($results as $result) {
			$rights[$result['service_id']]['firms'][$result['fid']]['checked'] = true;
		}

		return $rights;
	}

	public function saveRights()
	{
		UserFirmRights::model()->deleteAll('user_id = :user_id',
				array(':user_id' => $this->userId));

		if (!empty($_POST['Rights']['firm'])) {
			foreach($_POST['Rights']['firm'] as $id => $value) {
				$ufr = new UserFirmRights;
				$ufr->user_id = $this->userId;
				$ufr->firm_id = $id;
				$ufr->save();
			}
		}

		UserServiceRights::model()->deleteAll('user_id = :user_id',
			array(':user_id' => $this->userId));

		if (!empty($_POST['Rights']['service'])) {
			foreach($_POST['Rights']['service'] as $id => $value) {
				$usr = new UserServiceRights;
				$usr->user_id = $this->userId;
				$usr->service_id = $id;
				$usr->save();
			}
		}

		return true;
	}
}
