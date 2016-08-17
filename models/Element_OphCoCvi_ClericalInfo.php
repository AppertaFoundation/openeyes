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
 * @property OphCoCvi_ClericalInfo_PatientFactor $factor
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
			array('employment_status_id, preferred_info_fmt_id, contact_urgency_id, preferred_language_id, social_service_comments, ', 'required', 'on' => 'finalise'),
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
			'employment_status' => array(self::BELONGS_TO, 'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_EmploymentStatus', 'employment_status_id'),
			'preferred_info_fmt' => array(self::BELONGS_TO, 'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt', 'preferred_info_fmt_id'),
			'contact_urgency' => array(self::BELONGS_TO, 'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_ContactUrgency', 'contact_urgency_id'),
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


	protected function afterSave()
	{
		if (!empty($_POST['ophcocvi_clinicinfo_patient_factor_id'])) {
			$existing_ids = array();
			foreach (OphCoCvi_ClericalInfo_PatientFactor_Answer::model()->findAll('element_id = :elementId', array(':elementId' => $this->id)) as $item) {
				$existing_ids[] = $item->ophcocvi_clinicinfo_patient_factor_id;
			}
			foreach ($_POST['ophcocvi_clinicinfo_patient_factor_id'] as $id) {
				if (!in_array($id,$existing_ids) && isset($_POST['is_factor'][$id])) {
					$item = new OphCoCvi_ClericalInfo_PatientFactor_Answer;
					$item->element_id = $this->id;
					$item->ophcocvi_clinicinfo_patient_factor_id = $id;
					$item->is_factor = $_POST['is_factor'][$id];
					if($_POST['require_comments'][$id] ==1)
					{
						$item->comments = $_POST['comments'][$id];
					}
					if (!$item->save()) {
						throw new Exception('Unable to save patient factor : '.print_r($item->getErrors(),true));
					}
				}
			}
			foreach ($existing_ids as $id) {
				if (!in_array($id,$_POST['ophcocvi_clinicinfo_patient_factor_id'])) {
					$item = OphCoCvi_ClericalInfo_PatientFactor_Answer::model()->find('element_id = :elementId and ophcocvi_clinicinfo_patient_factor_id = :lookupfieldId',array(':elementId' => $this->id, ':lookupfieldId' => $id));
					if (!$item->delete()) {
						throw new Exception('Unable to delete patient factor: '.print_r($item->getErrors(),true));
					}
				}
			}
		}
		return parent::afterSave();
	}


	/**
	 * To generate the employement status array for the pdf
	 *
	 * @return array
	 */
	public function generateEmployementStatus() {
		$data = array();
		$employement_status = (OphCoCvi_ClericalInfo_EmploymentStatus::model()->findAll('`active` = ?',array(1),array('order' => 'display_order asc')));
		if( sizeof($employement_status ) > 1 ){
			$data[] = "Is the patient:";
			foreach($employement_status  as $employement){

				for($i=0; $i<sizeof($employement)/2; $i++)
					$data[] =  $employement->name;
				$data[] = ($this->employment_status_id === $employement->id) ? 'X' : '';

			}
		}
		return $data;
	}

	/**
	 * To generate the preferred info format array for the pdf
	 *
	 * @return array
	 */
	public function generatePreferredInfoFormat() {
		$data = array();
		$preferredInfoFormats = (OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll('`require_email` = ?',array(0),array('order' => 'display_order asc')));
		foreach($preferredInfoFormats as $key => $preferredInfoFormat) {
			if ($key != 4) {
				for ($i = 0; $i < sizeof($preferredInfoFormat) / 2; $i++) {
					$data[] = ($this->preferred_info_fmt_id === $preferredInfoFormat->id) ? 'X' : '';
					$data[] = $preferredInfoFormat->name;
				}
			}
		}
		$preferredInfoFormatEmail= (OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll('`require_email` = ?',array(1),array('order' => 'display_order asc')));
		$emailData = array();
		foreach($preferredInfoFormatEmail as $key => $infoEmail) {
			for ($j = 0; $j < sizeof($infoEmail) / 2; $j++) {
				$emailData[] = ($this->info_email === "") ? '' : 'X';
				$emailData[] = $infoEmail->name;
				$emailData[] = $this->info_email ;
			}
		}
		return [$data,$emailData];
	}

	/**
	 * To generate the preferred language array for the pdf
	 *
	 * @return array
	 */
	public function generatePreferredLanguage() {
		$data = array();
		$preferredLanguages = (\Language::model()->findAll());
		foreach($preferredLanguages as $preferredLanguage) {
			$key = $preferredLanguage->name;
			$data[][$key] = ($this->preferred_language_id === $preferredLanguage->id) ? 'X' : '';
		}
		return $data;
	}

	/**
	 * To generate the contact urgency array for the pdf
	 *
	 * @return array
	 */
	public function generateContactUrgency() {
		$data = array();
		$contactUrgencies = (OphCoCvi_ClericalInfo_ContactUrgency::model()->findAll(array('order' => 'display_order asc')));
		foreach($contactUrgencies as $contactUrgency) {
			$key = $contactUrgency->name;
			$data[]= array(($this->contact_urgency_id === $contactUrgency->id) ? 'X' : '',$key);
		}
		return $data;
	}


	/**
	 * Returns an associative array of the data values for printing
	 */
	public function getStructuredDataForPrint()
	{
		$result = array();
		foreach (OphCoCvi_ClinicalInfo_PatientFactor::model()->findAll('`active` = ?',array(1)) as $factor) {
			$is_factor = OphCoCvi_ClericalInfo_PatientFactor_Answer::model()->getFactorAnswer($factor->id,$this->id);
			if($is_factor == 1){$isFactor = "Y";}
			if($is_factor == 0){$isFactor = "N";}
			if($is_factor == 2){$isFactor = "";}
			$result['patientFactor'][] = array($factor->name, $isFactor);
		}
		$result['employmentStatus'][] = $this->generateEmployementStatus();
		$result['contactUrgency'] = $this->generateContactUrgency();
		$result['preferredInfoFormat'] = $this->generatePreferredInfoFormat();
		//$result['preferredLanguage'] = $this->generatePreferredLanguage();
		$result['socialServiceComments'] = $this->social_service_comments;
		return $result;
	}

}
?>