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
 * This is the model class for table "et_ophtroperationnote_personnel".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_personnel':
 * @property string $id
 * @property integer $event_id
 * @property integer $scrub_nurse_id
 * @property integer $floor_nurse_id
 * @property integer $accompanying_nurse_id
 * @property integer $operating_department_practitioner_id
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class Element_OphTrOperationnote_Personnel extends Element_OpNote
{
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
		return 'et_ophtroperationnote_personnel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id, scrub_nurse_id, floor_nurse_id, accompanying_nurse_id, operating_department_practitioner_id', 'safe'),
			array('scrub_nurse_id, floor_nurse_id, accompanying_nurse_id, operating_department_practitioner_id', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, scrub_nurse_id, floor_nurse_id, accompanying_nurse_id, operating_department_practitioner_id', 'safe', 'on' => 'search'),
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
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'scrub_nurse' => array(self::BELONGS_TO, 'PersonnelScrubNurse', 'scrub_nurse_id'),
			'floor_nurse' => array(self::BELONGS_TO, 'PersonnelFloorNurse', 'floor_nurse_id'),
			'accompanying_nurse' => array(self::BELONGS_TO, 'PersonnelAccompanyingNurse', 'accompanying_nurse_id'),
			'operating_department_practitioner' => array(self::BELONGS_TO, 'PersonnelOperatingDepartmentPractitioner', 'operating_department_practitioner_id'),
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
			'scrub_nurse_id' => 'Scrub nurse',
			'floor_nurse_id' => 'Floor nurse',
			'accompanying_nurse_id' => 'Accompanying nurse',
			'operating_department_practitioner_id' => 'Operating department practitioner',
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

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

	public function getScrub_nurses()
	{
		return Contact::model()->findAllByParentClass('OphTrOperationnote_Personnel_scrub_nurses');
	}

	public function getFloor_nurses()
	{
		return Contact::model()->findAllByParentClass('OphTrOperationnote_Personnel_floor_nurses');
	}

	public function getAccompanying_nurses()
	{
		return Contact::model()->findAllByParentClass('OphTrOperationnote_Personnel_accompanying_nurses');
	}

	public function getOperating_department_practitioners()
	{
		return Contact::model()->findAllByParentClass('OphTrOperationnote_Personnel_operating_department_practitioners');
	}
}
