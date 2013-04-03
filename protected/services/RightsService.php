<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
			->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
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
			->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
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
				if (!$ufr->save()) {
					throw new SystemException('Unable to save UserFirmRights: '.print_r($ufr->getErrors(),true));
				}
			}
		}

		UserServiceRights::model()->deleteAll('user_id = :user_id',
			array(':user_id' => $this->userId));

		if (!empty($_POST['Rights']['service'])) {
			foreach($_POST['Rights']['service'] as $id => $value) {
				$usr = new UserServiceRights;
				$usr->user_id = $this->userId;
				$usr->service_id = $id;
				if (!$usr->save()) {
					throw new SystemException('Unable to save UserServiceRights: '.print_r($usr->getErrors(),true));
				}
			}
		}

		return true;
	}
}
