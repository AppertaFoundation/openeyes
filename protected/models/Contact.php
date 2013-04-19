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
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id,2) DESC, date_start DESC"
			),
			// Prefer H records for home address, but fall back to others
			'homeAddress' => array(self::HAS_ONE, 'Address', 'parent_id',
				'on' => "parent_class = 'Contact'",
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id,2) DESC, date_start DESC"
			),
			// Prefer C records for correspond address, but fall back to others
			'correspondAddress' => array(self::HAS_ONE, 'Address', 'parent_id',
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id,3) DESC, date_start DESC",
				'on' => "parent_class = 'Contact'",
			),
			'label' => array(self::BELONGS_TO, 'ContactLabel', 'contact_label_id'),
			'locations' => array(self::HAS_MANY, 'ContactLocation', 'contact_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nick_name' => 'Nickname',
			'primary_phone' => 'Phone number',
			'title' => 'Title',
			'first_name' => 'First name',
			'last_name' => 'Last name',
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

	public function contactLine($location) {
		return $this->fullName.' ('.$this->label->name.', '.$location.')';
	}

	static public function contactsByLabel($term, $label=false, $exclude=false) {
		if ($label) {
			if (!is_object($label)) {
				if (!$label = ContactLabel::model()->find('name=?',array($label))) {
					throw new Exception("Label not found");
				}
			}
		}

		$criteria = new CDbCriteria;
		$criteria->addSearchCondition('lower(last_name)',$term,false);
		if ($label) {
			if ($exclude) {
				$criteria->compare('contact_label_id','<>'.$label->id);
			} else {
				$criteria->compare('contact_label_id',$label->id);
			}
		}
		$criteria->order = 'title, first_name, last_name';

		foreach (Contact::model()->findAll($criteria) as $contact) {
			foreach ($contact->locations as $location) {
				$contacts[] = array(
					'line' => $contact->contactLine($location),
					'contact_location_id' => $location->id,
				);
			}
		}

		return $contacts;
	}

	static public function contactsByModel($term, $model) {
		$contacts = array();

		$criteria = new CDbCriteria;
		$criteria->addSearchCondition("lower(`contact`.last_name)",$term,false);
		if ($model == 'User') {
			$criteria->compare('active',1);
		}
		$criteria->order = 'contact.title, contact.first_name, contact.last_name';

		foreach ($model::model()->with(array('contact' => array('with' => 'locations')))->findAll($criteria) as $object) {
			foreach ($object->contact->locations as $location) {
				$contacts[] = array(
					'line' => $object->contact->contactLine($location),
					'contact_location_id' => $location->id,
				);
			}
		}

		return $contacts;
	}

	public function getType() {
		foreach (array('User','Gp','Patient','Person') as $model) {
			if ($model::model()->find('contact_id=?',array($this->id))) {
				return $model;
			}
		}

		return false;
	}
}
