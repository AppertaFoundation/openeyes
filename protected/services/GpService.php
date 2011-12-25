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

class GpService
{
	/**
	 * Get all the GPs from PAS and either insert or update them in the OE db
	 *
	 * @param int $pasKey
	 */
	public function populateGps()
	{
		$contactType = ContactType::model()->find("name = 'GP'");

		if (!isset($contactType)) {
			exit("Unable to find GP contact type.\n");
		}

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
			foreach (Yii::app()->db_pas->createCommand("select distinct rm_patient_no as patient_id, max(date_from) as latestGP from silver.patient_gps where rm_patient_no in (".implode(',',$ids).") group by rm_patient_no order by rm_patient_no")->queryAll() as $latestGP) {
				$gp = Yii::app()->db_pas->createCommand("select * from silver.patient_gps where rm_patient_no = '{$latestGP['PATIENT_ID']}' and date_from = '{$latestGP['LATESTGP']}'")->queryRow();

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
								echo "x";
							}
						} else {
							$errors[] = "Unable to update existing gp contact " . $pasGp['OBJ_PROF'];
							echo "x";
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
						echo ".";
					} else {
						$errors[] = "Unable to find patient {$latestGP['PATIENT_ID']}";
						echo "x";
					}
				} else {
					$errors[] = "Unable to find GP for patient id={$latestGP['PATIENT_ID']}, GP_ID={$gp['GP_ID']}";
					echo "x";
				}
			}
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
				$msg .= " - $patient->first_name $patient->last_name (pas_key=$patient->pas_key, hos_num=$patient->hos_num)\n";
			}
		}

		if (strlen($msg) >0) {
			$hostname = trim(`/bin/hostname`);
			mail(Yii::app()->params['alerts_email'],"[$hostname] FetchGP errors",$msg);
		}
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
}
