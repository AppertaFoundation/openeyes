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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

class GpService {
	
	public $gp;
	public $pas_gp;
	
	/**
	 * Create a new instance of the service
	 *
	 * @param model $gp Instance of the gp model
	 * @param model $pas_gp Instance of the PAS gp model
	 */
	public function __construct($gp = null, $pas_gp = null) {
		if (empty($gp)) {
			$this->gp = new Gp();
		} else {
			$this->gp = $gp;
		}
		if (empty($pas_gp)) {
			$this->pas_gp = new PAS_Gp();
		} else {
			$this->pas_gp = $pas_gp;
		}
	}
	
	/**
	 * Get all the GPs from PAS and either insert or update them in the OE db
	 *
	 * @param int $pasKey
	 */
	public function populateGps()
	{
		// collect patient ids with no gp
		$patient_ids = array();
		$n=0;
		foreach (Yii::app()->db->createCommand()->select()->from('patient')->queryAll() as $patient) {
			$patient_ids[$n][] = $patient['id'];

			// pas parameter limit is 1000
			if (count($patient_ids[$n]) == 1000) $n++;
		}

		$errors = array();

		// get last gp date from past table for all these patients
		//select distinct rm_patient_no as patient_id, max(date_from) as latestGP from silver.patient_gps where rm_patient_no in (16218,16219) group by rm_patient_no order by rm_patient_no
		foreach ($patient_ids as $ids) {
			$errors = array_merge($errors, $this->GetPatientGp($ids,true));
		}
		echo "\n";

		$msg = '';

		if (!empty($errors)) {
			$msg = implode("\n",$errors)."\n";
		}

		$patients_with_null_gp = array();
		foreach (Yii::app()->db->createCommand()->select()->from('patient')->where("gp_id is null")->queryAll() as $patient) {
			$patients_with_null_gp[] = $patient;
		}

		if (count($patients_with_null_gp) >0) {
			$msg .= count($patients_with_null_gp)." patient(s) have a null gp_id:\n";
			foreach ($patients_with_null_gp as $patient) {
				$msg .= " - {$patient['first_name']} {$patient['last_name']} (pas_key={$patient['pas_key']}, hos_num={$patient['hos_num']})\n";
			}
		}

		if (strlen($msg) >0) {
			$hostname = trim(`/bin/hostname`);
			mail(Yii::app()->params['alerts_email'],"[$hostname] FetchGP errors",$msg);
		}
	}

	// Populate the GP for a given patient. $patient_id can also be an array of patient_ids (used by the PopulateGps method above to populate multiple patient GPs at once)
	public function GetPatientGp($patient_id, $verbose=false) {
		if (!is_array($patient_id)) {
			$patient_id = array($patient_id);
		}

		$bad_gps = Yii::app()->params['bad_gps'];

		$errors = array();
		foreach (Yii::app()->db_pas->createCommand("select distinct rm_patient_no as patient_id, max(date_from) as latestGP from silver.patient_gps where rm_patient_no in (".implode(',',$patient_id).") group by rm_patient_no order by rm_patient_no")->queryAll() as $latestGP) {
			$gp = Yii::app()->db_pas->createCommand("select * from silver.patient_gps where rm_patient_no = '{$latestGP['PATIENT_ID']}' and date_from = '{$latestGP['LATESTGP']}'")->queryRow();

			// Exclude bad GP data by obj_prof
			if (in_array($gp['GP_ID'],$bad_gps)) {
				$errors[] = "Rejected bad GP record: {$gp['GP_ID']}";
			} else {
				if ($pasGp = Yii::app()->db_pas->createCommand("select * from silver.ENV040_PROFDETS where obj_prof = '{$gp['GP_ID']}'")->queryRow()) {
					if ($gp = Gp::model()->find('obj_prof = ?', array($pasGp['OBJ_PROF']))) {
						// Update existing GP
						if ($contact = Contact::model()->findByPk($gp->contact_id)) {
							if (!$this->populateContact($contact, $pasGp)) {
								$errors[] = "Failed to populate contact for GP $gp->id: ".print_r($this->errors,true);
							}

							if ($address = Address::model()->findByPk($contact->address_id)) {
								if (!$this->populateAddress($address, $pasGp)) {
									$errors[] = "Failed to populate address for GP $gp->id: ".print_r($this->errors,true);
								}

								if (!$this->populateGp($gp, $pasGp)) {
									$errors[] = "Failed to populate GP $gp->id: ".print_r($this->errors,true);
								}
							} else {
								$errors[] = "No address for gp contact " . $contact->id;
								if ($verbose) echo "x";
							}
						} else {
							$errors[] = "Unable to update existing gp contact " . $pasGp['OBJ_PROF'];
							if ($verbose) echo "x";
						}
					} else {
						$address = new Address;

						if (!$this->populateAddress($address, $pasGp)) {
							$errors[] = "Unable to save new GP address: ".print_r($this->errors,true);
						}

						$contact = new Contact;

						$contact->consultant = 0;
						$contact->address_id = $address->id;

						if (!$this->populateContact($contact, $pasGp)) {
							$errors[] = "Unable to save new GP contact: ".print_r($this->errors,true);
						}

						$gp = new Gp;

						$gp->contact_id = $contact->id;

						if (!$this->populateGp($gp, $pasGp)) {
							$errors[] = "Unable to save new GP: ".print_r($this->errors,true);
						}
					}

					// Update patient
					if ($patient = Patient::model()->findByPk($latestGP['PATIENT_ID'])) {
						$patient->gp_id = $gp->id;
						if (!$patient->save()) {
							$errors[] = "Unable to save patient {$latestGP['PATIENT_ID']}: ".print_r($patient->getErrors(),true);
						}
						if ($verbose) echo ".";
					} else {
						$errors[] = "Unable to find patient {$latestGP['PATIENT_ID']}";
						if ($verbose) echo "x";
					}
				} else {
					$errors[] = "Unable to find GP for patient id={$latestGP['PATIENT_ID']}, GP_ID={$gp['GP_ID']}";
					if ($verbose) echo "x";
				}
			}
		}

		return $errors;
	}

