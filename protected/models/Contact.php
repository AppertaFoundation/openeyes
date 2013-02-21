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
 * This is the model class for table "Contact".
 *
 * The following are the available columns in table 'Contact':
 * @property string $id
 * @property string $nick_name
 * @property string $primary_phone
 * @property string $title
 * @property string $first_name
 * @property string $last_name
 * @property string $qualifications
 * 
 * The following are the available model relations:
 * @property Gp $gp
 * @property Consultant $consultant
 * @property Address[] $addresses
 * @property Address $address Primary address
 * @property HomeAddress $homeAddress Home address
 * @property CorrespondAddress $correspondAddress Correspondence address
 * 
 * The following are pseudo (calculated) fields
 * @property string $SalutationName
 * @property string $FullName
 */
class Contact extends BaseActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @return Contact the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('nick_name', 'length', 'max' => 80),
			array('id, nick_name, primary_phone, title, first_name, last_name, qualifications', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			'consultant' => array(self::HAS_ONE, 'Consultant', 'contact_id'),
			'gp' => array(self::HAS_ONE, 'Gp', 'contact_id'),
			'addresses' => array(self::HAS_MANY, 'Address', 'parent_id',
				'on' => "parent_class = 'Contact'"
			),
			// Prefer H records for primary address, but fall back to others
			'address' => array(self::HAS_ONE, 'Address', 'parent_id',
				'on' => "parent_class = 'Contact'",
				'order' => "FIELD(type,'H') DESC, date_start DESC"
			),
			// Prefer H records for home address, but fall back to others
			'homeAddress' => array(self::HAS_ONE, 'Address', 'parent_id',
				'on' => "parent_class = 'Contact'",
				'order' => "FIELD(type,'H') DESC, date_start DESC"
			),
			// Prefer C records for correspond address, but fall back to others
			'correspondAddress' => array(self::HAS_ONE, 'Address', 'parent_id',
				'order' => "FIELD(type,'C') DESC, date_start DESC",
				'on' => "parent_class = 'Contact'",
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nick_name' => 'Nick Name',
 			'primary_phone' => 'Primary Phone Number',
 			'title' => 'Title',
 			'first_name' => 'First Name',
 			'last_name' => 'Last Name',
 			'qualifications' => 'Qualifications',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('nick_name',$this->nick_name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return string Full name
	 */
	public function getFullName() {
		return trim(implode(' ',array($this->title, $this->first_name, $this->last_name)));
	}
	
	public function getReversedFullName() {
		return trim(implode(' ',array($this->title, $this->last_name, $this->first_name)));
	}

	/**
	 * @return string Salutaion name
	 */
	public function getSalutationName() {
		return $this->title . ' ' . $this->last_name;
	}

	public function getLetterAddress() {
		$address = $this->fullName;

		if (isset($this->qualifications)) {
			$address .= ' '.$this->qualifications;
		}

		$address .= "\n";

		if ($this->address) {
			$address .= implode("\n",$this->address->getLetterArray());
		}

		return $address;
	}

	public function findAllByParentClass($parent_class) {
		$criteria = new CDbCriteria;
		$criteria->compare('parent_class',$parent_class);
		$criteria->order = 'first_name, last_name';

		return Contact::Model()->findAll($criteria);
	}

	public function getPrefix() {
		if ($this->parent_class == 'Gp') {
			return 'GP';
		}

		if (UserContactAssignment::model()->find('contact_id=?',array($this->id)) && $this->parent_class != 'Consultant') {
			return '';
		}
		return 'Consultant';
	}
}
