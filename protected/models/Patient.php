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

/**
 * This is the model class for table "patient".
 *
 * The followings are the available columns in table 'patient':
 * @property integer $id
 * @property string  $pas_key
 * @property string  $title
 * @property string  $first_name
 * @property string  $last_name
 * @property string  $dob
 * @property string  $date_of_death
 * @property string  $gender
 * @property string  $hos_num
 * @property string  $nhs_num
 * @property string  $primary_phone
 * @property integer $gp_id
 * @property integer $practice_id
 * @property string  $created_date
 * @property string  $last_modified_date
 * @property integer $created_user_id
 * @property integer $last_modified_user_id
 * @property datetime $no_allergies_date
 *
 * The followings are the available model relations:
 * @property Episode[] $episodes
 * @property Address[] $addresses
 * @property Address $address Primary address
 * @property Contact[] $contactAssignments
 * @property Gp $gp
 * @property Practice $practice
 * @property Allergy[] $allergies
 * @property EthnicGroup $ethnic_group
 * @property CommissioningBody[] $commissioningbodies
 */
class Patient extends BaseActiveRecordVersioned
{
	const CHILD_AGE_LIMIT = 16;

	public $use_pas = TRUE;
	private $_orderedepisodes;

	/**
		* Returns the static model of the specified AR class.
		* @return Patient the static model class
		*/
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function behaviors()
	{
		return array(
			'ContactBehavior' => array(
				'class' => 'application.behaviors.ContactBehavior',
			),
		);
	}

	/**
	 * Suppress PAS integration
	 * @return Patient
	 */
	public function noPas()
	{
		// Clone to avoid singleton problems with use_pas flag
		$model = clone $this;
		$model->use_pas = FALSE;
		return $model;
	}

	/**
		* @return string the associated database table name
		*/
	public function tableName()
	{
		return 'patient';
	}

	/**
		* @return array validation rules for model attributes.
		*/
	public function rules()
	{
		return array(
			array('pas_key', 'length', 'max' => 10),
			array('hos_num, nhs_num', 'length', 'max' => 40),
			array('gender', 'length', 'max' => 1),
			array('dob, date_of_death, ethnic_group_id', 'safe'),
			array('dob, hos_num, nhs_num, date_of_death', 'safe', 'on' => 'search'),
		);
	}

	/**
	* @return array relational rules.
	*/
	public function relations()
	{
		return array(
			'legacyepisodes' => array(self::HAS_MANY, 'Episode', 'patient_id',
				'condition' => "legacy=1",
			),
			'supportserviceepisodes' => array(self::HAS_MANY, 'Episode', 'patient_id',
				'condition' => 'support_services=1',
			),
			'episodes' => array(self::HAS_MANY, 'Episode', 'patient_id',
				'condition' => "(patient_episode.legacy=0 or patient_episode.legacy is null)",
				'alias' => 'patient_episode'
			),
			'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
			'gp' => array(self::BELONGS_TO, 'Gp', 'gp_id'),
			'practice' => array(self::BELONGS_TO, 'Practice', 'practice_id'),
			'contactAssignments' => array(self::HAS_MANY, 'PatientContactAssignment', 'patient_id'),
			'allergies' => array(self::MANY_MANY, 'Allergy', 'patient_allergy_assignment(patient_id, allergy_id)',
				'alias' => 'patient_allergies',
				'order' => 'patient_allergies.name'),
			'allergyAssignments' => array(self::HAS_MANY, 'PatientAllergyAssignment', 'patient_id'),
			'secondarydiagnoses' => array(self::HAS_MANY, 'SecondaryDiagnosis', 'patient_id'),
			'ethnic_group' => array(self::BELONGS_TO, 'EthnicGroup', 'ethnic_group_id'),
			'previousOperations' => array(self::HAS_MANY, 'PreviousOperation', 'patient_id', 'order' => 'CASE WHEN Date IS NULL THEN 1 ELSE 0 END, Date'),
			'familyHistory' => array(self::HAS_MANY, 'FamilyHistory', 'patient_id', 'order' => 'created_date'),
			'medications' => array(self::HAS_MANY, 'Medication', 'patient_id', 'order' => 'created_date', 'condition' => 'end_date is null'),
			'previous_medications' => array(self::HAS_MANY, 'Medication', 'patient_id', 'order' => 'created_date', 'condition' => 'end_date is not null'),
			'commissioningbodies' => array(self::MANY_MANY, 'CommissioningBody', 'commissioning_body_patient_assignment(patient_id, commissioning_body_id)'),
			'referrals' => array(self::HAS_MANY, 'Referral', 'patient_id'),
			'lastReferral' => array(self::HAS_ONE, 'Referral', 'patient_id', 'order' => 'received_date desc'),
			'socialhistory' => array(self::HAS_ONE, 'SocialHistory', 'patient_id'),
			'adherence' => array(self::HAS_ONE, 'MedicationAdherence', 'patient_id'),
		);
	}

