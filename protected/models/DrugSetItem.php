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
 * This is the model class for table "drug_set_item".
 *
 * The followings are the available columns in table 'drug_set_item':
 * @property integer $id
 * @property DrugSet $drug_set
 * @property Drug $drug
 * @property DrugSetItemTaper[] $tapers
 * @property string $dose
 * @property DrugFrequency $frequency
 * @property DrugDuration $duration
 */
class DrugSetItem extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DrugSetItem the static model class
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
		return 'drug_set_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('drug_set_id, drug_id', 'required'),
				array('dose, frequency_id, duration_id', 'safe'),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('id, drug_set_id, drug_id, dose, frequency_id, duration_id', 'safe', 'on'=>'search'),
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
				'drug' => array(self::BELONGS_TO, 'Drug', 'drug_id'),
				'drug_set' => array(self::BELONGS_TO, 'DrugSet', 'drug_set_id'),
				'frequency' => array(self::BELONGS_TO, 'DrugFrequency', 'frequency_id'),
				'duration' => array(self::BELONGS_TO, 'DrugDuration', 'duration_id'),
				'tapers' => array(self::HAS_MANY, 'DrugSetItemTaper', 'item_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
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
		$criteria->compare('drug_set_id',$this->drug_set_id,true);
		$criteria->compare('drug_id',$this->drug_id,true);
		$criteria->compare('dose',$this->dose,true);
		$criteria->compare('frequency_id',$this->frequency_id,true);
		$criteria->compare('duration_id',$this->duration_id,true);

		return new CActiveDataProvider(get_class($this), array(
				'criteria'=>$criteria,
		));
	}

}
