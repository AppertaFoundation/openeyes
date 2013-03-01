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

/**
 * This is the model class for table "patient".
 *
 * The followings are the available columns in table 'patient':
 * @property integer	$id
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
 * @property integer	$gp_id
 * @property integer $practice_id
 * @property string  $created_date
 * @property string  $last_modified_date
 * @property integer	$created_user_id
 * @property integer	$last_modified_user_id
 * 
 * The followings are the available model relations:
 * @property Episode[] $episodes
 * @property Address[] $addresses
 * @property Address $address Primary address
 * @property HomeAddress $homeAddress Home address
 * @property CorrespondAddress $correspondAddress Correspondence address
 * @property Contact[] $contacts
 * @property Gp $gp
 * @property Practice $practice
 * @property Allergy[] $allergies
 */
class Patient extends BaseActiveRecord {
	
	const CHILD_AGE_LIMIT = 16;
	
	public $use_pas = TRUE;
	private $_orderedepisodes;
	
	
	/**
		* Returns the static model of the specified AR class.
		* @return Patient the static model class
		*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Suppress PAS integration
	 * @return Patient
	 */
	public function noPas() {
		// Clone to avoid singleton problems with use_pas flag
		$model = clone $this;
		$model->use_pas = FALSE;
		return $model;
	}

	/**
		* @return string the associated database table name
		*/
	public function tableName() {
		return 'patient';
	}

	/**
		* @return array validation rules for model attributes.
		*/
	public function rules() {
		return array(
			array('pas_key', 'length', 'max' => 10),
			array('hos_num, nhs_num', 'length', 'max' => 40),
			array('gender', 'length', 'max' => 1),
			array('dob, date_of_death', 'safe'),
			array('dob, hos_num, nhs_num, date_of_death', 'safe', 'on' => 'search'),
		);
	}

	/**
		* @return array relational rules.
		*/
	public function relations() {
		return array(
			'legacyepisodes' => array(self::HAS_MANY, 'Episode', 'patient_id',
				'condition' => "legacy=1",
			),
			'episodes' => array(self::HAS_MANY, 'Episode', 'patient_id',
				'condition' => "legacy=0 or legacy is null",
			),
			'addresses' => array(self::HAS_MANY, 'Address', 'parent_id',
				'on' => "parent_class = 'Patient'"
			),
			// Order: Current addresses; prefer H records for primary address, but fall back to C and then others (T); most recent start date
			// Unexpired addresses are preferred, but an expired address will be returned if necessary.
			'address' => array(self::HAS_ONE, 'Address', 'parent_id',
				'on' => "parent_class = 'Patient'",
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(type,'C','H') DESC, date_start DESC"
			),
			// Order: Current addresses; prefer H records for home address, but fall back to C and then others (T); most recent start date
			// Unexpired addresses are preferred, but an expired address will be returned if necessary.
			'homeAddress' => array(self::HAS_ONE, 'Address', 'parent_id',
				'on' => "parent_class = 'Patient'",
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(type,'C','H') DESC, date_end DESC, date_start DESC"
			),
			// Order: Current addresses; prefer C records for correspond address, but fall back to T and then others (H); most recent start date
			// Unexpired addresses are preferred, but an expired address will be returned if necessary.
			'correspondAddress' => array(self::HAS_ONE, 'Address', 'parent_id',
				'on' => "parent_class = 'Patient'",
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(type,'T','C') DESC, date_end DESC, date_start DESC"
			),
			'contact' => array(self::HAS_ONE, 'Contact', 'parent_id',
				'on' => "parent_class = 'Patient'",
			),
			'gp' => array(self::BELONGS_TO, 'Gp', 'gp_id'),
			'practice' => array(self::BELONGS_TO, 'Practice', 'practice_id'),
			'contactAssignments' => array(self::HAS_MANY, 'PatientContactAssignment', 'patient_id'),
			'allergies' => array(self::MANY_MANY, 'Allergy', 'patient_allergy_assignment(patient_id, allergy_id)', 'order' => 'name'),
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
			'hos_num' => 'Hospital Number',
			'nhs_num' => 'NHS Number',
		);
	}

