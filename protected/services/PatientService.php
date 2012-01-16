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

class PatientService
{
	public $patient;
	public $pasPatient;

	/**
	 * Create a new instance of the service
	 *
	 * @param model $patient			instance of the patient model
	 * @param model $pasPatient		instance of the PAS patient model
	 */
	public function __construct($patient = null, $pasPatient = null)
	{
		if (empty($patient)) {
			$this->patient = new Patient;
		} else {
			$this->patient = $patient;
		}
		if (empty($pasPatient)) {
			$this->pasPatient = new PAS_Patient;
		} else {
			$this->pasPatient = $pasPatient;
		}
	}

	/**
	 * Perform a search based on form $_POST data from the patient search page
	 * Search against PAS data and then import the data into OpenEyes database
	 *
	 * @param array $data
	 */
	public function search($data)
	{
		// oracle apparently doesn't do case-insensitivity, so everything is uppercase
		foreach ($data as $key => &$value) {
			$value = strtoupper($value);
		}

		$whereSql = '';

								// Hospital number
								if (!empty($data['hos_num'])) {
									$hosNum = preg_replace('/[^\d]/', '0', $data['hos_num']);
									$whereSql .= " AND n.num_id_type = substr('" . $hosNum . "',1,1) and n.number_id = substr('" . $hosNum . "',2,6)";
								}
								
								// Date of birth
								if (!empty($data['dob'])) {
									$whereSql .= " AND TO_CHAR(DATE_OF_BIRTH, 'YYYY-MM-DD') = '" . addslashes($data['dob']) . "'";
								}
								
								// Gender
								if (!empty($data['gender'])) {
									$whereSql .= " AND SEX = '" . addslashes($data['gender']) . "'";
								}
								
								// Name
								if (!empty($data['first_name']) && !empty($data['last_name'])) {
									$whereSql .= " AND p.RM_PATIENT_NO IN (SELECT RM_PATIENT_NO FROM SILVER.SURNAME_IDS WHERE Surname_Type = 'NO' AND ((Name1 = '" . addslashes($data['first_name'])
									. "' OR Name2 = '" . addslashes($data['first_name']) . "') AND Surname_ID = '" . addslashes($data['last_name']) . "'))";
								}
								
								// NHS Number
								if (!empty($data['nhs_num'])) {
									$whereSql .= " AND p.RM_PATIENT_NO IN (SELECT RM_PATIENT_NO FROM SILVER.NUMBER_IDS WHERE NUM_ID_TYPE = 'NHS' AND NUMBER_ID = '" . addslashes($data['nhs_num']) . "')";
								}
		
								$sql = "
												SELECT
													p.rm_patient_no,
													n.num_id_type,
													n.number_id,
													TO_CHAR(p.DATE_OF_BIRTH, 'YYYY-MM-DD') AS DATE_OF_BIRTH
												FROM
													SILVER.PATIENTS p,
													SILVER.SURNAME_IDS s,
													SILVER.NUMBER_IDS n
												WHERE
													s.rm_patient_no = p.rm_patient_no
													AND
													s.surname_type = 'NO'
													AND
													(
														n.rm_patient_no = p.rm_patient_no
														" . $whereSql . "
													)
								";

								$connection = Yii::app()->db_pas;
								$command = $connection->createCommand($sql);
								$results = $command->queryAll();

		$patients = array();
		$ids = array();

		foreach ($results as $result) {
			$pasPatient = PAS_Patient::model()->findByPk($result['RM_PATIENT_NO']);

			foreach ($connection->createCommand("select s.* from SILVER.PATIENTS p, SILVER.SURNAME_IDS s where p.RM_PATIENT_NO = '{$result['RM_PATIENT_NO']}' and s.surname_type = 'NO' and s.rm_patient_no = p.rm_patient_no")->queryAll() as $row) {
				$surname = PAS_PatientSurname::model();
				foreach ($row as $key => $value) {
					$surname->{$key} = $value;
				}
				break;
			}

			foreach ($connection->createCommand("select * from SILVER.PATIENT_ADDRS where RM_PATIENT_NO = $pasPatient->RM_PATIENT_NO order by date_end desc")->queryAll() as $row) {
				$address = PAS_PatientAddress::model();
				foreach ($row as $key => $value) {
					$address->{$key} = $value;
				}
				break;
			}

			if (isset($address)) {
				if ($patient = $this->updatePatient($pasPatient, $address, $result, $surname)) {
					$patients[] = $patient;
					$ids[] = $patient->hos_num;
				}
			}
		}

		// collect all the patients we just created
		$criteria = new CDbCriteria;
		$criteria->addInCondition('hos_num', $ids);

		return $criteria;
	}

	/**
	 * Format a number from PAS into a meaningful digit
	 *
	 * @param string $string
	 * @return string
	 * @deprecated
	 */
	public function formatHospitalNumberFromPas($string)
	{
		if (is_numeric($string) && $string < 1000000) {
			$number = str_pad($string, 7, '0', STR_PAD_LEFT);
		} else {
			$number = $string;
		}

		return $number;
	}

