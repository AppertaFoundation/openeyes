<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class PasService {

	public $available = true;

	public function __construct() {
		$this->available = $this->isAvailable();
	}

	/**
	 * Is PAS enabled and up?
	 */
	public function isAvailable() {
		if(isset(Yii::app()->params['mehpas_enabled']) && Yii::app()->params['mehpas_enabled'] === true) {
			try {
				$connection = Yii::app()->db_pas;
			} catch (Exception $e) {
				//Yii::log('PAS is not available: '.$e->getMessage());
				return false;
			}
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Check to see if a GP ID (obj_prof) is on our block list
	 * @param string $gp_id
	 * @return boolean
	 */
	protected function isBadGp($gp_id) {
		return (in_array($gp_id, Yii::app()->params['mehpas_bad_gps']));
	}

	/**
	 * Update Gp from PAS
	 * @param Gp $gp
	 */
	public function updateGpFromPas($gp, $assignment) {
		Yii::log('Pulling data from PAS for gp ID:'.$gp->id, 'trace');
		if(!$assignment->external_id) {
			// Without an external ID we have no way of looking up the gp in PAS
			throw new CException('GP assignment has no external ID');
		}
		if($pas_gp = $assignment->external) {
			Yii::log('Found GP in PAS obj_prof:'.$pas_gp->OBJ_PROF, 'trace');
			$gp->nat_id = $pas_gp->NAT_ID;
				
			// Contact
			if(!$contact = $gp->contact) {
				$contact = new Contact();
			}
			$contact->first_name = trim($pas_gp->FN1 . ' ' . $pas_gp->FN2);
			$contact->last_name = $pas_gp->SN;
			$contact->title = $pas_gp->TITLE;
			$contact->primary_phone = $pas_gp->TEL_1;
				
			// Address
			if(!$address = $contact->address) {
				$address = new Address();
				$address->parent_class = 'Contact';
			}
			$address->address1 = trim($pas_gp->ADD_NAM . ' ' . $pas_gp->ADD_NUM . ' ' . $pas_gp->ADD_ST);
			$address->address2 = $pas_gp->ADD_DIS;
			$address->city = $pas_gp->ADD_TWN;
			$address->county = $pas_gp->ADD_CTY;
			$address->postcode = $pas_gp->PC;
			$address->country_id = 1;
				
			// Save
			$contact->save();
			$address->parent_id = $contact->id;
			$address->save();
			$gp->contact_id = $contact->id;
			$gp->save();
			$assignment->internal_id = $gp->id;
			$assignment->save();

		} else {
			Yii::log('GP not found in PAS: '.$gp->id, 'info');
		}
	}

	/**
	 * Update patient from PAS
	 * @param Patient $patient
	 * @param PasPatientAssignment $assignment
	 */
	public function updatePatientFromPas($patient, $assignment) {
		Yii::log('Pulling data from PAS for patient ID:'.$patient->id, 'trace');
		if(!$assignment->external_id) {
			// Without an external ID we have no way of looking up the patient in PAS
			throw new CException('Patient assignment has no external ID');
		}
		if($pas_patient = $assignment->external) {
			Yii::log('Found patient in PAS rm_patient_no:'.$pas_patient->RM_PATIENT_NO, 'trace');
			$patient_attrs = array(
					'title' => $pas_patient->name->TITLE,
					'first_name' => ($pas_patient->name->NAME1) ? $pas_patient->name->NAME1 : '(UNKNOWN)',
					'last_name'=> $pas_patient->name->SURNAME_ID,
					'gender' =>$pas_patient->SEX,
					'dob' => $pas_patient->DATE_OF_BIRTH,
					'date_of_death' => $pas_patient->DATE_OF_DEATH,
			);
			if($hos_num = $pas_patient->hos_number) {
				$hos_num = $hos_num->NUM_ID_TYPE . $hos_num->NUMBER_ID;
				$patient_attrs['pas_key'] = $hos_num;
				$patient_attrs['hos_num'] = $hos_num;
			}
			if($nhs_number = $pas_patient->nhs_number) {
				$patient_attrs['nhs_num'] = $nhs_number->NUMBER_ID;
			}
			
			// Get primary phone from patient's main address
			if($pas_patient->address) {
				$patient_attrs['primary_phone'] = $pas_patient->address->TEL_NO;
			}
			
			$patient->attributes = $patient_attrs;
				
			// Get latest GP mapping from PAS
			$pas_patient_gp = $pas_patient->PatientGp;
			if($pas_patient_gp) {
				// Check that GP is not on our block list
				if($this->isBadGp($pas_patient_gp->GP_ID)) {
					Yii::log('GP on blocklist, ignoring: '.$pas_patient_gp->GP_ID, 'trace');
					$patient->gp_id = null;
				} else {
					Yii::log('Checking if GP is in openeyes: '.$pas_patient_gp->GP_ID, 'trace');
					// Check that the GP is in openeyes
					$gp = Gp::model()->findByAttributes(array('obj_prof' => $pas_patient_gp->GP_ID));
					if(!$gp) {
						// GP not in openeyes, pulling from PAS
						Yii::log('GP not in openeyes: '.$pas_patient_gp->GP_ID, 'trace');
						$gp = new Gp();
						$gp_assignment = new PasAssignment();
						$gp_assignment->internal_type = 'Gp';
						$gp_assignment->external_id = $pas_patient_gp->GP_ID;
						$gp_assignment->external_type = 'PAS_Gp';
						$this->updateGpFromPas($gp, $gp_assignment);
					}

					// Update/set patient's GP
					if(!$patient->gp || $patient->gp_id != $gp->id) {
						Yii::log('Patient\'s GP changed:'.$gp->obj_prof, 'trace');
						$patient->gp_id = $gp->id;
					} else {
						Yii::log('Patient\'s GP has not changed', 'trace');
					}
				}
			} else {
				Yii::log('Patient has no GP in PAS', 'info');
			}

			// Save
			$patient->save();
			$assignment->internal_id = $patient->id;
			$assignment->save();

			// Addresses
			if($pas_patient->addresses) {

				// Matching addresses for update is tricky cos we don't have a primary key on the pas address table,
				// so we need to keep track of patient address ids as we go
				$matched_address_ids = array();
				foreach($pas_patient->addresses as $pas_address) {

					// Match an address
					Yii::log("looking for patient address:".$pas_address->POSTCODE, 'trace');
					$matched_clause = ($matched_address_ids) ? ' AND id NOT IN ('.implode(',',$matched_address_ids).')' : '';
					$address = Address::model()->find(array(
							'condition' => "parent_id = :patient_id AND parent_class = 'Patient' AND REPLACE(postcode,' ','') = :postcode" . $matched_clause,
							'params' => array(':patient_id' => $patient->id, ':postcode' => str_replace(' ','',$pas_address->POSTCODE)),
					));

					// Check if we have an address (that we haven't already matched)
					if(!$address) {
						Yii::log("patient address not found, creating", 'trace');
						$address = new Address;
						$address->parent_id = $patient->id;
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
						'params' => array(':patient_id' => $patient->id),
				));
				Yii::log("$orphaned_addresses orphaned patient addresses deleted", 'trace');

			}

		} else {
			Yii::log('Patient not found in PAS: '.$patient->id, 'info');
		}
	}

	/**
	 * Perform a search based on form $_POST data from the patient search page
	 * Search against PAS data and then import the data into OpenEyes database
	 * @param array $data
	 * @param integer $num_results
	 * @param integer $page
	 */
	public function search($data, $num_results = 20, $page = 1) {
		Yii::log('Searching PAS', 'trace');

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

		// Name
		if (!empty($data['first_name']) && !empty($data['last_name'])) {
			$whereSql .= " AND p.RM_PATIENT_NO IN (SELECT RM_PATIENT_NO FROM SILVER.SURNAME_IDS WHERE Surname_Type = 'NO' AND ((Name1 = '" . addslashes($data['first_name'])
			. "' OR Name2 = '" . addslashes($data['first_name']) . "') AND Surname_ID = '" . addslashes($data['last_name']) . "'))";
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
		switch ($data['sortBy']) {
			case 'HOS_NUM*1':
				// hos_num
				$sort_by = "n.NUM_ID_TYPE||n.NUMBER_ID";
				break;
			case 'TITLE':
				// title
				$sort_by = "s.TITLE";
				break;
			case 'FIRST_NAME':
				// first_name
				$sort_by = "s.NAME1";
				break;
			case 'LAST_NAME':
				// last_name
				$sort_by = "s.SURNAME_ID";
				break;
			case 'DOB':
				// date of birth
				$sort_by = "p.DATE_OF_BIRTH";
				break;
			case 'GENDER':
				// gender
				$sort_by = "p.SEX";
				break;
			case 'NHS_NUM*1':
				// nhs_num
				$sort_by = "NHS_NUMBER";
				break;
		}

		$sort_dir = ($data['sortDir'] == 'asc' ? 'ASC' : 'DESC');
		$sort_rev = ($data['sortDir'] == 'asc' ? 'DESC' : 'ASC');

		$sql = "
		SELECT * from
		( select a.*, rownum rnum from (
		SELECT p.RM_PATIENT_NO, n.NUM_ID_TYPE, n.NUMBER_ID
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

			$hos_num = $result['NUM_ID_TYPE'] . $result['NUMBER_ID'];
				
			// See if the patient is in openeyes, if not then fetch from PAS
			if($assignment = PasAssignment::model()->findByExternal('PAS_Patient', $result['RM_PATIENT_NO'])) {
				// Patient is in OpenEyes and has an existing assignment
				$patient = $assignment->internal;
				if($assignment->isStale()) {
					$this->updatePatientFromPas($patient, $assignment);
				}
			} else if($patient = Patient::model()->findByAttributes(array('hos_num' => $hos_num))) {
				// Patient is in OpenEyes, but doesn't have an assignment
				// FIXME: Ideally this step should not be necessary, and could be removed if we prefill the assignment table when the module is setup
				$assignment = new PasAssignment();
				$assignment->external_id = $result['RM_PATIENT_NO'];
				$assignment->external_type = 'PAS_Patient';
				$this->updatePatientFromPas($patient, $assignment);
			} else {
				// Patient is not in OpenEyes
				$patient = new Patient();
				$assignment = new PasAssignment();
				$assignment->external_id = $result['RM_PATIENT_NO'];
				$assignment->external_type = 'PAS_Patient';
				$this->updatePatientFromPas($patient, $assignment);
			}

			// Check that patient has an address
			if($patient->address) {
				$ids[] = $patient->id;
			} else {
				$patients_with_no_address++;
			}

		}

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
	 * @param Address $address The patient address model to be updated
	 * @param PAS_PatientAddress $data Data from PAS to store in the patient address model
	 */
	protected function updateAddress($address, $data) {

		$address1 = '';
		$address2 = '';
		$city = '';
		$county = '';
		$postcode = '';

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

	}

}