	public function search_nr($params)
	{
		$criteria=new CDbCriteria;
		$criteria->join = "JOIN contact ON contact.parent_id = t.id AND contact.parent_class='Patient'";
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
		* @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
		*/
	public function search($params = false) {
		if (!is_array($params)) {
			$params = array(
				'pageSize' => 20,
				'currentPage' => 0,
				'sortBy' => 'hos_num*1',
				'sortDir' => 'asc',
			);
		}

		$criteria=new CDbCriteria;
		$criteria->join = "JOIN contact ON contact.parent_id = t.id AND contact.parent_class='Patient'";
		$criteria->compare('LOWER(contact.first_name)',strtolower($params['first_name']), false);
		$criteria->compare('LOWER(contact.last_name)',strtolower($params['last_name']), false);
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
	public function getOrderedEpisodes() {
		
		if (!isset($this->_orderedepisodes)) {
			$episodes = $this->episodes;
			$by_specialty = array();
			
			// group
			foreach ($episodes as $ep) {
				$specialty = $ep->firm->serviceSubspecialtyAssignment->subspecialty->specialty;
				$by_specialty[$specialty->code]['episodes'][] = $ep;
				$by_specialty[$specialty->code]['specialty'] = $specialty;
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
				function cmp($a, $b) {
					return strcasecmp($a['specialty']->name, $b['specialty']->name);
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
	
	public function getAge() {
		return Helper::getAge($this->dob, $this->date_of_death);
	}

	/**
	* @return boolean Is patient a child?
	*/
	public function isChild() {
		$age_limit = (isset(Yii::app()->params['child_age_limit'])) ? Yii::app()->params['child_age_limit'] : self::CHILD_AGE_LIMIT;
		return ($this->getAge() < $age_limit);
	}

	/**
	* @param integer $drug_id
	* @return boolean Is patient allergic?
	*/
	public function hasAllergy($drug_id = null) {
		if($drug_id) {
			if($this->allergies) {
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
		* @return boolean Is patient deceased?
		*/
	public function isDeceased() {
		// Assume that if the patient has a date of death then they are actually dead, even if the date is in the future
		return (!empty($this->date_of_death));
	}

	/**
		* @return string Patient name for prefixing an address 
		*/
	public function getAddressName() {
		if ($this->isChild()) {
			return 'Parent/Guardian of ' . $this->getFullName();
		} else {
			return $this->getFullName();
		}	
	}
	
	/**
		* @return string Patient name for using as a salutation 
		*/
	public function getSalutationName() {
		if ($this->isChild()) {
			return 'Parent/Guardian of ' . $this->first_name . ' ' . $this->last_name;
		} else {
			return $this->title . ' ' . $this->last_name;
		}	
	}
	
	/**
		* @return string Full name 
		*/
	public function getFullName() {
		return trim(implode(' ',array($this->title, $this->first_name, $this->last_name)));
	}

	public function getDisplayName() {
		return '<span class="surname">'.strtoupper($this->last_name).'</span>, <span class="given">'.$this->first_name.'</span>';
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
	protected function instantiate($attributes) {
			$model = parent::instantiate($attributes);
			$model->use_pas = $this->use_pas;
			return $model;
	}
	
	/**
		* Raise event to allow external data sources to update patient
		* @see CActiveRecord::afterFind()
		*/
	protected function afterFind() {
		parent::afterFind();
		Yii::app()->event->dispatch('patient_after_find', array('patient' => $this));
	}

	public function getEpisodeForCurrentSubspecialty() {
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		$ssa = $firm->serviceSubspecialtyAssignment;

		// Get all firms for the subspecialty
		$firm_ids = array();
		foreach (Firm::model()->findAll('service_subspecialty_assignment_id=?',array($ssa->id)) as $firm) {
			$firm_ids[] = $firm->id;
		}

		return Episode::model()->find('patient_id=? and firm_id in ('.implode(',',$firm_ids).')',array($this->id));
	}

	/* Patient as subject, eg man, woman, boy girl */

	public function getSub() {
		if ($this->isChild()) {
			return ($this->gender == 'F' ? 'girl' : 'boy');
		} else {
			return ($this->gender == 'M' ? 'man' : 'woman');
		}
	}

	public function getPro() {
		return ($this->gender == 'F' ? 'she' : 'he');
	}

	public function getEpd() {
		$episode = $this->getEpisodeForCurrentSubspecialty();
		
		if ($episode && $disorder = $episode->diagnosis) {
			return strtolower($disorder->term);
		}
	}

	public function getEps() {
		$episode = $this->getEpisodeForCurrentSubspecialty();

		if ($episode && $eye = $episode->eye) {
			return strtolower($eye->adjective);
		}
	}

	public function getObj() {
		return ($this->gender == 'F' ? 'her' : 'him');
	}

	public function getOpl() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphTrOperation'))->id)) {
				if ($eo = ElementOperation::model()->find('event_id=?',array($event->id))) {
					foreach ($eo->procedures as $i => $procedure) {
						if ($i) $return .= ', ';
						@$return .= $eo->eye->name.' '.$procedure->term;
					}

					return strtolower($return);
				}
			}
		}
	}

	public function getOpr() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphTrOperationnote'))->id)) {
				if ($pl = ModuleAPI::getmodel('OphTrOperationnote','ElementProcedureList')) {
					if ($pl = $pl->find('event_id=?',array($event->id))) {
						foreach ($pl->procedures as $i => $procedure) {
							if ($i) $return .= ', ';
							@$return .= $pl->eye->name.' '.$procedure->term;
						}

						return strtolower($return);
					}
				}
			}
		}
	}

	public function getOps() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphTrOperationnote'))->id)) {
				if ($pl = ModuleAPI::getmodel('OphTrOperationnote','ElementProcedureList')) {
					if ($pl = $pl->find('event_id=?',array($event->id))) {
						foreach ($pl->procedures as $i => $procedure) {
							@$return .= $pl->eye->name.' '.$procedure->snomed_term."\n";
						}

						return strtolower($return);
					}
				}
			}
		}
	}

	public function getPos() {
		return ($this->gender == 'M' ? 'his' : 'her');
	}

	public function getTitle() {
		return $this->contact->title;
	}

	public function getFirst_name() {
		return $this->contact->first_name;
	}

	public function getLast_name() {
		return $this->contact->last_name;
	}

	public function getNick_name() {
		return $this->contact->nick_name;
	}

	public function getPrimary_phone() {
		return $this->contact->primary_phone;
	}

	public function getPre() {
		if($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?', array('OphDrPrescription'))->id)) {
				if($details_model = ModuleAPI::getmodel('OphDrPrescription','Element_OphDrPrescription_Details')) {
					$details = $details_model->find('event_id=?',array($event->id));
					return $details->getLetterText();
				}
			}
		}
		return '';
	}

