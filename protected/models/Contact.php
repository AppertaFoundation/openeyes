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
 * This is the model class for table "contact".
 *
 * The following are the available columns in table 'contact':
 * @property integer $id
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
 * @property Address $homeAddress Home address
 * @property Address $correspondAddress Correspondence address
 * @property ContactLabel $label
 *
 * The following are pseudo (calculated) fields
 * @property string $salutationName
 * @property string $fullName
 */
class Contact extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Contact the static model class
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
		return 'contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('nick_name', 'length', 'max' => 80),
			array('title, first_name, last_name, nick_name, primary_phone, qualifications, contact_label_id', 'safe'),
			array('id, nick_name, primary_phone, title, first_name, last_name, qualifications', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'consultant' => array(self::HAS_ONE, 'Consultant', 'contact_id'),
			'gp' => array(self::HAS_ONE, 'Gp', 'contact_id'),
			'addresses' => array(self::HAS_MANY, 'Address', 'parent_id',
				'on' => "parent_class = 'Contact'"
			),
			// Prefer H records for primary address, but fall back to others
			'address' => array(self::HAS_ONE, 'Address', 'parent_id',
				'on' => "parent_class = 'Contact'",
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id," . AddressType::HOME . ") DESC, date_start DESC"
			),
			// Prefer H records for home address, but fall back to others
			'homeAddress' => array(self::HAS_ONE, 'Address', 'parent_id',
				'on' => "parent_class = 'Contact'",
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id," . AddressType::HOME . ") DESC, date_start DESC"
			),
			// Prefer C records for correspond address, but fall back to others
			'correspondAddress' => array(self::HAS_ONE, 'Address', 'parent_id',
				'order' => "((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id," . AddressType::CORRESPOND . ") DESC, date_start DESC",
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
			'contact_label_id' => 'Label',
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
	public function getFullName()
	{
		return trim(implode(' ',array($this->title, $this->first_name, $this->last_name)));
	}

	public function getReversedFullName()
	{
		return trim(implode(' ',array($this->title, $this->last_name, $this->first_name)));
	}

	public function getCorrespondenceName()
	{
		return $this->getFullName();
	}
	
	/**
	 * @return string Salutaion name
	 */
	public function getSalutationName()
	{
		return $this->title . ' ' . $this->last_name;
	}

	public function contactLine($location=false)
	{
		$line = $this->fullName.' ('.$this->label->name;
		if ($location) $line .= ', '.$location;
		return $line.')';
	}

	public function findByLabel($term, $label, $exclude=false)
	{
		if (!$cl = ContactLabel::model()->find('name=?',array($label))) {
			throw new Exception("Unknown contact label: $label");
		}

		$contacts = array();

		$criteria = new CDbCriteria;
		$criteria->addSearchCondition('lower(last_name)',$term,false);
		if ($exclude) {
			$criteria->compare('contact_label_id','<>'.$cl->id);
		} else {
			$criteria->compare('contact_label_id',$cl->id);
		}
		$criteria->order = 'title, first_name, last_name';

		foreach (Contact::model()->findAll($criteria) as $contact) {
			if ($contact->locations) {
				foreach ($contact->locations as $location) {
					$contacts[] = array(
						'line' => $contact->contactLine($location),
						'contact_location_id' => $location->id,
					);
				}
			} else {
				$contacts[] = array(
					'line' => $contact->contactLine(),
					'contact_id' => $contact->id,
				);
			}
		}

		return $contacts;
	}

	public function getType()
	{
		foreach (array('User','Gp','Patient','Person') as $model) {
			if ($model::model()->find('contact_id=?',array($this->id))) {
				return $model;
			}
		}

		return false;
	}
}
