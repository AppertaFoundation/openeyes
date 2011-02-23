<?php
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

	$params = array();
		$criteria=new CDbCriteria;
		$criteria->select = '"t".RM_PATIENT_NO, "t".SEX, TO_CHAR("t".DATE_OF_BIRTH, \'YYYY-MM-DD\') AS DATE_OF_BIRTH, SILVER.SURNAME_IDS.*, SILVER.NUMBER_IDS.*';
		$criteria->join = 'LEFT OUTER JOIN SILVER.SURNAME_IDS ON "t".RM_PATIENT_NO = SILVER.SURNAME_IDS.RM_PATIENT_NO LEFT OUTER JOIN SILVER.NUMBER_IDS ON ("t".RM_PATIENT_NO = SILVER.NUMBER_IDS.RM_PATIENT_NO)';
		if (!empty($data['dob'])) {
			$criteria->addCondition("TO_CHAR(DATE_OF_BIRTH, 'YYYY-MM-DD') = :dob");
			$params[':dob'] = $data['dob'];
		}
		if (!empty($data['first_name'])) {
			$criteria->addCondition("RM_PATIENT_NO IN (SELECT RM_PATIENT_NO FROM SILVER.SURNAME_IDS WHERE Surname_Type = :sn_type AND (Name1 LIKE :first_name OR Name2 LIKE :first_name))");
			$params[':first_name'] = "%{$data['first_name']}%";
		}
		if (!empty($data['last_name'])) {
			$criteria->addCondition("RM_PATIENT_NO IN (SELECT RM_PATIENT_NO FROM SILVER.SURNAME_IDS WHERE Surname_Type = :sn_type AND Surname_ID LIKE :last_name)");
			$params[':sn_type'] = 'NO';
			$params[':last_name'] = "%{$data['last_name']}%";
		}
		if (!empty($data['gender'])) {
			$criteria->compare('SEX',$data['gender']);
			$params[':ycp0'] = $data['gender'];
		}
		if (!empty($data['hos_num'])) {
			// add tweaks in for hospital number jiggering
			$criteria->addCondition("RM_PATIENT_NO IN
				(SELECT RM_PATIENT_NO FROM SILVER.NUMBER_IDS 
				WHERE NUM_ID_TYPE != :number_type AND NUMBER_ID = :hos_number)");
			$params[':number_type'] = 'NHS';
			$params[':hos_number'] = $this->formatHospitalNumberForPas($data['hos_num']);
		}
		if (!empty($data['nhs_num'])) {
			// add tweaks in for hospital number jiggering
			$criteria->addCondition("RM_PATIENT_NO IN
				(SELECT RM_PATIENT_NO FROM SILVER.NUMBER_IDS 
				WHERE NUM_ID_TYPE = :number_type AND
				NUMBER_ID = :nhs_number)");
			$params[':number_type'] = 'NHS';
			$params[':nhs_number'] = $data['nhs_num'];
		}

		$criteria->params = $params;
		$results = PAS_Patient::model()->findAll($criteria);
		$patients = array();
		$ids = array();

		if (!empty($results)) {
			foreach ($results as $pasPatient) {
				$patient = $this->updatePatient($pasPatient);
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
			$number = $string + 1000000;
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
	protected function updatePatient($data)
	{
		// update OpenEyes database info
		$patient = Patient::model()->findByAttributes(array('pas_key' => $data->RM_PATIENT_NO));
		if (empty($patient)) {
			$patient = new Patient;
		}
		$patient->pas_key    = $data->RM_PATIENT_NO;
		$patient->title      = $data->names[0]->TITLE;
		$patient->first_name = $data->names[0]->NAME1;
		$patient->last_name  = $data->names[0]->SURNAME_ID;
		$patient->dob        = $data->DATE_OF_BIRTH;
		$patient->gender     = $data->SEX;

		$hospitalNumber = PAS_PatientNumber::model()->findByAttributes(
			array('RM_PATIENT_NO' => $data->RM_PATIENT_NO),
			'NUM_ID_TYPE != :numType',
			array(':numType' => 'NHS'));
		if (!empty($hospitalNumber)) {
			$patient->hos_num = $this->formatHospitalNumberFromPas($hospitalNumber->NUMBER_ID);
		}
		$nhsNumber = PAS_PatientNumber::model()->findByAttributes(
			array('RM_PATIENT_NO' => $data->RM_PATIENT_NO,
				  'NUM_ID_TYPE' => 'NHS'));
		if (!empty($nhsNumber)) {
			$patient->nhs_num = $nhsNumber->NUMBER_ID;
		}
		$patient->save();

		return $patient;
	}
}
