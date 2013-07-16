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
 * This is the model class for table "subspecialty".
 *
 * The followings are the available columns in table 'subspecialty':
 * @property string $id
 * @property string $name
 * @property string $class_name
 *
 * The followings are the available model relations:
 * @property EventTypeElementTypeAssignmentSubspecialtyAssignment[] $eventTypeElementTypeAssignmentSubspecialtyAssignments
 * @property ExamPhrase[] $examPhrases
 * @property LetterTemplate[] $letterTemplates
 * @property ServiceSubspecialtyAssignment[] $serviceSubspecialtyAssignments
 */
class Subspecialty extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Subspecialty the static model class
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
		return 'subspecialty';
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
			array('name, class_name', 'length', 'max'=>40),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, class_name', 'safe', 'on'=>'search'),
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
//			'eventTypeElementTypeAssignmentSubspecialtyAssignments' => array(self::HAS_MANY, 'EventTypeElementTypeAssignmentSubspecialtyAssignment', 'subspecialty_id'),
			'examPhrases' => array(self::HAS_MANY, 'ExamPhrase', 'subspecialty_id'),
			'letterTemplates' => array(self::HAS_MANY, 'LetterTemplate', 'subspecialty_id'),
			'serviceSubspecialtyAssignments' => array(self::HAS_MANY, 'ServiceSubspecialtyAssignment', 'subspecialty_id'),
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
			'name' => 'Name',
			'class_name' => 'Class Name',
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
		$criteria->compare('class_name',$this->class_name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Fetch an array of subspecialty IDs and names, by default does not return non medical subspecialties (as defined by parent specialty)
	 *
	 * @param bool $nonmedical
	 *
	 * @return array
	 */
	public function getList($nonmedical = false)
	{
		if (!$nonmedical) {
			$list = Subspecialty::model()->with('specialty')->findAll('specialty.specialty_type_id = :surgical or specialty.specialty_type_id = :medical',array(':surgical'=>1,':medical'=>2));
		} else {
			$list = Subspecialty::model()->findAll();
		}
		$result = array();

		foreach ($list as $subspecialty) {
			$result[$subspecialty->id] = $subspecialty->name;
		}

		return $result;
	}

	public function findAllByCurrentSpecialty()
	{
		if (!isset(Yii::app()->params['institution_specialty'])) {
			throw new Exception("institution_specialty code is not set in params");
		}

		if (!$specialty = Specialty::model()->find('code=?',array(Yii::app()->params['institution_specialty']))) {
			throw new Exception("Specialty not found: ".Yii::app()->params['institution_specialty']);
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition('specialty_id = :specialty_id');
		$criteria->params[':specialty_id'] = $specialty->id;
		$criteria->order = 'name asc';

		return Subspecialty::model()->findAll($criteria);
	}

	public function getTreeName()
	{
		return $this->ref_spec;
	}
}
