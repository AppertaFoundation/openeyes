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
 * This is the model class for table "letter_template".
 *
 * The followings are the available columns in table 'letter_template':
 * @property string $id
 * @property string $name
 * @property string $phrase
 * @property string $subspecialty_id
 * @property string $send_to
 * @property string $cc
 *
 * The followings are the available model relations:
 * @property ContactType $cc0
 * @property Subspecialty $subspecialty
 * @property ContactType $send_to
 */
class LetterTemplate extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return LetterTemplate the static model class
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
		return 'letter_template';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, phrase, subspecialty_id, send_to, cc', 'required'),
			array('name', 'length', 'max'=>255),
			array('phrase', 'length', 'max'=>2047),
			array('subspecialty_id, send_to, cc', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, phrase, subspecialty_id, send_to, cc', 'safe', 'on'=>'search'),
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
			'cc0' => array(self::BELONGS_TO, 'ContactType', 'cc'),
			'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
			'sendTo' => array(self::BELONGS_TO, 'ContactType', 'send_to'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'phrase' => 'Phrase',
			'subspecialty_id' => 'Subspecialty',
			'send_to' => 'To',
			'cc' => 'Cc',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phrase',$this->phrase,true);
		$criteria->compare('subspecialty_id',$this->subspecialty_id,true);
		$criteria->compare('send_to',$this->send_to,true);
		$criteria->compare('cc',$this->cc,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getSubspecialtyText()
	{
		return $this->subspecialty->name;
	}

	public function getToText()
	{
		return $this->sendTo->name;
	}

	public function getCcText()
	{
		return $this->cc0->name;
	}

	public function getSubspecialtyOptions()
	{
		$specialties = Yii::app()->db->createCommand()
			->select('s.id, s.name')
			->from('subspecialty s')
			->order('name ASC')
			->queryAll();

		return CHtml::listData($specialties, 'id', 'name');
	}

	public function getContactTypeOptions()
	{
		$contactTypes = Yii::app()->db->createCommand()
			->select('c.id, c.name')
			->from('contact_type c')
			->order('name ASC')
			->queryAll();

		return CHtml::listData($contactTypes, 'id', 'name');
	}
}
