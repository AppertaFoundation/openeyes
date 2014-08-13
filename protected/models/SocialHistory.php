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
 * @property string $id
 * @property integer $event_id
 * @property integer $occupation_id
 * @property integer $driving_status_id
 * @property integer $smoking_status_id
 * @property integer $accommodation_id
 * @property string $comments
 * @property string $type_of_job
 * @property integer $carer_id
 * @property integer $alcohol_intake
 * @property integer $substance_misuse_id
 *
 * relations:
 * @property User $user
 * @property Patient $patient
 * @property User $usermodified
 * @property SocialHistoryOccupation $occupation
 * @property SocialHistoryDrivingStatus $driving_status
 * @property SocialHistorySmokingStatus $smoking_status
 * @property SocialHistoryAccommodation $accommodation
 * @property SocialHistoryCarer $carer
 * @property SocialHistorySubstanceMisuse $substance_misuse
 */

class SocialHistory  extends  BaseActiveRecordVersioned
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'socialhistory';
	}

	public function rules()
	{
		return array(
			array('event_id, occupation_id, driving_status_id, accommodation_id, comments, type_of_job, carer_id, alcohol_intake, substance_misuse_id, ', 'safe'),
			array('smoking_status_id','required'),
			array('id, event_id, occupation_id, driving_status_id, smoking_status_id, accommodation_id, comments, type_of_job, carer_id, alcohol_intake, substance_misuse_id, ', 'safe', 'on' => 'search'),
			array('alcohol_intake', 'default', 'setOnEmpty' => true, 'value' => null),
		);
	}

	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'occupation' => array(self::BELONGS_TO, 'SocialHistoryOccupation', 'occupation_id'),
			'driving_status' => array(self::BELONGS_TO, 'SocialHistoryDrivingStatus', 'driving_status_id'),
			'smoking_status' => array(self::BELONGS_TO, 'SocialHistorySmokingStatus', 'smoking_status_id'),
			'accommodation' => array(self::BELONGS_TO, 'SocialHistoryAccommodation', 'accommodation_id'),
			'carer' => array(self::BELONGS_TO, 'SocialHistoryCarer', 'carer_id'),
			'substance_misuse' => array(self::BELONGS_TO, 'SocialHistorySubstanceMisuse', 'substance_misuse_id'),
			'substance_misuse' => array(self::BELONGS_TO, 'SocialHistorySubstanceMisuse', 'substance_misuse_id'),
			'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'occupation_id' => 'Employment Status',
			'driving_status_id' => 'Driving Status',
			'smoking_status_id' => 'Smoking Status',
			'accommodation_id' => 'Accommodation',
			'comments' => 'Comments',
			'type_of_job' => 'Type of job',
			'carer_id' => 'Carer',
			'alcohol_intake' => 'Alcohol Intake',
			'substance_misuse_id' => 'Substance Misuse',
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('occupation_id', $this->occupation_id);
		$criteria->compare('driving_status_id', $this->driving_status_id);
		$criteria->compare('smoking_status_id', $this->smoking_status_id);
		$criteria->compare('accommodation_id', $this->accommodation_id);
		$criteria->compare('comments', $this->comments);
		$criteria->compare('type_of_job', $this->type_of_job);
		$criteria->compare('carer_id', $this->carer_id);
		$criteria->compare('alcohol_intake', $this->alcohol_intake);
		$criteria->compare('substance_misuse_id', $this->substance_misuse_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}
}
