<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "et_ophcocvi_clericinfo".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property integer $employment_status_id
 * @property integer $preferred_info_fmt_id
 * @property string $info_email
 * @property integer $contact_urgency_id
 * @property integer $preferred_language_id
 * @property string $social_service_comments
 *
 * The followings are the available model relations:
 *
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphCoCvi_ClericalInfo_EmploymentStatus $employment_status
 * @property OphCoCvi_ClericalInfo_PreferredInfoFmt $preferred_info_fmt
 * @property OphCoCvi_ClericalInfo_ContactUrgency $contact_urgency
 * @property Language $preferred_language
 */

class Element_OphCoCvi_ClericalInfo extends \BaseEventTypeElement
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
		return 'et_ophcocvi_clericinfo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('event_id, employment_status_id, preferred_info_fmt_id, info_email, contact_urgency_id, preferred_language_id, social_service_comments, ', 'safe'),
			array('employment_status_id, preferred_info_fmt_id, contact_urgency_id, preferred_language_id, social_service_comments, ', 'required'),
			array('id, event_id, employment_status_id, preferred_info_fmt_id, info_email, contact_urgency_id, preferred_language_id, social_service_comments, ', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'employment_status' => array(self::BELONGS_TO, 'OphCoCvi_ClericalInfo_EmploymentStatus', 'employment_status_id'),
			'preferred_info_fmt' => array(self::BELONGS_TO, 'OphCoCvi_ClericalInfo_PreferredInfoFmt', 'preferred_info_fmt_id'),
			'contact_urgency' => array(self::BELONGS_TO, 'OphCoCvi_ClericalInfo_ContactUrgency', 'contact_urgency_id'),
			'preferred_language' => array(self::BELONGS_TO, 'Language', 'preferred_language_id'),
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
			'employment_status_id' => 'Employment status',
			'preferred_info_fmt_id' => 'Preferred information format',
			'info_email' => 'Info email',
			'contact_urgency_id' => 'Contact urgency',
			'preferred_language_id' => 'Preferred language',
			'social_service_comments' => 'Social service comments',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);
		$criteria->compare('employment_status_id', $this->employment_status_id);
		$criteria->compare('preferred_info_fmt_id', $this->preferred_info_fmt_id);
		$criteria->compare('info_email', $this->info_email);
		$criteria->compare('contact_urgency_id', $this->contact_urgency_id);
		$criteria->compare('preferred_language_id', $this->preferred_language_id);
		$criteria->compare('social_service_comments', $this->social_service_comments);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}
}
?>