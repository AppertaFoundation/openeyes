<?php

/**
 * This is the model class for table "patient".
 *
 * The followings are the available columns in table 'patient':
 * @property string $id
 * @property string $pas_key
 * @property string $title
 * @property string $first_name
 * @property string $last_name
 * @property string $dob
 * @property string $gender
 * @property string $hos_num
 * @property string $nhs_num
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $postcode
 * @property string $country
 * @property string $telephone
 * @property string $mobile
 * @property string $email
 * @property string $comments
 * @property string $pmh
 * @property string $poh
 * @property string $drugs
 * @property string $allergies
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
			array('pas_key, postcode', 'length', 'max'=>10),
			array('title', 'length', 'max'=>8),
			array('first_name, last_name, hos_num, nhs_num, address1, address2, city, country', 'length', 'max'=>40),
			array('gender', 'length', 'max'=>1),
			array('telephone, mobile', 'length', 'max'=>24),
			array('email', 'length', 'max'=>60),
			array('dob, comments, pmh, poh, drugs, allergies', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('first_name, last_name, dob, hos_num, nhs_num', 'safe', 'on'=>'search'),
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
			'pasinfo' => array(self::BELONGS_TO, 'PAS_Patient', 'pas_key')
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
			'address1' => 'Address1',
			'address2' => 'Address2',
			'city' => 'City',
			'postcode' => 'Post Code',
			'country' => 'Country',
			'telephone' => 'Telephone',
			'mobile' => 'Mobile',
			'email' => 'Email',
			'comments' => 'Comments',
			'pmh' => 'PMH',
			'poh' => 'POH',
			'drugs' => 'Drugs',
			'allergies' => 'Allergies',
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

		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('dob',$this->dob,false);
		$criteria->compare('gender',$this->gender,false);
		$criteria->compare('hos_num',$this->hos_num,false);
		$criteria->compare('nhs_num',$this->nhs_num,false);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}