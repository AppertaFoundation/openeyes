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

class Element_OphCoCvi_ClericalInfo_PatientFactor_Answer extends \BaseEventTypeElement
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
		return 'et_ophcocvi_clericinfo_patient_factor_answer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('element_id, patient_factor_id', 'safe'),
			array('element_id, patient_factor_id', 'required'),
			array('id, element_id, patient_factor_id', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'element' => array(self::BELONGS_TO, 'Element_OphCoCvi_ClericalInfo_PatientFactor_Answer', 'element_id'),
			'patient_factor_id' => array(self::BELONGS_TO, 'OphCoCvi_ClinicalInfo_PatientFactor', 'patient_factor_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
		);
	}



}
?>