	/**
	 * Format a number for PAS with cropping
	 *
	 * @param string $string
	 * @return string
	 * @deprecated
	 */
	public function formatHospitalNumberForPas($string)
	{
		if (!is_numeric($string)) {
			$number = preg_replace("/[a-zA-Z]/", "", $string);
		} else {
			$number = sprintf("%'06u", $string % 1000000);
		}

		return $number;
	}

	/**
	 * Find an existing patient or create a new one
	 * Update its info with the latest info from PAS
	 *
	 * @param array $data		Data from PAS to store in the patient model
	 *
	 * @return Patient
	 */
	protected function updatePatient($patientData, $addressData, $result, $surname)
	{
		$hosNum = $result['NUM_ID_TYPE'] . $result['NUMBER_ID'];

		if (!ctype_digit($hosNum)) return false;

		// update OpenEyes database info
		$patient = Patient::model()->findByPk($patientData->RM_PATIENT_NO);
		$address = new Address;
		if (empty($patient)) {
			$patient = new Patient;
			$patient->id = $patientData->RM_PATIENT_NO;
		} elseif (!empty($patient->address_id)) {
			$address = Address::model()->findByPk($patient->address_id);

			// This was put here because the migration had some patient records with address_ids that
			// pointed to non-existent records.
			if (empty($address)) {
				$address = new Address;
				$address->id = $patient->address_id;
			}
		}
		$patient->pas_key		 = $hosNum;
		$patient->title			 = $surname->TITLE;
		$patient->first_name = $surname->NAME1;
		$patient->last_name  = $surname->SURNAME_ID;
//		$patient->dob				 = date('Y-m-d', strtotime(preg_replace('/(\d\d)$/', '19$1', $patientData->DATE_OF_BIRTH)));
		$patient->dob				 = $result['DATE_OF_BIRTH'];
		$patient->gender		 = $patientData->SEX;
		if ($addressData->TEL_NO != 'NONE') {
			$patient->primary_phone = $addressData->TEL_NO;
		}

		$address = $this->updateAddress($address, $addressData);
		if (!$address->save()) {
			throw new SystemException('Unable to update patient address: '.print_r($address->getErrors(),true));
		}

		$patient->address_id = $address->id;

/*
		$hospitalNumber = PAS_PatientNumber::model()->findByAttributes(
			array('RM_PATIENT_NO' => $patientData->RM_PATIENT_NO),
			'NUM_ID_TYPE != :numType',
			array(':numType' => 'NHS'));

		if (!empty($hospitalNumber)) {
			$patient->hos_num = $this->formatHospitalNumberFromPas($hospitalNumber->NUMBER_ID);
		}
*/

		if (preg_match('/^[0-9]+$/',$hosNum)) {
			$patient->hos_num = $hosNum;
		}

		$nhsNumber = PAS_PatientNumber::model()->findByAttributes(
			array('RM_PATIENT_NO' => $patientData->RM_PATIENT_NO,
					'NUM_ID_TYPE' => 'NHS'));
		if (!empty($nhsNumber)) {
			$patient->nhs_num = $nhsNumber->NUMBER_ID;
		}
		if (!$patient->save()) {
			throw new SystemException('Unable to update patient: '.print_r($patient->getErrors(),true));
		}

		// Pull in the GP associate from PAS if we don't already have it
		$patient->GetGP();

		return $patient;
	}

