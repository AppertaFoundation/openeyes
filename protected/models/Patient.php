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
 * @property integer $address_id
 * @property string  $primary_phone
 */
class Patient extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Patient the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('first_name, last_name', 'required'),
			array('pas_key', 'length', 'max'=>10),
			array('title', 'length', 'max'=>8),
			array('first_name, last_name, hos_num, nhs_num, primary_phone', 'length', 'max'=>40),
			array('gender', 'length', 'max'=>1),
			array('dob, primary_phone, address_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('first_name, last_name, dob, hos_num, nhs_num, primary_phone', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'episodes' => array(self::HAS_MANY, 'Episode', 'patient_id'),
			'address' => array(self::BELONGS_TO, 'Address', 'id'),
			'contacts' => array(self::MANY_MANY, 'Contact', 'patient_contact_assignment(patient_id, contact_id)'),
			'gp' => array(self::HAS_ONE, 'Gp', 'gp_id')
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
			'title' => 'Title',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'dob' => 'Date of Birth',
			'gender' => 'Gender',
			'hos_num' => 'Hospital Number',
			'nhs_num' => 'NHS Number',
			'primary_phone' => 'Primary Phone',
			'address_id' => 'Address'
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

	public function isChild() {
		return ($this->getAge() < 16);
	}

	public function getAddressName() {
		if ($this->isChild()) {
			return 'Parent/Guardian of ' . $this->getFullName();
		} else {
			return $this->getFullName();
		}	
	}
	
	public function getSalutationName() {
		if ($this->isChild()) {
			return 'Parent/Guardian of ' . $this->first_name . ' ' . $this->last_name;
		} else {
			return $this->title . ' ' . $this->last_name;
		}	
	}
	
	public function getFullName() {
		return implode(' ',array($this->title, $this->first_name, $this->last_name));
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

	public function getGP() {
		if ($this->gp_id === NULL) {
			if (Yii::app()->params['use_pas']) {
				$service = new GpService;
				$service->GetPatientGp($this->id);
			} else {
				return false;
			}
		} else {
			return Gp::Model()->findByPk($this->gp_id);
		}
	}
}
