<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "pas_assignment".
 *
 * The followings are the available columns in table 'pas_assignment':
 * @property string $id
 * @property string $external_id
 * @property string $external_type
 * @property integer $internal_id
 * @property string $internal_type
 * @property string $created_date
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $last_modified_user_id
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property PAS_Patient $pas_patient
 */
class PasAssignment extends BaseActiveRecord {
	
	/**
	 * How long (in seconds) before cached PAS details are considered stale
	 */
	const PAS_CACHE_TIME = 3;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Phrase the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'pas_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('external_id, external_type, internal_id, internal_type', 'required'),
			array('id, external_id, external_type internal_id, internal_type, created_date, last_modified_date, created_user_id, last_modified_user_id', 'safe', 'on'=>'search'),
		);
	}

	public function getInternal() {
		return self::model($this->internal_type)->findByPk($this->internal_id);
	}
	
	public function getExternal() {
		return self::model($this->external_type)->findByPk($this->external_id);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('internal_id',$this->internal_id,true);
		$criteria->compare('internal_type',$this->internal_type,true);
		$criteria->compare('external_id',$this->external_id,true);
		$criteria->compare('external_type',$this->external_type,true);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('last_modified_date',$this->last_modified_date,true);
		$criteria->compare('created_user_id',$this->created_user_id,true);
		$criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);
		
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function findByInternal($internal_type, $internal_id) {
		return $this->find('internal_id = :internal_id AND internal_type = :internal_type', array(':internal_id' => (int) $internal_id, ':internal_type' => $internal_type));
	}
	
	public function findByExternal($external_type, $external_id) {
		return $this->find('external_id = :external_id AND external_type = :external_type', array(':external_id' => (int) $external_id, ':external_type' => $external_type));
	}
	
	public function isStale() {
		return strtotime($this->last_modified_date) < (time() - self::PAS_CACHE_TIME);
	}
	
	public static function is_stale($internal_type, $internal_id) {
		$record = self::model()->findByInternal($internal_type, $internal_id);
		return $record && $record->isStale();
	}
	
}
