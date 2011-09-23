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
 * This is the model class for table "element_nsc_grade".
 *
 * The followings are the available columns in table 'element_nsc_grade':
 * @property string $id
 * @property string $event_id
 * @property integer $retinopathy_grade_id
 * @property integer $maculopathy_grade_id
 */
class ElementNSCGrade extends BaseElement
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementNSCGrade the static model class
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
		return 'element_nsc_grade';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id', 'length', 'max'=>10),
			array('retinopathy_grade_id, maculopathy_grade_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, grade', 'safe', 'on'=>'search'),
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
			'retinopathyGrade' => array(self::BELONGS_TO, 'NSCGrade', 'retinopathy_grade_id'),
			'maculopathyGrade' => array(self::BELONGS_TO, 'NSCGrade', 'maculopathy_grade_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
			'retinopathy_grade_id' => 'Retinopathy Grade',
			'maculopathy_grade_id' => 'Maculopathy Grade',
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
		$criteria->compare('event_id',$this->event_id,true);
		$criteria->compare('retinopathy_grade_id',$this->retinopathy_grade_id,true);
		$criteria->compare('maculopathy_grade_id',$this->maculopathy_grade_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Select NSC Grade Options for select dropdown based on type
	 * 
	 * @param type $gradeType  retinopathy or maculopathy
	 * @return array
	 */
	public function getSelectOptions($gradeType)
	{
		$criteria = new CDbCriteria;
		$criteria->select = 'id, name';
		$criteria->compare('type', $gradeType);
		$options = NSCGrade::model()->findAll($criteria);
		
		$result = array();
		foreach ($options as $option) {
			$result[$option->id] = $option->name;
		}
		return $result;
	}
}