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
 * This is the model class for table "element_type".
 *
 * The followings are the available columns in table 'element_type':
 * @property integer $id
 * @property string $name
 * @property string $class_name
 * @property integer $parent_element_type_id
 *
 * The followings are the available model relations:
 * @property ElementType $parent_element_type
 * @property ElementType[] $child_element_types
 */
class ElementType extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementType the static model class
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
		return 'element_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, class_name', 'required'),
			array('name, class_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, class_name, parent_element_type_id', 'safe', 'on'=>'search'),
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
			'parent_element_type' => array(self::BELONGS_TO, 'ElementType', 'parent_element_type_id'),
			'child_element_types' => array(self::HAS_MANY, 'ElementType', 'parent_element_type_id')
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
			'class_name' => 'Class Name',
		);
	}

	/**
	 * Recursively get all children of an element type
	 * @return ElementType[]
	 */
	public function getDescendents()
	{
		$element_types = array();
		if ($child_element_types = $this->child_element_types) {
			foreach ($child_element_types as $child_element_type) {
				$element_types[] = $child_element_type;
				if ($descendents = $child_element_type->getDescendents()) {
					$element_types = array_merge($element_types, $descendents);
				}
			}
		}
		return $element_types;
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
		$criteria->compare('class_name',$this->class_name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * If the element type is a child, returns true
	 *
	 * @return bool
	 */
	public function isChild()
	{
		return ($this->parent_element_type_id) ? true : false;
	}

	/**
	 * Generator method to return a new instance of the element type class
	 *
	 * @return BaseEventTypeElement
	 */
	public function getInstance()
	{
		return new $this->class_name;
	}
}
