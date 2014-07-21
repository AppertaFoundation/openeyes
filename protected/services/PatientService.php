<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace services;

class PatientService extends ModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
		'identifier' => self::TYPE_TOKEN,
		'family' => self::TYPE_STRING,
		'given' => self::TYPE_STRING,
	);

	static protected $primary_model = 'Patient';

	public function search(array $params)
	{
		$model = $this->getSearchModel();
		if (isset($params['id'])) $model->id = $params['id'];
		if (isset($params['identifier'])) {
			$model->hos_num = sprintf('%07s', $params['identifier']);
			$model->nhs_num = $params['identifier'];
		}

		$searchParams = array('pageSize' => 5);
		if (isset($params['family'])) $searchParams['last_name'] = $params['family'];
		if (isset($params['given'])) $searchParams['first_name'] = $params['given'];

		return $this->getResourcesFromDataProvider($model->search($searchParams));
	}

	public function modelToResource($patient)
	{
		$res = parent::modelToResource($patient);
		$res->nhs_num = $patient->nhs_num;
		$res->hos_num = $patient->hos_num;
		$res->title = $patient->contact->title;
		$res->family_name = $patient->contact->last_name;
		$res->given_name = $patient->contact->first_name;
		$res->gender = $patient->gender;
		$res->birth_date = $patient->dob;
		$res->date_of_death = $patient->date_of_death;
		$res->primary_phone = $patient->contact->primary_phone;
		$res->addresses = array_map(array('services\PatientAddress', 'fromModel'), $patient->contact->addresses);

		if ($patient->gp_id) $res->gp_ref = new InternalReference('Gp', $patient->gp_id);
		if ($patient->practice_id) $res->prac_ref = new InternalReference('Practice', $patient->practice_id);
		foreach ($patient->commissioningbodies as $cb) {
			$res->cb_refs[] = new InternalReference('CommissioningBody', $cb->id);
		}
		$res->care_providers = array_merge(array_filter(array($res->gp_ref, $res->prac_ref)), $res->cb_refs);

		return $res;
	}

	public function resourceToModel($res, $patient)
	{
		$patient->nhs_num = $res->nhs_num;
		$patient->hos_num = $res->hos_num;
		$patient->gender = $res->gender;
		$patient->dob = $res->birth_date;
		$patient->date_of_death = $res->date_of_death;
		$patient->gp_id = $res->gp_ref ? $res->gp_ref->getId() : null;
		$patient->practice_id = $res->prac_ref ? $res->prac_ref->getId() : null;
		$this->saveModel($patient);

		$contact = $patient->contact;
		$contact->title = $res->title;
		$contact->last_name = $res->family_name;
		$contact->first_name = $res->given_name;
		$contact->primary_phone = $res->primary_phone;
		$this->saveModel($contact);

		$cur_addrs = array();
		foreach ($contact->addresses as $addr) {
			$cur_addrs[$addr->id] = PatientAddress::fromModel($addr);
		}

		$add_addrs = array();
		$matched_ids = array();

		foreach ($res->addresses as $new_addr) {
			$found = false;
			foreach ($cur_addrs as $id => $cur_addr) {
				if ($cur_addr->isEqual($new_addr)) {
					$matched_ids[] = $id;
					$found = true;
					unset($cur_addrs[$id]);
					break;
				}
			}
			if (!$found) $add_addrs[] = $new_addr;
		}

		$crit = new \CDbCriteria;
		$crit->compare('contact_id', $contact->id)->addNotInCondition('id', $matched_ids);
		\Address::model()->deleteAll($crit);

		foreach ($add_addrs as $add_addr) {
			$addr = new \Address;
			$addr->contact_id = $contact->id;
			$add_addr->toModel($addr);
			$this->saveModel($addr);
		}

		$cur_cb_ids = array();
		foreach ($patient->commissioningbodies as $cb) {
			$cur_cb_ids[] = $cb->id;
		}

		$new_cb_ids = array();
		foreach ($res->cb_refs as $cb_ref) {
			$new_cb_ids[] = $cb_ref->getId();
		};

		$add_cb_ids = array_diff($new_cb_ids, $cur_cb_ids);
		$del_cb_ids = array_diff($cur_cb_ids, $new_cb_ids);

		foreach ($add_cb_ids as $cb_id) {
			$cba = new \CommissioningBodyPatientAssignment;
			$cba->commissioning_body_id = $cb_id;
			$cba->patient_id = $patient->id;
			$this->saveModel($cba);
		}

		if ($del_cb_ids) {
			$crit = new \CDbCriteria;
			$crit->compare('patient_id', $patient->id)->addInCondition('commissioning_body_id', $del_cb_ids);
			\CommissioningBodyPatientAssignment::model()->deleteAll($crit);
		}
	}
}