	public function getLetterAddress() {
		$address = $this->addressName;

		if (isset($this->qualifications)) {
			$address .= ' '.$this->qualifications;
		}

		$address .= "\n";
		
		if ($this->address) {
			$address .= implode("\n",$this->address->getLetterArray(false));
		}
		
		return $address; 
	}
	
	public function getAllergiesString() {
		$allergies = array();
		foreach($this->allergies as $allergy) {
			$allergies[] = $allergy->name;
		}
		return implode(', ',$allergies);		
	}

	public function addAllergy($allergy_id) {
		if (!PatientAllergyAssignment::model()->find('patient_id=? and allergy_id=?',array($this->id,$allergy_id))) {
			$paa = new PatientAllergyAssignment;
			$paa->patient_id = $this->id;
			$paa->allergy_id = $allergy_id;
			if (!$paa->save()) {
				throw new Exception('Unable to add patient allergy assignment: '.print_r($paa->getErrors(),true));
			}

			$audit = new Audit;
			$audit->action = "add-allergy";
			$audit->target_type = "patient";
			$audit->patient_id = $this->id;
			$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
			$audit->data = $paa->getAuditAttributes();
			$audit->save();
		}
	}
	
	public function removeAllergy($allergy_id) {
		if ($paa = PatientAllergyAssignment::model()->find('patient_id=? and allergy_id=?',array($this->id,$allergy_id))) {
			if (!$paa->delete()) {
				throw new Exception('Unable to delete patient allergy assignment: '.print_r($paa->getErrors(),true));
			}

			$audit = new Audit;
			$audit->action = "remove-allergy";
			$audit->target_type = "patient";
			$audit->patient_id = $this->id;
			$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
			$audit->data = $paa->getAuditAttributes();
			$audit->save();
		}
	}

	public function assignAllergies($allergy_ids) {
		$add_allergy_ids = $allergy_ids;
		$remove_allergy_ids = array();

		// Check existing allergies
		foreach($this->allergies as $allergy) {
			if(($key = array_search($allergy->id, $insert_allergy_ids)) !== false) {
				// Allergy unchanged, don't remove or insert
				unset($insert_allergy_ids[$key]);
			} else {
				// Allergy removed
				$remove_allergy_ids[] = $allergy->id;
			}
		}

		// Insert new allergies
		$query = 'INSERT INTO `patient_allergy_assignment` (patient_id,allergy_id) VALUES (:patient_id, :allergy_id)';
		$command = Yii::app()->db->createCommand($query);
		$command->bindValue('patient_id', $this->id);
		foreach($insert_allergy_ids as $allergy_id) {
			$command->bindValue('allergy_id', $allergy_id);
			$command->execute();
		}

		// Delete removed allergies
		$query = 'DELETE from `patient_allergy_assignment` WHERE patient_id = :patient_id AND allergy_id IN (:allergy_ids)';
		$command = Yii::app()->db->createCommand($query);
		$command->bindValue('patient_id', $this->id);
		$command->bindValue('allergy_ids', implode(',',$remove_allergy_ids));
		$command->execute();
	}

