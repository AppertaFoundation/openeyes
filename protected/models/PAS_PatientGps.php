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
 * This is the model class for table "SILVER.PATIENT_GPS".
 *
 * The followings are the available columns in table 'SILVER.PATIENT_GPS':
 * @property string $RM_PATIENT_NO
 * @property string $DATE_FROM
 * @property string $GP_ID
 * @property string $PRACTICE_CODE
 * @property string $HDDR_GROUP
 * @property string $DATE_TO
 */
class PAS_PatientGps extends MultiActiveRecord {

	/**
	 * Returns the static model of the specified AR class.
	 * @return PAS_PatientGps the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated db connection name
	 */
	public function connectionId() {
		return 'db_pas';
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'SILVER.PATIENT_GPS';
	}

	/**
	 * @return array primary key for the table
	 */
	public function primaryKey() {
		return array('RM_PATIENT_NO','DATE_FROM','GP_ID');
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('RM_PATIENT_NO, DATE_FROM, GP_ID, PRACTICE_CODE, HDDR_GROUP, DATE_TO', 'safe'),
			array('RM_PATIENT_NO, DATE_FROM, GP_ID, PRACTICE_CODE, HDDR_GROUP, DATE_TO', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			'Gp' => array(self::HAS_ONE, 'PAS_Gp', 'OBJ_PROF',
				// DATE_START is the tiebreaker
				'order' => 'DATE_FR DESC',
				// Exclude expired and future gps
				'condition' => '("Gp"."DATE_TO" IS NULL OR "Gp"."DATE_TO" >= SYSDATE) AND ("Gp"."DATE_FR" IS NULL OR "Gp"."DATE_FR" <= SYSDATE)',
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
 			'RM_PATIENT_NO' => 'RM Patient No.',
			'DATE_FROM' => 'Date From',
			'GP_ID' => 'GP ID',
			'PRACTICE_CODE' => 'Practice Code',
			'HDDR_GROUP' => 'HDDR Group',
			'DATE_TO' => 'Date To',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$criteria=new CDbCriteria;
		
		$criteria->compare('RM_PATIENT_NO',$this->RM_PATIENT_NO,true);
		$criteria->compare('DATE_FROM',$this->DATE_FROM,true);
		$criteria->compare('GP_ID',$this->GP_ID,true);
		$criteria->compare('PRACTICE_CODE',$this->PRACTICE_CODE,true);
		$criteria->compare('HDDR_GROUP',$this->HDDR_GROUP,true);
		$criteria->compare('DATE_TO',$this->DATE_TO,true);
		
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
}
