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
 * This is the model class for table "socialhistory".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property integer $occupation_id
 * @property integer $driving_status_id
 * @property integer $smoking_status_id
 * @property integer $accommodation_id
 * @property string $comments
 * @property string $type_of_job
 * @property integer $carer
 * @property string $alcohol_intake
 * @property integer $substance_misuse
 *
 * The followings are the available model relations:
 *
 * @property User $user
 * @property User $usermodified
 * @property SocialHistory_Occupation $occupation
 * @property SocialHistory_DrivingStatus $driving_status
 * @property SocialHistory_SmokingStatus $smoking_status
 * @property SocialHistory_Accommodation $accommodation
 */

class SocialHistory  extends  BaseActiveRecordVersioned
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
		return 'socialhistory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
				array('event_id, occupation_id, driving_status_id, smoking_status_id, accommodation_id, comments, type_of_job, carer, alcohol_intake, substance_misuse, ', 'safe'),
				array('occupation_id, driving_status_id, smoking_status_id, accommodation_id, comments, type_of_job, carer, alcohol_intake, substance_misuse, ', 'required'),
				array('id, event_id, occupation_id, driving_status_id, smoking_status_id, accommodation_id, comments, type_of_job, carer, alcohol_intake, substance_misuse, ', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
				'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
				'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
				'occupation' => array(self::BELONGS_TO, 'SocialHistory_Occupation', 'occupation_id'),
				'driving_status' => array(self::BELONGS_TO, 'SocialHistory_DrivingStatus', 'driving_status_id'),
				'smoking_status' => array(self::BELONGS_TO, 'SocialHistory_SmokingStatus', 'smoking_status_id'),
				'accommodation' => array(self::BELONGS_TO, 'SocialHistory_Accommodation', 'accommodation_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'id' => 'ID',
				'occupation_id' => 'Occupation',
				'driving_status_id' => 'Driving Status',
				'smoking_status_id' => 'Smoking Status',
				'accommodation_id' => 'Accommodation',
				'comments' => 'Comments',
				'type_of_job' => 'Type of job',
				'carer' => 'Carer',
				'alcohol_intake' => 'Alcohol Intake',
				'substance_misuse' => 'Substance Misuse',
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
		$criteria->compare('occupation_id', $this->occupation_id);
		$criteria->compare('driving_status_id', $this->driving_status_id);
		$criteria->compare('smoking_status_id', $this->smoking_status_id);
		$criteria->compare('accommodation_id', $this->accommodation_id);
		$criteria->compare('comments', $this->comments);
		$criteria->compare('type_of_job', $this->type_of_job);
		$criteria->compare('carer', $this->carer);
		$criteria->compare('alcohol_intake', $this->alcohol_intake);
		$criteria->compare('substance_misuse', $this->substance_misuse);

		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
		));
	}



	protected function afterSave()
	{

		return parent::afterSave();
	}
}
?>