	public function populateContact($contact, $pasGp)
	{
		$contact->title = $pasGp['TITLE'];
		$contact->first_name = $pasGp['FN1'] . ' ' . $pasGp['FN2'];
		$contact->last_name = $pasGp['SN'];
		$contact->primary_phone = $pasGp['TEL_1'];

		if (!$contact->save()) {
			$this->errors = $contact->getErrors();
			return false;
		}

		return true;
	}

	public function populateAddress($address, $pasGp)
	{
		$address->address1 = $pasGp['ADD_NAM'] . ' ' . $pasGp['ADD_NUM'] . ' ' . $pasGp['ADD_ST'];
		$address->address2 = $pasGp['ADD_TWN'] . ' ' . $pasGp['ADD_DIS'];
		$address->city = $pasGp['ADD_CTY'];
		$address->postcode = $pasGp['PC'];
		$address->country_id = 1;

		if (!$address->save()) {
			$this->errors = $address->getErrors();
			return false;
		}

		return true;
	}

	public function populateGp($gp, $pasGp)
	{
		$gp->obj_prof = $pasGp['OBJ_PROF'];
		$gp->nat_id = $pasGp['NAT_ID'];

		if (!$gp->save()) {
			$this->errors = $gp->getErrors();
			return false;
		}

		return true;
	}
	
	/**
	* Load data from PAS into existing GP object and save
	*/
	public function loadFromPas() {
		Yii::log('GP data stale, pulling from PAS:'.$this->gp->obj_prof);
		if($this->gp->obj_prof && $pas_gp = PAS_Gp::model()->findByPk($this->gp->obj_prof)) {
			$this->gp->nat_id = $pas_gp->NAT_ID;
			if(!$this->gp->contact) {
				$this->gp->contact = new Contact();
			}
			$this->gp->contact->first_name = trim($pas_gp->FN1 . ' ' . $pas_gp->FN2);
			$this->gp->contact->last_name = $pas_gp->SN;
			$this->gp->contact->title = $pas_gp->TITLE;
			$this->gp->contact->primary_phone = $pas_gp->TEL_1;
			if(!$this->gp->contact->address) {
				$this->gp->contact->address = new Address();
			}
			$this->gp->contact->address->address1 = trim($pas_gp->ADD_NAM . ' ' . $pas_gp->ADD_NUM . ' ' . $pas_gp->ADD_ST);
			$this->gp->contact->address->address2 = $pas_gp->ADD_TWN . ' ' . $pas_gp->ADD_DIS;
			$this->gp->contact->address->city = $pas_gp->ADD_TWN;
			$this->gp->contact->address->county = $pas_gp->ADD_CTY;
			$this->gp->contact->address->postcode = $pas_gp->PC;
			$this->gp->contact->address->country_id = 1;
			
			// Save
			$this->gp->contact->address->save();
			$this->gp->contact->save();
			$this->gp->save();
		} else {
			throw CException('GP not found: '.$this->gp->id);
		}
	}
	
}
