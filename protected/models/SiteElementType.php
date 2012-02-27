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
 * This is the model class for table "site_element_type".
 *
 * The followings are the available columns in table 'site_element_type':
 * @property string $id
 * @property string $possible_element_type_id
 * @property string $specialty_id
 * @property integer $required
 * @property integer $view_number
 * @property integer $first_in_episode
 *
 * The followings are the available model relations:
 * @property EventTypeElementTypeAssignment $eventTypeElementTypeAssignment
 * @property Specialty $specialty
 */
class SiteElementType extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SiteElementType the static model class
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
		return 'site_element_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('possible_element_type_id, specialty_id', 'required'),
			array('first_in_episode', 'numerical', 'integerOnly'=>true),
			array('possible_element_type_id, specialty_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('required', 'safe'),
			array('view_number', 'safe'),
			array('id, possible_element_type_id, specialty_id, first_in_episode, required, view_number', 'safe', 'on'=>'search'),
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
			'possibleElementType' => array(self::BELONGS_TO, 'PossibleElementType', 'possible_element_type_id'),
			'specialty' => array(self::BELONGS_TO, 'Specialty', 'specialty_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'possible_element_type_id' => 'Possible element type',
			'specialty_id' => 'Specialty',
			'first_in_episode' => 'First In Episode',
			'view_number' => 'View number',
			'required' => 'Required'
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
		$criteria->compare('possible_element_type_id',$this->possible_element_type_id,true);
		$criteria->compare('specialty_id',$this->specialty_id,true);
		$criteria->compare('first_in_episode',$this->first_in_episode);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getNumViewsArray()
	{
		$viewsList = array();
		for ($counter=0;$counter<$this->possibleElementType->num_views;$counter++) {
			$viewsList[$counter+1] = $counter+1;
		}
		return $viewsList;
	}
}
