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
	public $down = false;

	/**
	 * Create a new instance of the service
	 *
	 * @param model $patient			instance of the patient model
	 * @param model $pasPatient		instance of the PAS patient model
	 */
	public function __construct($patient = null, $pasPatient = null)
	{
		try {
			$connection = Yii::app()->db_pas;
		} catch (Exception $e) {
			$this->down = true;
			Yii::app()->params['use_pas'] = false;
			Yii::app()->params['pas_down'] = true;
			return;
		}

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
	public function search($data, $num_results = 20, $page = 1) {
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
			$whereSql .= " AND DATE_OF_BIRTH = '" . addslashes($data['dob']) . "'";
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
			SELECT COUNT(*) as count
			FROM SILVER.PATIENTS p
			JOIN SILVER.SURNAME_IDS s ON s.rm_patient_no = p.rm_patient_no
			JOIN SILVER.NUMBER_IDS n ON n.rm_patient_no = p.rm_patient_no
			WHERE s.surname_type = 'NO' $whereSql
			AND LENGTH(TRIM(TRANSLATE(n.num_id_type, '0123456789', ' '))) is null
		";
		$connection = Yii::app()->db_pas;
		$command = $connection->createCommand($sql);
		foreach ($command->queryAll() as $results) $this->num_results = $results['COUNT'];

		$offset = (($page-1) * $num_results) + 1;
		$limit = $offset + $num_results - 1;

		switch ($data['sort_by']) {
			case 0:
				// hos_num
				$sort_by = "n.NUM_ID_TYPE||n.NUMBER_ID";
				break;
			case 1:
				// title
				$sort_by = "s.TITLE";
				break;
			case 2:
				// first_name
				$sort_by = "s.NAME1";
				break;
			case 3:
				// last_name
				$sort_by = "s.SURNAME_ID";
				break;
			case 4:
				// date of birth
				$sort_by = "p.DATE_OF_BIRTH";
				break;
			case 5:
				// gender
				$sort_by = "p.SEX";
				break;
			case 6:
				// nhs_num
				$sort_by = "NHS_NUMBER";
				break;
		}

		$sort_dir = ($data['sort_dir'] == 0 ? 'ASC' : 'DESC');
		$sort_rev = ($data['sort_dir'] == 0 ? 'DESC' : 'ASC');

		$sql = "
			SELECT * from
				( select a.*, rownum rnum from (
					SELECT p.RM_PATIENT_NO
					FROM SILVER.PATIENTS p
					JOIN SILVER.NUMBER_IDS n ON n.rm_patient_no = p.rm_patient_no
					JOIN SILVER.SURNAME_IDS s ON s.rm_patient_no = p.rm_patient_no
					LEFT OUTER JOIN SILVER.NUMBER_IDS n2 ON n2.rm_patient_no = p.rm_patient_no
						AND n2.NUM_ID_TYPE = 'NHS'
					WHERE ( s.surname_type = 'NO' $whereSql )
					AND LENGTH(TRIM(TRANSLATE(n.num_id_type, '0123456789', ' '))) is null
					ORDER BY $sort_by $sort_dir
				) a
				where rownum <= $limit
				order by rownum $sort_rev
				)
			where rnum >= $offset
			order by rnum $sort_rev
		";

		$connection = Yii::app()->db_pas;
		$command = $connection->createCommand($sql);
		$results = $command->queryAll();

		$ids = array();
		$patients_with_no_address = 0;

		foreach ($results as $result) {
			
			// Add patient to list of IDs

			// Check that patient has an address
			$pas_patient = PAS_Patient::model();
			$pas_patient->RM_PATIENT_NO = $result['RM_PATIENT_NO'];
			if($pas_patient->address) {
				$ids[] = $result['RM_PATIENT_NO'];
			} else {
				$patients_with_no_address++;
			}
			
		}
Yii::log(implode(',',$ids),'trace');
		switch ($_GET['sort_by']) {
			case 0:
				// hos_num
				$sort_by = "hos_num";
				break;
			case 1:
				// title
				$sort_by = "title";
				break;
			case 2:
				// first_name
				$sort_by = "first_name";
				break;
			case 3:
				// last_name
				$sort_by = "last_name";
				break;
			case 4:
				// date of birth
				$sort_by = "dob";
				break;
			case 5:
				// gender
				$sort_by = "gender";
				break;
			case 6:
				// nhs_num
				$sort_by = "nhs_num";
				break;
		}

		// collect all the patients we just created
		$criteria = new CDbCriteria;
		$criteria->addInCondition('id', $ids);
		$criteria->order = "$sort_by $sort_dir";

		if ($patients_with_no_address > 0) {
			$this->num_results -= $patients_with_no_address;
			$this->no_address = true;
		}

		return $criteria;
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
		
		// Dedupe
		if (isset($county) && isset($town) && $town == $county) {
			$county = '';
		}
		
		// Store data
		$address->address1 = $address1;
		$address->address2 = $address2;
		$address->city = $town;
		$address->county = $county;
		$unitedKingdom = Country::model()->findByAttributes(array('name' => 'United Kingdom'));
		$address->country_id = $unitedKingdom->id;
		$address->postcode = $postcode;
		$address->type = $data->ADDR_TYPE;
		$address->date_start = $data->DATE_START;
		$address->date_end = $data->DATE_END;

		return $address;
	}

	/**
	 * Load data from PAS into existing Patient object and save
	 */
	public function loadFromPas() {
		if(!$this->patient->id) {
			throw new CException('Patient not linked to PAS patient (id undefined)');
		}
		//Yii::log('Pulling Patient data from PAS:'.$this->patient->id, 'trace');
		if($pas_patient = PAS_Patient::model()->findByPk($this->patient->id)) {
			$this->patient->title = $pas_patient->name->TITLE;
			$this->patient->first_name = $pas_patient->name->NAME1;
			$this->patient->last_name = $pas_patient->name->SURNAME_ID;
			$this->patient->gender = $pas_patient->SEX;
			$this->patient->dob = $pas_patient->DATE_OF_BIRTH;
			if($hos_num = $pas_patient->hos_number) {
				$hos_num = $hos_num->NUM_ID_TYPE . $hos_num->NUMBER_ID;
				$this->patient->pas_key = $hos_num;
				$this->patient->hos_num = $hos_num;
			}
			if($nhs_number = $pas_patient->nhs_number) {
				$this->patient->nhs_num = $nhs_number->NUMBER_ID;
			}
				
			// Addresses
			if($pas_patient->addresses) {
				
				// Get primary phone from patient's main address
				$this->patient->primary_phone = $pas_patient->address->TEL_NO;
				
				// Matching addresses for update is tricky cos we don't have a primary key on the pas address table,
				// so we need to keep track of patient address ids as we go
				$matched_address_ids = array();
				$patient_addresses = $this->patient->addresses;
				foreach($pas_patient->addresses as $pas_address) {
					
					// Match an address
					//Yii::log("looking for patient address:".$pas_address->POSTCODE, 'trace');
					$address = Address::model()->find(array(
						'condition' => "parent_id = :patient_id AND parent_class = 'Patient' AND REPLACE(postcode,' ','') = :postcode",
						'params' => array(':patient_id' => $this->patient->id, ':postcode' => str_replace(' ','',$pas_address->POSTCODE)),
					));
					
					// Check if we have an address (that we haven't already matched)
					if(!$address || in_array($address->id, $matched_address_ids)) {
						Yii::log("patient address not found, creating", 'trace');
						$address = new Address;
						$address->parent_id = $this->patient->id;
						$address->parent_class = 'Patient';
					}
					
					$this->updateAddress($address, $pas_address);
					$address->save();
					$matched_address_ids[] = $address->id;
				}
				
				// Remove any orphaned addresses (expired?)
				$matched_string = implode(',',$matched_address_ids);
				$orphaned_addresses = Address::model()->deleteAll(array(
					'condition' => "parent_id = :patient_id AND parent_class = 'Patient' AND id NOT IN($matched_string)",
					'params' => array(':patient_id' => $this->patient->id),
				));
				//Yii::log("$orphaned_addresses orphaned patient addresses deleted", 'trace');
								
			}
				
			// Get latest GP mapping from PAS
			$pas_patient_gp = $pas_patient->PatientGp; 
			if($pas_patient_gp) {
				// Check that GP is not on our block list
				if(GpService::is_bad_gp($pas_patient_gp->GP_ID)) {
					//Yii::log('GP on blocklist, ignoring: '.$pas_patient_gp->GP_ID, 'trace');
					$this->patient->gp_id = null;
				} else {
					// Check that the GP is in openeyes
					$gp = Gp::model()->findByAttributes(array('obj_prof' => $pas_patient_gp->GP_ID));
					if(!$gp) {
						// GP not in openeyes, pulling from PAS
						//Yii::log('GP not in openeyes: '.$pas_patient_gp->GP_ID, 'trace');
						$gp = new Gp();
						$gp->obj_prof = $pas_patient_gp->GP_ID;
						$gp_service = new GpService($gp);
						$gp_service->loadFromPas();
						$gp = $gp_service->gp;
					}
	
					// Update/set patient's GP
					if(!$this->patient->gp || $this->patient->gp_id != $gp->id) {
						//Yii::log('Patient\'s GP changed:'.$gp->obj_prof, 'trace');
						$this->patient->gp_id = $gp->id;
					} else {
						//Yii::log('Patient\'s GP has not changed', 'trace');
					}
				}
			} else {
				//Yii::log('Patient has no GP in PAS', 'info');
			}
				
			// Save
			$this->patient->save();
				
		} else {
			//Yii::log('Patient not found in PAS: '.$this->patient->id, 'info');
		}
	}

}
