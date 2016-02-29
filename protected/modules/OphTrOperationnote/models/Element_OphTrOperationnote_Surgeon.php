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
 * This is the model class for table "element_procedurelist".
 *
 * The followings are the available columns in table 'element_operation':
 * @property string $id
 * @property integer $event_id
 * @property integer $surgeon_id
 * @property integer $assistant_id
 * @property integer $anaesthetic_type
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class Element_OphTrOperationnote_Surgeon extends Element_OpNote
{
	public $service;
	public $surgeonlist;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementOperation the static model class
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
		return 'et_ophtroperationnote_surgeon';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id, surgeon_id, assistant_id, supervising_surgeon_id', 'safe'),
			array('surgeon_id', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, surgeon_id, assistant_id, supervising_surgeon_id', 'safe', 'on' => 'search'),
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
			'surgeon' => array(self::BELONGS_TO, 'User', 'surgeon_id'),
			'assistant' => array(self::BELONGS_TO, 'User', 'assistant_id'),
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'supervising_surgeon' => array(self::BELONGS_TO, 'User', 'supervising_surgeon_id'),
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
			'surgeon_id' => 'Surgeon',
			'assistant_id' => 'Assistant',
			'supervising_surgeon_id' => 'Supervising surgeon',
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
		$criteria->compare('event_id', $this->event_id, true);
		$criteria->compare('surgeon_id', $this->surgeon_id);
		$criteria->compare('assistant_id', $this->assistant_id);
		$criteria->compare('supervising_surgeon_id', $this->supervising_surgeon_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

	/**
	* Set default values for forms on create
	*/
	public function setDefaultOptions()
	{
		$user = Yii::app()->session['user'];

		if ($user->is_doctor) {
			$this->surgeon_id = $user->id;
		}
	}

	/**
	 * wrapper function for retrieving surgeon list
	 *
	 * @return User[]
	 * @see User::getSurgeons()
	 */
	public function getSurgeons()
	{
		if (!$this->surgeonlist) {
			$this->surgeonlist = User::model()->getSurgeons();
		}

		return $this->surgeonlist;
	}
}
