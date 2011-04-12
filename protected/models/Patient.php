<?php

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
class Patient extends CActiveRecord
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
			array('dob, primary_phone', 'safe'),
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
			'primary_phone' => 'Primary Phone'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('LOWER(first_name)',strtolower($this->first_name),true);
		$criteria->compare('LOWER(last_name)',strtolower($this->last_name),true);
		$criteria->compare('dob',$this->dob,false);
		$criteria->compare('gender',$this->gender,false);
		$criteria->compare('hos_num',$this->hos_num,false);
		$criteria->compare('nhs_num',$this->nhs_num,false);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
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

	private function randomData($field) 
	{
		if (Yii::app()->params['pseudonymise_patient_details'] == 'no') {
			return false;
		}

		// exceptions come first
		if ($field == 'dob') {
			return $this->randomDate();
		}
		if ($field == 'title') {
			// gender neutral
			return 'Dr';
		}

		$keyInDatafile = $field; 
		if ( ($field == 'address1') or ($field == 'address2') ) {
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
}
