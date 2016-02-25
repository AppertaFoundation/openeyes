<?php /**
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
 * This is the model class for table "ophtroperationbooking_operation_erod".
 *
 * The followings are the available columns in table:
 * @property integer $id
 * @property integer $element_id
 * @property integer $session_id
 * @property date $session_date
 * @property time $session_start_time
 * @property time $session_end_time
 * @property integer $firm_id
 * @property boolean $consultant
 * @property boolean $paediatric
 * @property boolean $anaesthetist
 * @property boolean $general_anaesthetic
 * @property integer $session_duration
 * @property integer $total_operations_time
 * @property integer $available_time
 *
 * The followings are the available model relations:
 *
 * @property OphTrOperationbooking_Operation_Sequence $session
 * @property OphTrOperationbooking_Operation_Theatre $theatre
 *
 */

class OphTrOperationbooking_Operation_EROD extends BaseActiveRecordVersioned
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ophtroperationbooking_operation_erod';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('element_id, session_id, session_date, session_start_time, firm_id, consultant, paediatric, anaesthetist, general_anaesthetic, session_duration, total_operations_time, available_time', 'safe'),
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
			'operation' => array(self::BELONGS_TO, 'Element_OphTrOperationbooking_Operation', 'element_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
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

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	public function getFirmName()
	{
		return $this->firm->name . ' (' . $this->firm->serviceSubspecialtyAssignment->subspecialty->name . ')';
	}

	public function getTimeSlot()
	{
		return date('H:i',strtotime($this->session_start_time)) . ' - ' . date('H:i',strtotime($this->session_end_time));
	}

	public function getDescription()
	{
		return $this->NHSDate('session_date').' '.$this->getTimeSlot() .', '. $this->getFirmName();
	}
}
