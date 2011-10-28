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
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class PatientService
{
	public $patient;
	public $pasPatient;

	/**
	 * Create a new instance of the service
	 *
	 * @param model $patient      instance of the patient model
	 * @param model $pasPatient   instance of the PAS patient model
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

                if (!empty($data['hos_num'])) {
			$hosNum = preg_replace('/[^\d]/', '0', $data['hos_num']);
			//$hosNum = $this->formatHospitalNumberForPas($data['hos_num']);
			$whereSql .= " AND n.num_id_type = substr('" . $hosNum . "',1,1) and n.number_id = substr('" . $hosNum . "',2,6)";
                }
                if (!empty($data['dob'])) {
                        $whereSql .= " AND TO_CHAR(DATE_OF_BIRTH, 'YYYY-MM-DD') = '" . addslashes($data['dob']) . "'";
                }
                if (!empty($data['first_name']) && !empty($data['last_name'])) {
			$whereSql .= " AND p.RM_PATIENT_NO IN (SELECT RM_PATIENT_NO FROM SILVER.SURNAME_IDS WHERE Surname_Type = 'NO' AND ((Name1 = '" . addslashes($data['first_name']) . "' OR Name2 = '" . addslashes($data['first_name']) . "') AND Surname_ID = '" . addslashes($data['last_name']) . "'))";
                }
                if (!empty($data['gender'])) {
                        $whereSql .= " AND SEX = '" . addslashes($data['gender']) . "'";
                }
                if (!empty($data['nhs_num'])) {
			$whereSql .= " AND p.RM_PATIENT_NO IN (SELECT RM_PATIENT_NO FROM SILVER.NUMBER_IDS WHERE NUM_ID_TYPE = 'NHS' AND NUMBER_ID = '" . addslashes($data['nhs_num']) . "')";
                }
		
                $sql = "
                        SELECT
                                p.rm_patient_no,
				n.num_id_type,
				n.number_id
                        FROM
                                PATIENTS p,
                                SURNAME_IDS s,
                                NUMBER_IDS n
                        WHERE
                                (
                                        s.rm_patient_no = p.rm_patient_no
                                AND
                                        s.surname_type = 'NO'
                                )
                        AND
                                (
                                        n.rm_patient_no = p.rm_patient_no
                                " . $whereSql . "
                                )
                ";

/*
		$sql = "
			SELECT
    				p.rm_patient_no,
    				p.sex,
    				TO_CHAR(p.DATE_OF_BIRTH, 'YYYY-MM-DD') AS DATE_OF_BIRTH,
    				s.*,
    				n.*
			FROM
    				PATIENTS p,
    				SURNAME_IDS s,
    				NUMBER_IDS n
			WHERE
    				(
					s.rm_patient_no = p.rm_patient_no
				AND
					s.surname_type = 'NO'
				)
			AND
				(
					n.rm_patient_no = p.rm_patient_no
				" . $whereSql . "
				)
		";
*/
                $connection = Yii::app()->db_pas;
                $command = $connection->createCommand($sql);
                $results = $command->queryAll();

		$patients = array();
		$ids = array();

		foreach ($results as $result) {
			$pasPatient = PAS_Patient::model()->findByPk($result['RM_PATIENT_NO']);

			$address = PAS_PatientAddress::model()->findByPk($pasPatient->RM_PATIENT_NO);

			if (isset($address)) {
				$patient = $this->updatePatient($pasPatient, $address, $result);
				$patients[] = $patient;
				$ids[] = $patient->pas_key;
			}
		}

		// collect all the patients we just created
		$criteria = new CDbCriteria;
		$criteria->addInCondition('pas_key', $ids);

		return $criteria;
	}

	/**
	 * Format a number from PAS into a meaningful digit
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function formatHospitalNumberFromPas($string)
	{
		if (is_numeric($string) && $string < 1000000) {
//			$number = $string + 1000000;
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
	 *
	 * @return string
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
	 * @param array $data   Data from PAS to store in the patient model
	 *
	 * @return Patient
	 */
	protected function updatePatient($patientData, $addressData, $result)
	{
		$hosNum = $result['NUM_ID_TYPE'] . $result['NUMBER_ID'];

		// update OpenEyes database info
//		$patient = Patient::model()->findByAttributes(array('pas_key' => $patientData->RM_PATIENT_NO));
		$patient = Patient::model()->findByPk($patientData->RM_PATIENT_NO);
		$address = new Address;
		if (empty($patient)) {
			$patient = new Patient;
		} elseif (!empty($patient->address_id)) {
			$address = Address::model()->findByPk($patient->address_id);
		}
		$patient->pas_key    = $hosNum;
		$patient->title      = $patientData->names[0]->TITLE;
		$patient->first_name = $patientData->names[0]->NAME1;
		$patient->last_name  = $patientData->names[0]->SURNAME_ID;
		$patient->dob        = date('Y-m-d', strtotime(preg_replace('/(\d\d)$/', '19$1', $patientData->DATE_OF_BIRTH)));
		$patient->gender     = $patientData->SEX;
		if ($addressData->TEL_NO != 'NONE') {
			$patient->primary_phone = $addressData->TEL_NO;
		}

		$address = $this->updateAddress($address, $addressData);
		$address->save();
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

		$patient->hos_num = $hosNum;
		$nhsNumber = PAS_PatientNumber::model()->findByAttributes(
			array('RM_PATIENT_NO' => $patientData->RM_PATIENT_NO,
				  'NUM_ID_TYPE' => 'NHS'));
		if (!empty($nhsNumber)) {
			$patient->nhs_num = $nhsNumber->NUMBER_ID;
		}
		$patient->save();

		return $patient;
	}

	/**
	 * Update address info with the latest info from PAS
	 *
	 * @param object $address  the PAS_PatientAddress model to be updated
	 * @param array  $data     Data from PAS to store in the patient model
	 *
	 * @return Address
	 */
	protected function updateAddress($address, $data)
	{
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

			// Expand short address terms (eg CRES -> CRESCENT)
			$string = $this->expandShortTerms($string);

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
		# $address->county = $county;
		$address->country_id = $unitedKingdom->id;
		# $address->postcode = $postcode;

		return $address;
	}

	/**
	 * Import from Bill's original code
	 * Expands short terms such as 'Cres'
	 *
	 * @param string $_addr Address
	 *
	 * @return string
	 */
	private function expandShortTerms($string)
	{
		$addr = str_replace('Cres', 'Crescent', $string);

		return $addr;
	}
}