	/**
	 * Update address info with the latest info from PAS
	 *
	 * @param Address $address The patient address model to be updated
	 * @param PAS_PatientAddress $data Data from PAS to store in the patient address model
	 * @return Address
	 */
	protected function updateAddress($address, $data) {
		
		$propertyName = empty($data->PROPERTY_NAME) ? '' : trim($data->PROPERTY_NAME);
		$propertyNumber = empty($data->PROPERTY_NO) ? '' : trim($data->PROPERTY_NO);

		// Make sure they are not the same!
		if (strcasecmp($propertyName, $propertyNumber) == 0) {
			$propertyNumber = '';
		}

		// Address1 - Assume PAS ADDR1 is valid
		if (isset($data->ADDR1)) {
			$string = trim($data->ADDR1);

			// Remove any duplicate property name or number from ADDR1
			if (strlen($propertyName) > 0) {
				// Search plain, with comma, and with full stop
				$needles = array("{$propertyName},","{$propertyName}.",$propertyName);
				$string = trim(str_replace($needles, '', $string));
			}
			if (strlen($propertyNumber) > 0) {
				// Search plain, with comma, and with full stop
				$needles = array("{$propertyNumber},","{$propertyNumber}.",$propertyNumber);
				$string = trim(str_replace($needles, '', $string));
			}

			// Make sure street number has a comma and space after it
			$string = preg_replace('/([0-9]) /', '\1, ', $string);

			// Replace any full stops after street numbers with commas
			$string = preg_replace('/([0-9])\./', '\1,', $string);

			// That will probably do
			$address1 = '';
			if (!empty($propertyName)) {
				$address1 .= "{$propertyName}, ";
			}
			if (!empty($propertyNumber)) {
				$address1 .= "{$propertyNumber}, ";
			}
			$address1 .= $string;
		}

		// Create array of remaining address lines, from last to first
		$addressLines = array();
		if (!empty($data->POSTCODE)) {
			$addressLines[] = $data->POSTCODE;
		}
		if (!empty($data->ADDR5)) {
			$addressLines[] = $data->ADDR5;
		}
		if (!empty($data->ADDR4)) {
			$addressLines[] = $data->ADDR4;
		}
		if (!empty($data->ADDR3)) {
			$addressLines[] = $data->ADDR3;
		}
		if (!empty($data->ADDR2)) {
			$addressLines[] = $data->ADDR2;
		}

		// Instantiate a postcode utility object
		$postCodeUtility = new PostCodeUtility();

		// Set flags and default values
		$postCodeFound = false;
		$postCodeOuter = '';
		$townFound = false;
		$countyFound = false;
		$address2 = '';

		// Go through array looking for likely candidates for postcode, town/city and county
		for ($index = 0; $index < count($addressLines); $index++) {
			// Is element a postcode? (Postcodes may exist in other address lines)
			if ($postCodeArray = $postCodeUtility->parsePostCode($addressLines[$index])) {
				if (!$postCodeFound) {
					$postCodeFound = true;
					$postcode = $postCodeArray['full'];
					$postCodeOuter = $postCodeArray['outer'];
				}
			} else { // Otherwise a string
				// Last in (inverted array) is a non-postcode, non-city second address line
				if ($townFound) {
					$address2 = trim($addressLines[$index]);
				}

				// County?
				if (!$countyFound) {
					if ($postCodeUtility->isCounty($addressLines[$index])) {
						$countyFound = true;
						$county = trim($addressLines[$index]);
					}
				}

				// Town?
				if (!$townFound) {
					if ($postCodeUtility->isTown($addressLines[$index])) {
						$townFound = true;
						$town = trim($addressLines[$index]);
					}
				}
			}
		}

		// If no town or county found, get them from postcode data if available, otherwise fall back to best guess
		if ($postCodeFound) {
			if (!$countyFound) $county = $postCodeUtility->countyForOuterPostCode($postCodeOuter);
			if (!$townFound) $town = $postCodeUtility->townForOuterPostCode($postCodeOuter);
		} else {
			// Number of additional address lines
			$extraLines = count($addressLines) - 1;
			if ($extraLines > 1) {
				$county = trim($addressLines[0]);
				$town = trim($addressLines[1]);
			} elseif ($extraLines > 0) {
				$town = trim($addressLines[0]);
			}
		}

		// Store data
		$unitedKingdom = Country::model()->findByAttributes(array('name' => 'United Kingdom'));
		if (isset($address1)) {
			$address->address1 = $address1;
		}
		if (isset($address2)) {
			$address->address2 = $address2;
		}
		if (isset($town)) {
			$address->city = $town;
		}
		if (isset($county)) {
			$address->county = $county;
		}
		$address->country_id = $unitedKingdom->id;
		if (isset($postcode)) {
			$address->postcode = $postcode;
		}

		return $address;
	}
	
	/**
	 * Load data from PAS into existing Patient object and save
	 */
	public function loadFromPas() {
		Yii::log('Patient data stale, pulling from PAS:'.$this->patient->id);
		if($this->patient->id && $pas_patient = PAS_Patient::model()->findByPk($this->patient->id)) {
			if($hos_num = $pas_patient->hos_number) {
				// FIXME: When does pas_key ever differ from RM_PATIENT_NO ($this->patient->id)?
				$this->patient->pas_key = $hos_num->NUM_ID_TYPE . $hos_num->NUMBER_ID;
				$this->patient->hos_num = $hos_num->NUM_ID_TYPE . $hos_num->NUMBER_ID;
			}
			$this->patient->title = $pas_patient->name->TITLE;
			$this->patient->first_name = $pas_patient->name->NAME1;
			$this->patient->last_name = $pas_patient->name->SURNAME_ID;
			$this->patient->gender = $pas_patient->SEX;
			$this->patient->dob = date('Y-m-d',strtotime($pas_patient->DATE_OF_BIRTH));
			if($nhs_number = $pas_patient->nhs_number) {
				$this->patient->nhs_num = $nhs_number->NUMBER_ID;
			}
			if($pas_patient->address) {
				$this->patient->primary_phone = $pas_patient->address->TEL_NO;
				if(!$address = $this->patient->address) {
					$address = new Address();
				}
				$this->updateAddress($address, $pas_patient->address);
				$this->patient->address_id = $address->id;
				$address->save();
			}
			//$this->patient->gp_id
			$this->patient->save();
		} else {
			throw CException('Patient not found: '.$this->patient->id);
		}
	}
	
}
