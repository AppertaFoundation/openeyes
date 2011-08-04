<?php

class LetterOutService
{
	public $patient;
	public $firm;
	public $substitutions;

	public function __construct($firm)
	{
		$this->firm = $firm;

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

	public function getLetterTemplates()
	{
		$letterTemplates = array();

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		foreach(LetterTemplate::model()->findAll(
			'specialty_id = ?' , $firm->serviceSpecialtyAssignment->specialty_id
		) as $letterTemplate) {
			$sendTo = Contact::model()->findByPk($letterTemplate['send_to']);
			$cc = Contact::model()->findByPk($letterTemplate['cc']);

			if (isset($sentTo->gp)) {
				$sendToKey = 'gp_' . $sendTo->id;
			} elseif (isset($cc->consultant)) {
				$sendToKey = 'c_' . $sendTo->id;
			} else {
				$sendToKey = $sendTo->id;
			}

			if (isset($cc->gp)) {
				$ccKey = 'gp_' . $cc->id;
			} elseif (isset($cc->consultant)) {
				$ccKey = 'c_' . $cc->id;
			} else {
				$ccKey = $cc->id;
			}

			$letterTemplates[$letterTemplate['id']] = array(
				'name' => $letterTemplate['name'],
				'phrase' => $this->applySubstitutions($letterTemplate['phrase']),
				'send_to' => $sendToKey,
				'cc' => $ccKey
			);
		}

		return $letterTemplates;
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

	/**
	 * Gets details for all the users associated with all the firms associated with the user
	 *
	 * N.B. ideally this would be by role - not all users are eligible to have correspondence sent from
	 *
	 * @return array
	 */
	public function getFromOptions()
	{
		$firmIds = array();
		$users = array();

		$user = User::model()->findByPk(Yii::app()->user->id);

		// Add this user
		$users[$user->title . ' ' . $user->first_name . ' ' . $user->last_name . ' ' . $user->qualifications . ', ' . $user->role] =
                                        $user->title . ' ' . $user->first_name . ' ' . $user->last_name;	

		// If the user has global firm rights they can see all firms and all users
		if ($user->global_firm_rights) {
			// @todo - is every user necessarilty associated with a firm? Does it matter?
			$allUsers = User::model()->findAll();

			foreach ($allUsers as $au) {
				$users[$au->title . ' ' . $au->first_name . ' ' . $au->last_name . ' ' . $au->qualifications . ', ' . $au->role] =
					$au->title . ' ' . $au->first_name . ' ' . $au->last_name;
			}
		} else {
			// @todo - turn this into a UNION or somesuch?
			$results = Yii::app()->db->createCommand()
				->select('f.id AS fid')
				->from('firm f')
				->join('firm_user_assignment f_u_a', 'f_u_a.firm_id = f.id')
				->where('f_u_a.user_id = :u_id', array(':u_id' => $user->id))
				->queryAll();

			foreach ($results as $result) {
				if (!in_array($result['fid'], $firmIds)) {
					$firmIds[] = $result['fid'];
				}
			}

			$results = Yii::app()->db->createCommand()
				->select('f.id AS fid')
				->from('firm f')
				->join('user_firm_rights u_f_r', 'u_f_r.firm_id = f.id')
				->where('u_f_r.user_id = :u_id', array(':u_id' => $user->id))
				->queryAll();

			foreach ($results as $result) {
				if (!in_array($result['fid'], $firmIds)) {
					$firmIds[] = $result['fid'];
				}
			}

			// Get all firms the user is directly associated with
			$results = Yii::app()->db->createCommand()
                                ->select('firm_id AS fid')
                                ->from('firm_user_assignment f_u_a')
                                ->where('f_u_a.user_id = :u_id', array(':u_id' => $user->id))
                                ->queryAll();

                        foreach ($results as $result) {
                                if (!in_array($result['fid'], $firmIds)) {
                                        $firmIds[] = $result['fid'];
                                }
                        }

			foreach ($firmIds as $firmId) {
				$results2 = Yii::app()->db->createCommand()
					->select('title, first_name, last_name, role, qualifications')
					->from('user')
					->join('firm_user_assignment f_u_a', 'f_u_a.user_id = user.id')
					->where('f_u_a.firm_id = :f_id AND f_u_a.user_id != :u_id', array(
						':f_id' => $firmId, ':u_id' =>Yii::app()->user->id)
					)
					->queryAll();

				foreach ($results2 as $result2) {
					$user[$results2['title'] . ' ' . $results2['first_name'] . ' ' . $results['last_name'] . ' ' . $result2['qualifications'] . ', ' . $result2['role']] = 
						$result2['title'] . ' ' . $result2['first_name'] . ' ' . $result2['last_name'];
				}
			}
		}

		return $users;
	}

	public function populateOptions(&$options, $results)
	{
		foreach ($results as $result) {
			$options[$this->applySubstitutions($result['phrase'])] = $this->stripNewlines($result['pn_name']);
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

	public function applySubstitutions($phrase)
	{
		if (empty($this->substitutions)) {
			$age = floor((time() - strtotime($this->patient->dob)) / 60 / 60 / 24 / 365);

			$this->substitutions['age'] = $age;

			if ($this->patient->gender == 'M') {
				$this->substitutions['obj'] = 'him';
				$this->substitutions['pos'] = 'his';
				$this->substitutions['pro'] = 'he';

// @todo - are minors under 16?
				if ($age < 16) {
					$this->substitutions['sub'] = 'boy';
				} else {
					$this->substitutions['sub'] = 'man';
				}
			} else {
				$this->substitutions['obj'] = 'her';
				$this->substitutions['pos'] = 'her';
				$this->substitutions['pro'] = 'she';

				if ($age < 16) {
					$this->substitutions['sub'] = 'girl';
				} else {
					$this->substitutions['sub'] = 'woman';
				}
			}

			// Find most recent episode, if any
			$episode = Episode::getCurrentEpisodeByFirm($this->patient->id, $this->firm);

			if (isset($episode)) {
				// Get most recent diagnosis for this patient
				// @todo - this method can be consolidated with the ElementDiagnosis->getNewestDiagnosis method
				$diagnosis = $episode->getPrincipalDiagnosis();

				$this->substitutions['epd'] = $diagnosis->disorder->term;
				$this->substitutions['eps'] = $diagnosis->getEyeText();

				// Get the most recent operation for this patient
				// @todo - complete after mergin branch with booking. Refer to email 'Correspondence questions'.
			}
		}

/*
adm - admission date - not sure yet how we get this one

FIELDS WE MIGHT BE ABLE TO DO:

opl - operations listed for - source unknown
*/

		foreach($this->substitutions as $key => $sub) {
			$phrase = preg_replace('/\[' . $key . '\]/', $sub, $phrase);

			$sub = ucfirst($sub);

			$phrase = preg_replace('/\[\^' . $key . '\]/', $sub, $phrase);
		}

		return $phrase;
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
