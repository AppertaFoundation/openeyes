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
 * This is the model class for table "drug".
 *
 * The followings are the available columns in table 'drug':
 * @property integer $id
 * @property string $name
 * @property string $tallman
 * @property string $label
 * @property string $aliases
 * @property string $dose_unit
 * @property string $default_dose
 * @property integer $preservative_free
 *
 * @property Allergy[] $allergies
 * @property DrugType $type
 * @property DrugForm $form
 * @property DrugRoute $default_route
 * @property DrugFrequency $default_frequency
 * @property DrugDuration $default_duration
 */
class Drug extends BaseActiveRecordVersioned
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Drug the static model class
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
		return 'drug';
	}

	public function defaultScope()
	{
		return array('order' => $this->getTableAlias(true, false) . '.name');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, tallman', 'required'),
			array('name', 'unsafe', 'on' => 'update'),
			array('tallman, dose_unit, default_dose, preservative_free, type_id, form_id, default_duration_id, default_frequency_id, default_route_id, aliases', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, tallman, dose_unit, default_dose, preservative_free, type_id, form_id, default_duration_id, default_frequency_id, default_route_id, aliases', 'safe', 'on'=>'search'),
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
				'allergies' => array(self::MANY_MANY, 'Allergy', 'drug_allergy_assignment(drug_id, allergy_id)'),
				'type' => array(self::BELONGS_TO, 'DrugType', 'type_id'),
				'form' => array(self::BELONGS_TO, 'DrugForm', 'form_id'),
				'default_duration' => array(self::BELONGS_TO, 'DrugDuration', 'default_duration_id'),
				'default_frequency' => array(self::BELONGS_TO, 'DrugFrequency', 'default_frequency_id'),
				'default_route' => array(self::BELONGS_TO, 'DrugRoute', 'default_route_id'),
				'subspecialtyAssignments' => array(self::HAS_MANY, 'SiteSubspecialtyDrug', 'drug_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'type_id' => 'Type',
			'default_duration_id' => 'Default Duration',
			'default_frequency_id' => 'Default Frequency',
			'default_route_id' => 'Default Route',
		);
	}

	public function behaviors()
	{
		return array(
			'LookupTable' => 'LookupTable',
		);
	}

	public function getLabel()
	{
		if ($this->preservative_free) {
			return $this->name . ' (No Preservative)';
		} else {
			return $this->name;
		}
	}

	public function getTallmanLabel()
	{
		if ($this->preservative_free) {
			return $this->tallman . ' (No Preservative)';
		} else {
			return $this->tallman;
		}
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

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function listBySubspecialty($subspecialty_id)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('subspecialty_id',$subspecialty_id);
		$criteria->order = 'name asc';

		return CHtml::listData(Drug::model()->with('subspecialtyAssignments')->findAll($criteria),'id','name');
	}
}