	public function getAdm() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphTrOperation'))->id)) {
				if ($eo = ElementOperation::model()->find('event_id=?',array($event->id))) {
					if ($booking = $eo->booking) {
						return $booking->session->NHSDate('date');
					}
				}
			}
		}
	}

	public function getSystemicDiagnoses() {
		$criteria = new CDbCriteria;
		$criteria->compare('patient_id', $this->id);
		$criteria->join = 'join disorder on t.disorder_id = disorder.id and systemic = 1';
		$criteria->order = 'date asc';

		return SecondaryDiagnosis::model()->findAll($criteria);
	}

	public function getOphthalmicDiagnoses() {
		$criteria = new CDbCriteria;
		$criteria->compare('patient_id', $this->id);
		$criteria->join = 'join disorder on t.disorder_id = disorder.id and systemic = 0';
		$criteria->order = 'date asc';

		return SecondaryDiagnosis::model()->findAll($criteria);
	}

	public function addDiagnosis($disorder_id, $eye_id=false, $date=false) {
		if (!$date) {
			$date = date('Y-m-d');
		}

		if (!$disorder = Disorder::model()->findByPk($disorder_id)) {
			throw new Exception('Disorder not found: '.$disorder_id);
		}

		$type = $disorder->systemic ? 'systemic' : 'ophthalmic';

		if (!$sd = SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=?',array($this->id,$disorder_id))) {
			$action = "add-$type-diagnosis";
			$sd = new SecondaryDiagnosis;
			$sd->patient_id = $this->id;
			$sd->disorder_id = $disorder_id;
			$sd->eye_id = $eye_id;
			$sd->date = $date;
		} else {
			if ($sd->date == $date && (($sd->eye_id == 1 and $eye_id == 2) || ($sd->eye_id == 2 && $eye_id == 1))) {
				$action = "update-$type-diagnosis";
				$sd->eye_id = 3;
				$sd->date = $date;
			} else {
				if ($sd->eye_id == $eye_id) return;

				$action = "add-$type-diagnosis";
				$sd = new SecondaryDiagnosis;
				$sd->patient_id = $this->id;
				$sd->disorder_id = $disorder_id;
				$sd->eye_id = $eye_id;
				$sd->date = $date;
			}
		}

		if (!$sd->save()) {
			throw new Exception('Unable to save secondary diagnosis: '.print_r($sd->getErrors(),true));
		}

		$audit = new Audit;
		$audit->action = $action;
		$audit->target_type = "patient";
		$audit->patient_id = $this->id;
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->data = $sd->getAuditAttributes();
		$audit->save();
	}

	public function removeDiagnosis($diagnosis_id) {
		if (!$sd = SecondaryDiagnosis::model()->findByPk($diagnosis_id)) {
			throw new Exception('Unable to find secondary_diagnosis: '.$diagnosis_id);
		}

		if (!$disorder = Disorder::model()->findByPk($sd->disorder_id)) {
			throw new Exception('Unable to find disorder: '.$sd->disorder_id);
		}

		$type = $disorder->systemic ? 'systemic' : 'ophthalmic';

		$audit_attributes = $sd->getAuditAttributes();

		if (!$sd->delete()) {
			throw new Exception('Unable to delete diagnosis: '.print_r($sd->getErrors(),true));
		}

		$audit = new Audit;
		$audit->action = "remove-$type-diagnosis";
		$audit->target_type = "patient";
		$audit->patient_id = $this->id;
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->data = $audit_attributes;
		$audit->save();
	}

	public function getHpc() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($history = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_History')) {
					if ($history = $history->find('event_id=?',array($event->id))) {
						return strtolower($history->description);
					}
				}
			}
		}
	}

	public function getIpb() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($iop = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_IntraocularPressure')) {
					if ($iop = $iop->find('event_id=?',array($event->id))) {
						return $iop->getLetter_reading('right')." on the right, and ".$iop->getLetter_reading('left')." on the left";
					}
				}
			}
		}
	}

	public function getIpl() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($iop = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_IntraocularPressure')) {
					if ($iop = $iop->find('event_id=?',array($event->id))) {
						return $iop->getLetter_reading('left');
					} 
				}
			}
		}
	}

	public function getIpp() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($episode->eye) {
				if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
					if ($iop = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_IntraocularPressure')) {
						if ($iop = $iop->find('event_id=?',array($event->id))) {
							switch ($episode->eye_id) {
								case 1:
									return "The intraocular pressure was ".$iop->getLetter_reading('left')." in the left eye";
								case 2:
									return "The intraocular pressure was ".$iop->getLetter_reading('right')." in the right eye";
								case 3:
									return $this->ipb;
							}
						}
					}
				}
			}
		}
	}

	public function getIpr() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($iop = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_IntraocularPressure')) {
					if ($iop = $iop->find('event_id=?',array($event->id))) {
						return "The intraocular pressure is ".$iop->getLetter_reading('right')." in the right eye";
					} 
				}
			}
		}
	}

	# Bill: not for 1.1 [OE-2207]
	public function getAsb() {
	}

	public function getAsl() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_AnteriorSegment')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						return $as->left_description;
					} 
				}
			}
		}
	}

	public function getAsp() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			switch ($episode->eye_id) {
				case 1:
					return $this->asl;
				case 2:
					return $this->asr;
				case 3:
					return $this->asb;
			}
		}
	}

	public function getAsr() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_AnteriorSegment')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						return $as->right_description;
					} 
				}
			}
		}
	}

	# Bill: not for 1.1 [OE-2207]
	public function getPsb() {
	}

	public function getPsl() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_PosteriorSegment')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						return $as->left_description;
					} 
				}
			}
		}
	}

	public function getPsp() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			switch ($episode->eye_id) {
				case 1:
					return $this->asl;
				case 2:
					return $this->asr;
				case 3:
					return $this->asb;
			}
		}
	}

	public function getPsr() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_PosteriorSegment')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						return $as->right_description;
					}
				}
			}
		}
	}

	public function getVbb() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_VisualAcuity')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						return ($as->hasRight() ? $as->getBest('right') : "not recorded")." on the right and ".($as->hasLeft() ? $as->getBest('left') : "not recorded")." on the left";
					}
				}
			}
		}
	}

	public function getVbl() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_VisualAcuity')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						if ($as->hasLeft()) {
							return $as->getBest('left');
						}
					}
				}
			}
		}
	}

	public function getVbp() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			switch ($episode->eye_id) {
				case 1:
					return $this->asl;
				case 2:
					return $this->asr;
				case 3:
					return $this->asb;
			}
		}
	}

	public function getVbr() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_VisualAcuity')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						if ($as->hasRight()) {
							return $as->getBest('right');
						}
					}
				}
			}
		}
	}

	public function getCon() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_Conclusion')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						return $as->description;
					}
				}
			}
		}
	}

	public function getMan() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($as = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_Management')) {
					if ($as = $as->find('event_id=?',array($event->id))) {
						return $as->comments;
					}
				}
			}
		}
	}

	public function getContactAddress($contact_id, $location_type=false, $location_id=false) {
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

	public function getNhsnum() {
		$nhs_num = preg_replace('/[^0-9]/', '', $this->nhs_num);
		return $nhs_num ? substr($nhs_num,0,3).' '.substr($nhs_num,3,3).' '.substr($nhs_num,6,4) : 'not known';
	}

	public function hasLegacyLetters() {
		if ($model = ModuleAPI::getmodel('OphLeEpatientletter','Element_OphLeEpatientletter_EpatientLetter')) {
			return $model->find('epatient_hosnum=?',array($this->hos_num));
		}
	}

	public function getAdd() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($ac = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_AdnexalComorbidity')) {
					if ($ac = $ac->find('event_id=?',array($event->id))) {
						return $ac->right_description;
					}
				}
			}
		}
	}

	public function getAdl() {
		if ($episode = $this->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType(EventType::model()->find('class_name=?',array('OphCiExamination'))->id)) {
				if ($ac = ModuleAPI::getmodel('OphCiExamination','Element_OphCiExamination_AdnexalComorbidity')) {
					if ($ac = $ac->find('event_id=?',array($event->id))) {
						return $ac->left_description;
					}
				}
			}
		}
	}
}
