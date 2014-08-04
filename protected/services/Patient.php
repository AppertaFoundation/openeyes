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

class Patient extends Resource
{
	static public function fromFhir($fhirObject)
	{
		$patient = parent::fromFhir($fhirObject);

		foreach ($patient->care_providers as $ref) {
			switch ($ref->getServiceName()) {
				case 'Gp':
					$patient->gp_ref = $ref;
					break;
				case 'Practice':
					$patient->prac_ref = $ref;
					break;
				case 'CommissioningBody':
					$patient->cb_refs[] = $ref;
					break;
			}
		}

		return $patient;
	}

	static public function getServiceClass($fhirType)
	{
		if ($fhirType == 'Address') {
			return 'services\PatientAddress';
		}
		return parent::getServiceClass($fhirType);
	}

	public $nhs_num;
	public $hos_num;

	public $title;
	public $family_name;
	public $given_name;

	public $gender;

	public $birth_date;
	public $date_of_death;

	public $primary_phone;
	public $addresses = array();

	public $care_providers = array();

	public $gp_ref = null;
	public $prac_ref = null;
	public $cb_refs = array();

	/**
	 * @return Gp|null
	 */
	public function getGp()
	{
		return $this->gp_ref ? $this->gp_ref->resolve() : null;
	}

	/**
	 * @return Practice|null
	 */
	public function getPractice()
	{
		return $this->prac_ref ? $this->prac_ref->resolve() : null;
	}

	/**
	 * @return CommissioningBody[]
	 */
	public function getCommissioningBodies()
	{
		$cbs = array();
		foreach ($this->cb_refs as $cb_ref) {
			$cbs[] = $cb_ref->resolve();
		}
		return $cbs;
	}
}
