<?php
class LetterOutService
{
	public $patient;

	public function __construct()
	{
		if (isset(Yii::app()->session['patient_id'])) {
			$this->patient = Patient::model()->findByPk(Yii::app()->session['patient_id']);
		} else {
			throw new Exception('No patient id in session.');
		}
	}

	/** 
	 * Get the default text for the 'Re:' field when they are creating a new letterout.
	 *
	 * @return string
	 */
	public function getDefaultRe()
	{
		$re = $this->patient->first_name . ' ' . $this->patient->last_name . ', ';

		$re .= $this->generateAddress($this->patient->address) . ', ';

		$re .= 'DofB: ' . $this->patient->dob . ', ';
		$re .= 'HosNum: ' . $this->patient->hos_num;

		return $re;
	}

	public function getAllPhraseOptions()
	{
		$allPhraseOptions = array();

		foreach (array(
			'Introduction', 'Findings', 'Diagnosis', 'Management', 'Drugs', 'Outcome'
		) as $phrase) {
			$allPhraseOptions[$phrase] = $this->getPhraseOptions($phrase);
		}

		return $allPhraseOptions;
	}

	public function getPhraseOptions($phrase)
	{
		$options = array();

		$firmId = $this->getFirmId();

		$firm = Firm::model()->findByPk($firmId);

                $results = Yii::app()->db->createCommand()
                        ->select('phrase, pn.name AS pn_name')
                        ->from('phrase')
                        ->join('phrase_name pn', 'phrase.phrase_name_id = pn.id')
			->join('section s', 'phrase.section_id = s.id')
			->where('s.name = :s_name AND section_type_id = 1', array(':s_name' => $phrase))
                        ->order('pn.name')
                        ->queryAll();

		$this->populateOptions($options, $results);

                $results = Yii::app()->db->createCommand()
                        ->select('phrase, pn.name AS pn_name')
                        ->from('phrase_by_specialty p_b_s')
                        ->join('phrase_name pn', 'p_b_s.phrase_name_id = pn.id')
                        ->join('section s', 'p_b_s.section_id = s.id')
                        ->where('s.name = :s_name AND section_type_id = 1 AND specialty_id = :s_id', array(
				':s_name' => $phrase,
				':s_id' => $firm->serviceSpecialtyAssignment->specialty_id
			))
                        ->order('pn.name')
                        ->queryAll();

		$this->populateOptions($options, $results);

                $results = Yii::app()->db->createCommand()
                        ->select('phrase, pn.name AS pn_name')
                        ->from('phrase_by_firm p_b_f')
                        ->join('phrase_name pn', 'p_b_f.phrase_name_id = pn.id')
                        ->join('section s', 'p_b_f.section_id = s.id')
                        ->where('s.name = :s_name AND section_type_id = 1 AND firm_id = :f_id', array(
                                ':s_name' => $phrase,
                                ':f_id' => $firmId
                        ))
                        ->order('pn.name')
                        ->queryAll();

                $this->populateOptions($options, $results);

		return $options;
	}

	public function populateOptions(&$options, $results)
	{
		foreach ($results as $result) {
			$options[$result['phrase']] = $this->stripNewlines($result['pn_name']);
		}
	}

	public function generateAddress($address)
	{
		$output = array();

		foreach (array('address1', 'address2', 'city', 'county', 'postcode') as $field) {
			if (!empty($address->$field)) {
				$output[] = $address->$field;
			}
		}

		return implode(', ', $output);
	}

	/**
	 * Get an array of names and nicknames so javascript can populat the 'dear' field
	 *
	 * @return array
	 */
// @todo - consolidate with getPatientContactOptions
	public function getContactData()
	{
		$contactData = array(
			'p_' . $this->patient->id => array(
				'address' => $this->stripNewlines($this->generateAddress($this->patient->address)),
				'full_name' => $this->stripNewlines($this->patient->title . ' ' . $this->patient->first_name . ' ' . $this->patient->last_name),
				'dear_name' => $this->stripNewlines($this->patient->title . ' ' . $this->patient->last_name),
				'nickname' => $this->stripNewlines($this->patient->first_name . ' ' . $this->patient->last_name),
				'identifier' => $this->stripNewlines($this->patient->first_name . ' ' . $this->patient->last_name . ' (Patient)')
			)
		);

		$contacts = $this->patient->contacts;

		foreach($contacts as $contact) {
			$identifier = $this->stripNewlines($contact->first_name . ' ' . $contact->last_name);

			if (isset($contact->gp)) {
				$key = 'gp_' . $contact->id;
				$identifier .= ' (GP)';
			} elseif (isset($contact->consultant)) {
				$key = 'c_' . $contact->id;
				$identifier .= ' (Consultant)';
			} else {
				$key = $contact->id;
			}

			$contactData[$key] = array(
				'address' => $this->stripNewlines($this->generateAddress($contact->address)),
				'full_name' => $this->stripNewlines($contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name),
				'dear_name' => $this->stripNewlines($contact->title . ' ' . $contact->last_name),
				'nick_name' => $this->stripNewlines($contact->nick_name),
				'identifier' => $this->stripNewlines($identifier)
			);
		}

		return $contactData;
	}

	public function getFirmId()
	{
		return Yii::app()->session['selected_firm_id'];
	}

	public function stripNewlines($text)
	{
		return preg_replace("/\n/", "\\n", $text);
	}
}
