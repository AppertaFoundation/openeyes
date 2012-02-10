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
 * This is the model class for table "address".
 *
 * The followings are the available columns in table 'address':
 * @property string $id
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $postcode
 * @property string $county
 * @property integer $country_id
 * @property string $email
 * 
 * The following are the available model relations:
 * @property Contact[] $contacts
 * @property Patient[] $patients
 * @property Country $country
 */
class Address extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Address the static model class
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
		return 'address';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		array('address1, address2, city, county', 'length', 'max'=>255),
		array('postcode', 'length', 'max'=>10),
		array('email', 'length', 'max'=>255),
		// The following rule is used by search().
		// Please remove those attributes that should not be searched.
		array('id, address1, address2, city, postcode, county, email', 'safe', 'on'=>'search'),
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
			// FIXME: An address has_many contacts and patients? Obviously possible, 
			// but I'm not sure the code ever allows more than one contact/patient to an address 
			'contacts' => array(self::HAS_MANY, 'Contact', 'address_id'),
			'patients' => array(self::HAS_MANY, 'Patient', 'address_id'),
			'country' => array(self::BELONGS_TO, 'Country', 'country_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'address1' => 'Address1',
			'address2' => 'Address2',
			'city' => 'City',
			'postcode' => 'Postcode',
			'county' => 'County',
			'email' => 'Email',
		);
	}

	/**
	 * @return string Address as formatted HTML (<br/> separated)
	 */
	public function getLetterHtml() {
		return implode('<br />', $this->getLetterArray());
	}

	/**
	 * @return string Address as text (, separated) 
	 */
	public function getLetterLine() {
		return implode(', ', $this->getLetterArray());
	}

	/**
	 * @return array Address as an array 
	 */
	public function getLetterArray() {
		$address = array();
		foreach (array('address1', 'address2', 'city', 'county', 'postcode') as $field) {
			if (!empty($this->$field)) {
				$address[] = CHtml::encode($this->$field);
			}
		}
		if(!empty($this->country->name)) {
			$address[] = CHtml::encode($this->country->name);
		}
		return $address;
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('address1',$this->address1,true);
		$criteria->compare('address2',$this->address2,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('postcode',$this->postcode,true);
		$criteria->compare('county',$this->county,true);
		$criteria->compare('country_id',$this->country_id,true);
		$criteria->compare('email',$this->email,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
