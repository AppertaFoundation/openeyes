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

/**
 * This is the model class for table "patient".
 *
 * The followings are the available columns in table 'patient':
 * @property string  $id
 * @property string  $pas_key
 * @property string  $title
 * @property string  $first_name
 * @property string  $last_name
 * @property string  $dob
 * @property string  $gender
 * @property string  $hos_num
 * @property string  $nhs_num
 * @property string  $primary_phone
 * @property string  $gp_id
 * @property string  $created_date
 * @property string  $last_modified_date
 * @property string  $created_user_id
 * @property string  $last_modified_user_id
 * 
 * The followings are the available model relations:
 * @property Episode[] $episodes
 * @property Address[] $addresses
 * @property Address $address Primary address
 * @property HomeAddress $address Home address
 * @property CorrespondAddress $address Correspondence address
 * @property Contact[] $contacts
 * @property Gp $gp
 */
class Patient extends BaseActiveRecord {
	
	// Set to false to supress cache refresh afterFind
	public $use_pas = true;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Patient the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
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
			array('first_name, last_name', 'required'),
			array('pas_key', 'length', 'max' => 10),
			array('title', 'length', 'max' => 8),
			array('first_name, last_name, hos_num, nhs_num, primary_phone', 'length', 'max' => 40),
			array('gender', 'length', 'max' => 1),
			array('dob, primary_phone', 'safe'),
			array('first_name, last_name, dob, hos_num, nhs_num, primary_phone', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			'episodes' => array(self::HAS_MANY, 'Episode', 'patient_id'),
			'addresses' => array(self::HAS_MANY, 'Address', 'parent_id'),
			// TODO: Add date filtering and ordering to allow fallbacks
			'address' => array(self::HAS_ONE, 'Address', 'parent_id', 'on' => "type = 'H'"),
			'homeAddress' => array(self::HAS_ONE, 'Address', 'parent_id', 'on' => "type = 'H'"),
			'correspondAddress' => array(self::HAS_ONE, 'Address', 'parent_id', 'on' => "type = 'C'"),
			'contacts' => array(self::MANY_MANY, 'Contact', 'patient_contact_assignment(patient_id, contact_id)'),
			'gp' => array(self::BELONGS_TO, 'Gp', 'gp_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'pas_key' => 'PAS Key',
			'title' => 'Title',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'dob' => 'Date of Birth',
			'gender' => 'Gender',
			'hos_num' => 'Hospital Number',
			'nhs_num' => 'NHS Number',
			'primary_phone' => 'Primary Phone',
		);
	}

	public function search_nr()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('LOWER(first_name)',strtolower($this->first_name),false);
		$criteria->compare('LOWER(last_name)',strtolower($this->last_name),false);
		$criteria->compare('dob',$this->dob,false);
		$criteria->compare('gender',$this->gender,false);
		$criteria->compare('hos_num',$this->hos_num,false);
		$criteria->compare('nhs_num',$this->nhs_num,false);

		return Patient::model()->count($criteria);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($params=false)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		if (!is_array($params)) {
			$params = array(
				'items_per_page' => PHP_INT_MAX,
				'currentPage' => 0
			);
		}

		$criteria=new CDbCriteria;

		$criteria->compare('LOWER(first_name)',strtolower($this->first_name),false);
		$criteria->compare('LOWER(last_name)',strtolower($this->last_name),false);
		$criteria->compare('dob',$this->dob,false);
		$criteria->compare('gender',$this->gender,false);
		$criteria->compare('hos_num',$this->hos_num,false);
		$criteria->compare('nhs_num',$this->nhs_num,false);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination' => array('pageSize' => $params['items_per_page'], 'currentPage' => $params['currentPage'])
		));
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

	public function getAge()
	{
		$age = date('Y') - substr($this->dob, 0, 4);
		$birthDate = substr($this->dob, 5, 2) . substr($this->dob, 8, 2);
		if (date('md') < $birthDate) {
			$age--; // birthday hasn't happened yet this year
		}
		return $age;
	}

	/**
	 * @return boolean Is patient a child?
	 */
	public function isChild() {
		return ($this->getAge() < 16);
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
		return implode(' ',array($this->title, $this->first_name, $this->last_name));
	}

	public function getDisplayName() {
		return '<span class="surname">'.$this->last_name.'</span>, <span class="given">'.$this->first_name.'</span>';
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

	public function loadGP() {
		if ($this->gp_id === NULL) {
			if (Yii::app()->params['use_pas']) {
				$service = new GpService;
				$service->GetPatientGp($this->id);
			} else {
				return false;
			}
		} else {
			return Gp::model()->noPas()->findByPk($this->gp_id);
		}
	}
	
	/**
	 * Supress PAS call after find
	 */
	public function noPas() {
		$this->use_pas = false;
		return $this;
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
	 * Update from PAS if enabled
	 * @see CActiveRecord::afterFind()
	 */
	protected function afterFind() {
		parent::afterFind();
		if($this->use_pas && Yii::app()->params['use_pas'] && strtotime($this->last_modified_date) < (time() - self::PAS_CACHE_TIME)) {
			Yii::log('Patient details stale', 'trace');
			$patient_service = new PatientService($this);
			$patient_service->loadFromPas();
		}
	}
	
}
