<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
			'subspecialty_id = ?' , $firm->serviceSubspecialtyAssignment->subspecialty_id
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
			->from('phrase_by_subspecialty p_b_s')
			->join('phrase_name pn', 'p_b_s.phrase_name_id = pn.id')
			->join('section s', 'p_b_s.section_id = s.id')
			->where('s.name = :s_name AND section_type_id = 1 AND subspecialty_id = :s_id', array(
				':s_name' => $phrase,
				':s_id' => $firm->serviceSubspecialtyAssignment->subspecialty_id
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
	 * Gets the phrase for a particular name and section
	 */
	public function getPhrase($sectionName, $phraseName)
	{
		$name = PhraseName::model()->find('name = ?', array($phraseName));

		if (!isset($name)) {
			return 'NO NAME';
		}

		$section = Section::model()->find('section_type_id = 1 AND name = ?', array($sectionName));

		if (!isset($section)) {
			return 'NO SECTION';
		}

		$phrase = PhraseByFirm::model()->find('firm_id = ? AND phrase_name_id = ? AND section_id = ?', array(
			$this->firm->id, $name->id, $section->id
		));

		if (isset($phrase)) {
			return $phrase->phrase;
		}

		$phrase = PhraseBySubspecialty::model()->find('subspecialty_id = ? AND phrase_name_id = ? AND section_id = ?', array(
			$this->firm->serviceSubspecialtyAssignment->subspecialty_id, $name->id, $section->id
		));

		if (isset($phrase)) {
			return $phrase->phrase;
		}

		$phrase = Phrase::model()->find('phrase_name_id = ? AND section_id = ?', array(
			$name->id, $section->id
		));

		if (isset($phrase)) {
			return $phrase->phrase;
		}

		return 'NO DATA';
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
			$allUsers = User::model()->findAll();

			foreach ($allUsers as $au) {
				$users[$au->title . ' ' . $au->first_name . ' ' . $au->last_name . ' ' . $au->qualifications . ', ' . $au->role] =
					$au->title . ' ' . $au->first_name . ' ' . $au->last_name;
			}
		} else {
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
			$age = $this->patient->age;

			$this->substitutions['age'] = $age;

			if ($this->patient->gender == 'M') {
				$this->substitutions['obj'] = 'him';
				$this->substitutions['pos'] = 'his';
				$this->substitutions['pro'] = 'he';

				if ($this->patient->isChild()) {
					$this->substitutions['sub'] = 'boy';
				} else {
					$this->substitutions['sub'] = 'man';
				}
			} else {
				$this->substitutions['obj'] = 'her';
				$this->substitutions['pos'] = 'her';
				$this->substitutions['pro'] = 'she';

				if ($this->patient->isChild()) {
					$this->substitutions['sub'] = 'girl';
				} else {
					$this->substitutions['sub'] = 'woman';
				}
			}

			// Find most recent episode, if any
			$episode = Episode::getCurrentEpisodeByFirm($this->patient->id, $this->firm);

			$this->substitutions['epd'] = 'NO DATA';
			$this->substitutions['eps'] = 'NO DATA';

			if (isset($episode) && $episode) {
				if ($episode->diagnosis && $episode->eye) {
					$this->substitutions['epd'] = $episode->diagnosis->term;
					$this->substitutions['eps'] = $episode->eye->name;
				}
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