	/**
	* @return array customized attribute labels (name=>label)
	*/
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pas_key' => 'PAS Key',
			'dob' => 'Date of Birth',
			'date_of_death' => 'Date of Death',
			'gender' => 'Gender',
			'ethnic_group_id' => 'Ethnic Group',
			'hos_num' => 'Hospital Number',
			'nhs_num' => 'NHS Number',
		);
	}

	public function search_nr($params)
	{
		$criteria=new CDbCriteria;
		$criteria->join = "JOIN contact ON contact_id = contact.id";
		$criteria->compare('LOWER(first_name)',strtolower($params['first_name']),false);
		$criteria->compare('LOWER(last_name)',strtolower($params['last_name']),false);
		$criteria->compare('dob',$this->dob,false);
		$criteria->compare('gender',$this->gender,false);
		$criteria->compare('hos_num',$this->hos_num,false);
		$criteria->compare('nhs_num',$this->nhs_num,false);


		return $this->count($criteria);

	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @param array $params
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($params = array())
	{
		$params += array(
			'pageSize' => 20,
			'currentPage' => 0,
			'sortBy' => 'hos_num*1',
			'sortDir' => 'asc',
		);

		$criteria=new CDbCriteria;
		$criteria->compare('t.id', $this->id);
		$criteria->join = "JOIN contact ON contact_id = contact.id";
		if (isset($params['first_name'])) $criteria->compare('LOWER(contact.first_name)',strtolower($params['first_name']), false);
		if (isset($params['last_name'])) $criteria->compare('LOWER(contact.last_name)',strtolower($params['last_name']), false);
		if (strlen($this->nhs_num) == 10) {
			$criteria->compare('nhs_num',$this->nhs_num, false);
		} else {
			$criteria->compare('hos_num',$this->hos_num, false);
		}

		$criteria->order = $params['sortBy'] . ' ' . $params['sortDir'];

		Yii::app()->event->dispatch('patient_search_criteria', array('patient' => $this, 'criteria' => $criteria, 'params' => $params));

		$dataProvider = new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination' => array('pageSize' => $params['pageSize'], 'currentPage' => $params['currentPage'])
		));

		return $dataProvider;
	}

	public function beforeSave()
	{
		foreach (array('first_name', 'last_name', 'dob', 'title', 'primary_phone') as $property) {
			if ($randomised = $this->randomData($property)) {
				$this->$property = $randomised;
			}
		}
		return parent::beforeSave();
	}

	/*
	 * will group episodes by specialty, ordered by the configuration key of specialty sort,
	 * and alphanumeric for any specialties not configured.
	 *
	 * @returns Array
	 */
	public function getOrderedEpisodes()
	{
		if (!isset($this->_orderedepisodes)) {
			$episodes = $this->episodes;
			$by_specialty = array();

			// group
			foreach ($episodes as $ep) {
				if ($ep->firm) {
					if ($ssa = $ep->firm->serviceSubspecialtyAssignment) {
						$specialty = $ssa->subspecialty->specialty;
						$specialty_name = $specialty->name;
						$specialty_code = $specialty->code;
					} else {
						continue;
					}
				} else {
					$specialty_name = 'Support Services';
					$specialty_code = 'SUP';
				}
				$by_specialty[$specialty_code]['episodes'][] = $ep;
				$by_specialty[$specialty_code]['specialty'] = $specialty_name;
			}


			$res = array();
			if (count(array_keys($by_specialty)) > 1) {
				// get specialties that are configured
				if (isset(Yii::app()->params['specialty_sort'])) {
					foreach (Yii::app()->params['specialty_sort'] as $code) {
						if (isset($by_specialty[$code])) {
							$res[] = $by_specialty[$code];
							unset($by_specialty[$code]);
						}
					}
				}

				// sort the remainder
				function cmp($a, $b)
				{
					return strcasecmp($a['specialty'], $b['specialty']);
				}
				uasort($by_specialty, "cmp");
			}
			// either flattens, or gets the remainder
			foreach ($by_specialty as $row) {
				$res[] = $row;
			}

			$this->_orderedepisodes = $res;
		}

		return $this->_orderedepisodes;
	}

	/**
	 * Get the patient's age
	 *
	 * @return string
	 */
	public function getAge()
	{
		return Helper::getAge($this->dob, $this->date_of_death);
	}

	/**
	 * Calculate the patient's age
	 *
	 * @param string $check_date Date to check age on (default is today)
	 * @return string
	 */
	public function ageOn($check_date)
	{
		return Helper::getAge($this->dob, $this->date_of_death, $check_date);
	}

	/**
	 * @param string $check_date Optional date to check age on (default is today)
	 * @return boolean Is patient a child?
	 */
	public function isChild($check_date = null)
	{
		$age_limit = (isset(Yii::app()->params['child_age_limit'])) ? Yii::app()->params['child_age_limit'] : self::CHILD_AGE_LIMIT;
		if(!$check_date) {
			$check_date = date('Y-m-d');
		}
		return ($this->ageOn($check_date) < $age_limit);
	}

	/**
	 * Returns the date on which the patient will become an adult
	 *
	 * @return null|string
	 */
	public function getBecomesAdultDate()
	{
		return Helper::getDateForAge($this->dob, (isset(Yii::app()->params['child_age_limit'])) ? Yii::app()->params['child_age_limit'] : self::CHILD_AGE_LIMIT);
	}

	/**
	* @param integer $drug_id
	* @return boolean Is patient allergic?
	*/
	public function hasDrugAllergy($drug_id = null)
	{
		if ($drug_id) {
			if ($this->allergies) {
				$criteria = new CDbCriteria();
				$criteria->select = 't.id';
				$criteria->condition = 'paa.patient_id = :patient_id';
				$join = array();
				$join[] = 'JOIN drug_allergy_assignment daa ON daa.drug_id = t.id';
				$join[] = 'JOIN patient_allergy_assignment paa ON paa.allergy_id = daa.allergy_id';
				$criteria->join = implode(' ', $join);
				$criteria->params = array(':patient_id' => $this->id);
				return (bool) Drug::model()->findByPk($drug_id, $criteria);
			} else {
				return false;
			}
		} else {
			return (bool) $this->allergies;
		}
	}

	/**
	 * returns true if the patient has the allergy passed in
	 *
	 * @param $allergy
	 * @return boolean
	 */
	public function hasAllergy($allergy)
	{
		foreach ($this->allergies as $allrgy) {
			if ($allergy->id == $allrgy->id) {
				return true;
			}
		}
		return false;
	}

	/**
	 * returns true if the allergy status of the patient is known (has allergies, or no known allergies) false otherwise.
	 *
	 * @return bool
	 */
	public function hasAllergyStatus()
	{
		return ($this->no_allergies_date || $this->allergies);
	}
	/**
		* @return boolean Is patient deceased?
		*/
	public function isDeceased()
	{
		// Assume that if the patient has a date of death then they are actually dead, even if the date is in the future
		return (!empty($this->date_of_death));
	}

	/**
	* @return string Patient name for prefixing an address
	*/
	public function getCorrespondenceName()
	{
		if ($this->isChild()) {
			return 'Parent/Guardian of ' . $this->getFullName();
		} else {
			return $this->getFullName();
		}
	}

	/**
	* @return string Patient name for using as a salutation
	*/
	public function getSalutationName()
	{
		if ($this->isChild()) {
			return 'Parent/Guardian of ' . $this->first_name . ' ' . $this->last_name;
		} else {
			return $this->title . ' ' . $this->last_name;
		}
	}

	/**
	* @return string Full name
	*/
	public function getFullName()
	{
		return trim(implode(' ',array($this->title, $this->first_name, $this->last_name)));
	}

	/**
	 * get the Patient name according to HSCIC guidelines
	 *
	 * @return string
	 */
	public function getHSCICName($bold=false)
	{
		$last_name = $bold ? "<strong>".strtoupper($this->last_name)."</strong>" : strtoupper($this->last_name);
		return trim(implode(' ',array($last_name . ",", $this->first_name, '('. $this->title . ')')));
	}

	public function getDisplayName()
	{
		return '<span class="patient-surname">'.strtoupper($this->last_name).'</span>, <span class="patient-name">'.$this->first_name.'</span>';
	}

	private function randomData($field)
	{
		if (!Yii::app()->params['pseudonymise_patient_details']) {
			return false;
		}

		// exceptions come first
		if ('dob' == $field) {
			return $this->randomDate();
		}
		if ('title' == $field) {
			// gender neutral
			return 'Dr';
		}

		$keyInDatafile = $field;
		if (('address1' == $field) || ('address2' == $field)) {
			$keyInDatafile = 'address';
		}

		// the following cases are based on a random data source.  address has to cover the 'address1' and 'address2' fields
		$randomSourceFieldOrder = array('first_name','last_name','address','city','postcode','primary_phone');

		if (!in_array(strtolower($keyInDatafile), $randomSourceFieldOrder)) {
			return false;
		}

		$randomSource = file(Yii::app()->basePath . '/data/randomdata.csv');
		$randomEntryArray = explode(",", trim($randomSource[array_rand($randomSource)]));

		return $randomEntryArray[array_search($keyInDatafile, $randomSourceFieldOrder)];
	}

	private function randomDate($startDate='1931-01-01',$endDate='2010-12-12')
	{
		return date("Y-m-d",strtotime("$startDate + ".rand(0,round((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24)))." days"));
	}

	/**
	* Pass through use_pas flag to allow pas supression
	* @see CActiveRecord::instantiate()
	*/
	protected function instantiate($attributes)
	{
			$model = parent::instantiate($attributes);
			$model->use_pas = $this->use_pas;
			return $model;
	}

	/**
		* Raise event to allow external data sources to update patient
		* @see CActiveRecord::afterFind()
		*/
	protected function afterFind()
	{
		parent::afterFind();
		Yii::app()->event->dispatch('patient_after_find', array('patient' => $this));
	}

	/**
	* Get the episode for the subspecialty of the firm (or no subspecialty when the firm doesn't have one)
	*
	* @return Episode
	*/
	public function getEpisodeForCurrentSubspecialty()
	{
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		return Episode::model()->getCurrentEpisodeByFirm($this->id, $firm, true);
	}

	/**
	 * Get or create an episode for the patient under the given Firm (Note that an episode will be returned if there
	 * is match on Firm Subspecialty rather than on Firm)
	 *
	 * @param $firm
	 * @param bool $include_closed
	 * @return CActiveRecord|Episode|null
	 */
	public function getOrCreateEpisodeForFirm($firm, $include_closed = false)
	{
		if (!$episode = Episode::getCurrentEpisodeByFirm($this->id, $firm, $include_closed)) {
			$episode = $this->addEpisode($firm);
		}
		return $episode;
	}


	/**
	 * returns the ophthalmic information object for this patient (creates a default one if one does not exist - but does not save it)
	 *
	 * @return PatientOphInfo
	 */
	public function getOphInfo()
	{
		$info = PatientOphInfo::model()->find('patient_id = ?', array($this->id));
		if (!$info) {
			$info = new PatientOphInfo();
			$info->patient_id = $this->id;
			// only interested in yyyy mm dd for the cvi date
			$info->cvi_status_date = substr($this->created_date, 0 , 10);
			$info->cvi_status_id = 1;
		}
		return $info;
	}

	/* Patient as subject, eg man, woman, boy girl */

	public function getSub()
	{
		if ($this->isChild()) {
			return ($this->gender == 'F' ? 'girl' : 'boy');
		} else {
			return ($this->gender == 'M' ? 'man' : 'woman');
		}
	}

	public function getPro()
	{
		return ($this->gender == 'F' ? 'she' : 'he');
	}

	public function getEpd()
	{
		$episode = $this->getEpisodeForCurrentSubspecialty();

		if ($episode && $disorder = $episode->diagnosis) {

			if ($episode->eye) {
				return $episode->eye->getAdjective() . ' ' . strtolower($disorder->term);
			} else {
				return strtolower($disorder->term);
			}
		}
	}

	public function getEps()
	{
		$episode = $this->getEpisodeForCurrentSubspecialty();

		if ($episode && $eye = $episode->eye) {
			return strtolower($eye->adjective);
		}
	}

	public function getGenderString()
	{
		return ($this->gender == 'F' ? 'Female' : 'Male');
	}

	public function getEthnicGroupString()
	{
		if ($this->ethnic_group) {
			return $this->ethnic_group->name;
		} else {
			return 'Unknown';
		}
	}

	public function getObj()
	{
		return ($this->gender == 'F' ? 'her' : 'him');
	}

	public function getPos()
	{
		return ($this->gender == 'M' ? 'his' : 'her');
	}

	public function getTitle()
	{
		return $this->contact->title;
	}

	public function getFirst_name()
	{
		return $this->contact->first_name;
	}

	public function getLast_name()
	{
		return $this->contact->last_name;
	}

	public function getNick_name()
	{
		return $this->contact->nick_name;
	}

	public function getPrimary_phone()
	{
		return $this->contact->primary_phone;
	}

	public function getSummaryAddress()
	{
		return $this->contact->address ? $this->getLetterAddress(array('delimiter'=>'<br/>')) : 'Unknown';
	}

	/**
	 * returns a standard allergy string for the patient
	 *
	 * @return string
	 */
	public function getAllergiesString()
	{
		if (!$this->hasAllergyStatus()) {
			return 'Patient allergy status is not known';
		}
		if ($this->no_allergies_date) {
			return 'Patient has no known allergies (as of ' . Helper::convertDate2NHS($this->no_allergies_date) . ')';
		}

		$allergies = array();
		foreach ($this->allergies as $allergy) {
			$allergies[] = $allergy->name;
		}
		return 'Patient is allergic to: ' . implode(', ',$allergies);
	}

	/**
	 * adds an allergy to the patient
	 *
	 * @param Allergy $allergy
	 * @param string $other
	 * @throws Exception
	 */
	public function addAllergy(Allergy $allergy, $other = null, $comments = null)
	{
		if ($allergy->name == 'Other') {
			if (!$other) throw new Exception("No 'other' allergy specified");
		} else {
			if (PatientAllergyAssignment::model()->exists('patient_id=? and allergy_id=?', array($this->id, $allergy->id))) {
				throw new Exception("Patient is already assigned allergy '{$allergy->name}'");
			}
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			$paa = new PatientAllergyAssignment;
			$paa->patient_id = $this->id;
			$paa->allergy_id = $allergy->id;
			$paa->comments = $comments;
			$paa->other = $other;
			if (!$paa->save()) {
				throw new Exception('Unable to add patient allergy assignment: '.print_r($paa->getErrors(),true));
			}

			$this->audit('patient','add-allergy');
			if ($this->no_allergies_date) {
				$this->no_allergies_date = null;
				if (!$this->save()) {
					throw new Exception('Could not remove no allergy flag: ' . print_r($this->getErrors(), true));
				};
			}
			$this->audit('patient','remove-noallergydate');
			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback();
			throw $e;
		}
	}

	/**
	 * marks the patient as having no allergies as of now
	 *
	 * @throws Exception
	 */
	public function setNoAllergies()
	{
		if (!empty($this->allergyAssignments)) {
			throw new Exception('Unable to set no allergy date as patient still has allergies assigned');
		}

		$this->no_allergies_date = date('Y-m-d H:i:s');
		if (!$this->save()) {
			throw new Exception('Unable to set no allergy date:' .  print_r($this->getErrors(), true));
		}

		$this->audit('patient', 'set-noallergydate');
	}


	/**
	 * marks the patient as having no family history
	 *
	 * @throws Exception
	 */
	public function setNoFamilyHistory()
	{
		if (!empty($this->familyHistory)) {
			throw new Exception('Unable to set no family history date as patient still has family history assigned');
		}

		$this->no_family_history_date = date('Y-m-d H:i:s');

		if (!$this->save()) {
			throw new Exception('Unable to set no family history:' .  print_r($this->getErrors(), true));
		}

		$this->audit('patient', 'set-nofamilyhistorydate');
	}

	/*
	 * returns all disorder ids for the patient, aggregating the principal diagnosis for each patient episode, and any secondary diagnosis on the patient
	*
	* FIXME: some of this can be abstracted to a relation when we upgrade from yii 1.1.8, which has some problems with yii relations:
	* 	http://www.yiiframework.com/forum/index.php/topic/26806-relations-through-problem-wrong-on-clause-in-sql-generated/
	*
	* @returns array() of disorder ids
	*/
	private function getAllDisorderIds()
	{
		// Get all the secondary disorders
		$criteria = new CDbCriteria;
		$criteria->compare('patient_id', $this->id);
		$sd = SecondaryDiagnosis::model()->findAll($criteria);
		$disorder_ids = array();
		foreach ($sd as $d) {
			$disorder_ids[] = $d->disorder_id;
		}


		foreach ($this->episodes as $ep) {
			//primary disorder for episode
			if ($ep->disorder_id) {
				$disorder_ids[] = $ep->disorder_id;
			}
		}

		return array_unique($disorder_ids);
	}

	/*
	 * returns all disorders for the patient.
	 *
	 * FIXME: some of this can be abstracted to a relation when we upgrade from yii 1.1.8, which has some problems with yii relations:
	 * 	http://www.yiiframework.com/forum/index.php/topic/26806-relations-through-problem-wrong-on-clause-in-sql-generated/
	 *
	 * @returns array() of disorders
	 */
	public function getAllDisorders()
	{
		return Disorder::model()->findAllByPk($this->getAllDisorderIds());
	}

	/*
	 * checks if the patient has a disorder that is defined as being within the SNOMED tree specified by the given $snomed id.
	 *
	 * @returns bool
	 */
	public function hasDisorderTypeByIds($snomeds)
	{
		$disorder_ids = $this->getAllDisorderIds();
		if (count($disorder_ids)) {
			return Disorder::model()->ancestorIdsMatch($disorder_ids, $snomeds);
		}
		return false;
	}

	/**
	 * get the patient disorders that are of the type in the list of disorder ids provided
	 *
	 * @param integer[] $snomeds - disorder ids to check for
	 * @return Disorder[]
	 */
	public function getDisordersOfType($snomeds)
	{
		$disorders = array();
		foreach ($snomeds as $id) {
			$disorders[] = Disorder::model()->findByPk($id);
		}

		$patient_disorder_ids = $this->getAllDisorderIds();
		$res = array();
		foreach ($patient_disorder_ids as $p_did) {
			foreach ($disorders as $d) {
				if (($d->id == $p_did) || $d->ancestorOfIds(array($p_did))) {
					$res[] = Disorder::model()->findByPk($p_did);
					break;
				}
			}
		}
		return $res;
	}

	public function getSystemicDiagnoses()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('patient_id', $this->id);
		$criteria->join = 'join disorder on t.disorder_id = disorder.id and specialty_id is null';
		$criteria->order = 'date asc';

		return SecondaryDiagnosis::model()->findAll($criteria);
	}

	public function getOphthalmicDiagnoses()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('patient_id', $this->id);

		$criteria->join = 'join disorder on t.disorder_id = disorder.id join specialty on disorder.specialty_id = specialty.id';
		$criteria->compare('specialty.code', 130);

		$criteria->order = 'date asc';

		return SecondaryDiagnosis::model()->findAll($criteria);
	}

	/*
	 * returns the specialty codes that are relevant to the patient. Determined by looking at the diagnoses
	 * related to the patient.
	 *
	 * @return Array specialty codes
	 */
	public function getSpecialtyCodes()
	{
		$codes = array();
		if (isset(Yii::app()->params['specialty_codes'])) {
			$codes = Yii::app()->params['specialty_codes'];
		} else {
			// TODO: perform dynamic calculation of specialty codes based on the episodes and/or events assigned to patient
		}
		return $codes;
	}

	public function addDiagnosis($disorder_id, $eye_id=false, $date=false)
	{
		if (!$date) {
			$date = date('Y-m-d');
		}

		if (!$disorder = Disorder::model()->findByPk($disorder_id)) {
			throw new Exception('Disorder not found: '.$disorder_id);
		}

		if ($disorder->specialty_id) {
			$type = strtolower(Specialty::model()->findByPk($disorder->specialty_id)->code);
		} else {
			$type = 'sys';
		}

		if (!$sd = SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and eye_id=? and date=?',array($this->id,$disorder_id,$eye_id,$date))) {
			$action = "add-diagnosis-$type";
			$sd = new SecondaryDiagnosis;
			$sd->patient_id = $this->id;
			$sd->disorder_id = $disorder_id;
			$sd->eye_id = $eye_id;
			$sd->date = $date;

			if (!$sd->save()) {
				throw new Exception('Unable to save secondary diagnosis: '.print_r($sd->getErrors(),true));
			}

			$this->audit('patient',$action);
		}
	}

	public function removeDiagnosis($diagnosis_id)
	{
		if (!$sd = SecondaryDiagnosis::model()->findByPk($diagnosis_id)) {
			throw new Exception('Unable to find secondary_diagnosis: '.$diagnosis_id);
		}

		if (!$disorder = Disorder::model()->findByPk($sd->disorder_id)) {
			throw new Exception('Unable to find disorder: '.$sd->disorder_id);
		}

		if ($disorder->specialty_id) {
			$type = strtolower(Specialty::model()->findByPk($disorder->specialty_id)->code);
		} else {
			$type = 'sys';
		}

		if (!$sd->delete()) {
			throw new Exception('Unable to delete diagnosis: '.print_r($sd->getErrors(),true));
		}

		$this->audit('patient',"remove-$type-diagnosis");
	}

	/**
	 * update the patient's ophthalmic information
	 *
	 * @param PatientOphInfoCviStatus $cvi_status
	 * @param string $cvi_status_date - fuzzy date string of the format yyyy-mm-dd
	 * @return true|array True or array of errors
	 */
	public function editOphInfo($cvi_status, $cvi_status_date)
	{
		$oph_info = $this->getOphInfo();
		if ($oph_info->id) {
			$action = 'update-ophinfo';
		} else {
			$action = 'set-ophinfo';
		}

		$oph_info->cvi_status_id = $cvi_status->id;
		$oph_info->cvi_status_date = $cvi_status_date;

		if (!$oph_info->save()) {
			return $oph_info->errors;
		}

		$this->audit('patient', $action);

		return true;
	}

	public function getContactAddress($contact_id, $location_type=false, $location_id=false)
	{
		if ($location_type && $location_id) {
			if ($pca = PatientContactAssignment::model()->find('patient_id=? and contact_id=? and '.$location_type.'_id=?',array($this->id, $contact_id, $location_id))) {
				return $pca->address;
			}
		} else {
			if ($pca = PatientContactAssignment::model()->find('patient_id=? and contact_id=?',array($this->id, $contact_id))) {
				return $pca->address;
			}
		}

		return false;
	}

	public function getNhsnum()
	{
		$nhs_num = preg_replace('/[^0-9]/', '', $this->nhs_num);
		return $nhs_num ? substr($nhs_num,0,3).' '.substr($nhs_num,3,3).' '.substr($nhs_num,6,4) : 'not known';
	}

	public function hasLegacyLetters()
	{
		if ($api = Yii::app()->moduleAPI->get('OphLeEpatientletter')) {
			return $this->patientHasLegacyLetters($this->hos_num);
		}
	}

	/**
	 * Get the Diabetes Type as a Disorder instance
	 *
	 * @return Disorder|null
	 */
	public function getDiabetesType()
	{
		if ($this->hasDisorderTypeByIds(Disorder::$SNOMED_DIABETES_TYPE_I_SET) ) {
			return Disorder::model()->findByPk(Disorder::SNOMED_DIABETES_TYPE_I);
		} elseif ($this->hasDisorderTypeByIds(Disorder::$SNOMED_DIABETES_TYPE_II_SET)) {
			return Disorder::model()->findByPk(Disorder::SNOMED_DIABETES_TYPE_II);
		}

		return null;
	}

	/**
	 * Get the patient diabetes type as Disorder instance - will return generic Diabetes
	 * if no specific type available, but patient has diabetes
	 *
	 * @return Disorder|null
	 */
	public function getDiabetes()
	{
		$type = $this->getDiabetesType();
		if ($type === null && $this->hasDisorderTypeByIds(Disorder::$SNOMED_DIABETES_SET)) {
			return Disorder::model()->findByPk(Disorder::SNOMED_DIABETES);
		}
		return $type;
	}

	/**
	 * Diabetes mellitus as a letter string
	 *
	 * @return string
	 */
	public function getDmt()
	{
		if ($disorder = $this->getDiabetes()) {
			return $disorder->term;
		}

		return 'not diabetic';
	}

	public function audit($target, $action, $data=null, $log=false, $properties=array())
	{
		$properties['patient_id'] = $this->id;
		parent::audit($target, $action, $data, $log, $properties);
	}

	public function getChildPrefix()
	{
		return $this->isChild() ? "child's " : "";
	}

	public function getSdl()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('patient_id',$this->id);
		$criteria->order = 'created_date asc';

		$diagnoses = array();

		foreach (SecondaryDiagnosis::model()->findAll('patient_id=?',array($this->id)) as $i => $sd) {
			if ($sd->disorder->specialty && $sd->disorder->specialty->code == 130) {
				$diagnoses[] = strtolower(($sd->eye ? $sd->eye->adjective.' ' : '').$sd->disorder->term);
			}
		}

		return Helper::formatList($diagnoses);
	}

	/**
	 * Systemic diagnoses shortcode
	 *
	 * @return string
	 */
	public function getSyd()
	{
		return strtolower(Helper::formatList(Helper::extractValues($this->getSystemicDiagnoses(), 'disorder.term')));
	}

	public function addPreviousOperation($operation, $side_id, $date)
	{
		if (!$pa = PreviousOperation::model()->find('patient_id=? and operation=? and date=?',array($this->id,$operation,$date))) {
			$pa = new PreviousOperation;
			$pa->patient_id = $this->id;
			$pa->operation = $operation;
			$pa->date = $date;
		}
		$pa->side_id = $side_id ? $side_id : null;

		if (!$pa->save()) {
			throw new Exception("Unable to save previous operation: ".print_r($pa->getErrors(),true));
		}
	}

	/**
	 * Adds FamilyHistory entry to the patient if it's not a duplicate
	 *
	 * @param $relative_id
	 * @param $other_relative
	 * @param $side_id
	 * @param $condition_id
	 * @param $other_condition
	 * @param $comments
	 * @throws Exception
	 */
	public function addFamilyHistory($relative_id,$other_relative,$side_id,$condition_id,$other_condition,$comments)
	{
		$check_sql = 'patient_id=? and relative_id=? and side_id=? and condition_id=?';
		$params = array($this->id,$relative_id,$side_id,$condition_id);
		if ($other_relative) {
			$check_sql .= ' and other_relative=?';
			$params[] = $other_relative;
		}
		else {
			$check_sql .= ' and other_relative is null';
		}
		if ($other_condition) {
			$check_sql .= ' and other_condition=?';
			$params[] = $other_condition;
		}
		else {
			$check_sql .= ' and other_condition is null';
		}

		if (!$fh = FamilyHistory::model()->find($check_sql,$params)) {
			$fh = new FamilyHistory;
			$fh->patient_id = $this->id;
			$fh->relative_id = $relative_id;
			$fh->side_id = $side_id;
			$fh->condition_id = $condition_id;
		}

		$fh->comments = $comments;

		if (!$fh->save()) {
			throw new Exception("Unable to save family history: ".print_r($fh->getErrors(),true));
		}

		if ($this->no_family_history_date) {
			$this->no_family_history_date = null;
			if (!$this->save()) {
				throw new Exception('Could not remove no family history flag: ' . print_r($this->getErrors(), true));
			};
		}
	}

	public function currentContactIDS()
	{
		$ids = array(
			'locations' => array(),
			'contacts' => array(),
		);

		foreach ($this->contactAssignments as $pca) {
			if ($pca->location_id) {
				$ids['locations'][] = $pca->location_id;
			} else {
				$ids['contacts'][] = $pca->contact_id;
			}
		}
		return $ids;
	}

	public function getPrefix()
	{
		return 'Patient';
	}

	public function getEpc()
	{
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($user = $episode->firm->consultant) {
				return $user->fullName;
			}
		}
	}

	public function getEpv()
	{
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			return $episode->firm->serviceSubspecialtyAssignment->service->name;
		}
	}

	/**
	 * return the open episode of the given subspecialty if there is one, null otherwise
	 *
	 * @param $subspecialty_id
	 * @return CActiveRecord|null
	 */
	public function getOpenEpisodeOfSubspecialty($subspecialty_id)
	{
		return Episode::model()->getCurrentEpisodeBySubspecialtyId($this->id, $subspecialty_id);
	}

	/**
	 * returns true if patient has an open episode for the given subspecialty id
	 *
	 * @param $subspecialty_id
	 * @return boolean
	 */
	public function hasOpenEpisodeOfSubspecialty($subspecialty_id)
	{
		return  $this->getOpenEpisodeOfSubspecialty($subspecialty_id) ? true : false;
	}

	/**
	 * add an episode to the patient for the given Firm
	 *
	 * @param $firm
	 * @return Episode
	 * @throws Exception
	 */
	public function addEpisode($firm)
	{
		$episode = new Episode;
		$episode->patient_id = $this->id;
		if ($firm->getSubspecialtyID()) {
			$episode->firm_id = $firm->id;
		} else {
			$episode->support_services = true;
		}
		$episode->start_date = date("Y-m-d H:i:s");

		if (!$episode->save()) {
			OELog::log("Unable to create new episode for patient_id=$episode->patient_id, firm_id=$episode->firm_id, start_date='$episode->start_date'");
			throw new Exception('Unable to create create episode: '.print_r($episode->getErrors(),true));
		}

		OELog::log("New episode created for patient_id=$episode->patient_id, firm_id=$episode->firm_id, start_date='$episode->start_date'");

		$episode->audit('episode','create');

		Yii::app()->event->dispatch('episode_after_create', array('episode' => $episode));

		return $episode;
	}

	public function getLatestEvent()
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition('episode.patient_id = :pid');
		$criteria->params = array(':pid' => $this->id);
		$criteria->order = "t.event_date DESC, t.created_date DESC";
		$criteria->limit = 1;

		return Event::model()->with('episode')->find($criteria);

	}

	/**
	 * get an associative array of CommissioningBody for this patient and the patient's practice
	 * indexed by CommissioningBodyType id.
	 *
	 * @return array[string][CommissioningBody]
	 */
	public function getDistinctCommissioningBodiesByType()
	{
		$res = array();
		$seen_bodies = array();

		foreach ($this->commissioningbodies as $body) {
			if (in_array($body->id, $seen_bodies)) {
				continue;
			}
			if (array_key_exists($body->type->id, $res)) {
				$res[$body->type->id][] = $body;
			}
			else {
				$res[$body->type->id] = array($body);
			}
			$seen_bodies[] = $body->id;
		}

		if ($this->practice) {
			foreach ($this->practice->commissioningbodies as $body) {
				if (in_array($body->id, $seen_bodies)) {
					continue;
				}
				if (array_key_exists($body->type->id, $res)) {
					$res[$body->type->id][] = $body;
				}
				else {
					$res[$body->type->id] = array($body);
				}
				$seen_bodies[] = $body->id;
			}
		}

		return $res;
	}

	/**
	 * get the CommissioningBody of the CommissioningBodyType $type
	 * currently assumes there would only ever be one commissioning body of a given type
	 *
	 * @param CommissioningBodyType $type
	 * @return CommissioningBody
	 */
	public function getCommissioningBodyOfType($type)
	{
		foreach ($this->commissioningbodies as $body) {
			if ($body->type->id == $type->id) {
				return $body;
			}
		}

		if ($this->practice) {
			foreach ($this->practice->commissioningbodies as $body) {
				if ($body->type->id == $type->id) {
					return $body;
				}
			}
		}
	}

	// storage of warning data
	protected $_clinical_warnings = null;
	protected $_nonclinical_warnings = null;

	/**
	 * return the patient warnings that have been defined for the patient. If $clinical is false
	 * only non-clinical warnings will be returned.
	 *
	 * @param boolean $clinical
	 * @return {'short_msg' => string, 'long_msg' => string, 'details' => string}[]
	 */
	public function getWarnings($clinical=true)
	{
		// At the moment, we only warn for diabetes, so this is quite lightweight and hard coded
		// but this should serve as a wrapper function for configuring warnings (i.e. a system setting could
		// define what should be warned on, and then we return a structure that is determined from this)

		if ($this->_nonclinical_warnings === null) {
			// placeholder for nonclinical warning setup
			$this->_nonclinical_warnings = array();
		}

		$res = $this->_nonclinical_warnings;

		if ($clinical) {
			if ($this->_clinical_warnings === null) {
				$this->_clinical_warnings = array();
				if ($diabetic_disorders = $this->getDisordersOfType(Disorder::$SNOMED_DIABETES_SET) ) {
					$terms = array();
					foreach ($diabetic_disorders as $disorder) {
						$terms[] = $disorder->term;
					}
					$this->_clinical_warnings[] = array(
							'short_msg' => 'Diabetes',
							'long_msg' => 'Patient is Diabetic',
							'details' => implode(', ', $terms)
					);
				}
				if ($this->allergyAssignments) {
					foreach ($this->allergyAssignments as $aa) {
						$allergies[] = $aa->name;
					}
					$this->_clinical_warnings[] = array(
						'short_msg' => 'Allergies',
						'long_msg' => 'Patient has allergies',
						'details' => implode(', ', $allergies)
					);
				}
			}
			$res = array_merge($res, $this->_clinical_warnings);
		}

		return $res;
	}
}
