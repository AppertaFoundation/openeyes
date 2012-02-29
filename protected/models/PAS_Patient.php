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
 * This is the model class for table "SILVER.PATIENTS".
 *
 * The followings are the available columns in table 'SILVER.PATIENTS':
 * @property integer $RM_PATIENT_NO
 * @property string $FICT_CLIENT
 * @property string $DISC_CLIENT
 * @property string $SEX
 * @property string $BIOLOGICAL_SEX
 * @property string $MARITAL_STAT
 * @property string $RELIGION
 * @property string $DATE_OF_BIRTH
 * @property string $TIME_OF_BIRTH
 * @property string $PLACE_OF_BIRTH
 * @property string $BIRTH_NOTIFICATION
 * @property string $DATE_OF_DEATH
 * @property string $TIME_OF_DEATH
 * @property string $PLACE_OF_DEATH
 * @property string $DEATH_NOTIFICATION
 * @property string $DATE_DOD_NOTIFIED
 * @property string $DATE_POST_MORTEM
 * @property string $OUTCOME_PM
 * @property string $DEATH_CAUSE
 * @property string $BLOOD_GRP
 * @property string $RHESUS
 * @property string $ETHNIC_GRP
 * @property string $LANGUAGE
 * @property string $OLANG
 * @property string $INTERPRETER_REQD
 * @property string $IMM_STAT
 * @property string $ENG_SPK
 * @property string $STAFF_MEMBER
 * @property string $EMP_CATEGORY
 * @property string $OCCUPATION_CODE
 * @property string $OCCUPATION_DESC
 * @property string $DAY_PHONE_NO
 * @property string $DATE_REGISTERED
 * @property string $DATE_REG_SICK_DISABLED
 * @property string $HDDR_GROUP
 * @property string $NHS_STAT
 * @property string $WTEL
 * @property string $MTEL
 * @property string $MTEL_CS
 * @property string $EMAIL
 * @property string $EMAIL_CS
 * @property string $PDS_FLAG
 * @property integer $PDS_SCN
 * @property string $PDS_SYNC
 * @property string $PDS_DCPL
 * @property string $CTS_FLAG
 * @property string $CTS_TEXT
 * @property string $SMOKER
 * @property string $NOTES
 */
class PAS_Patient extends MultiActiveRecord {

	/**
	 * Returns the static model of the specified AR class.
	 * @return PAS_Patient the static model class
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
		return 'SILVER.PATIENTS';
	}

	/**
	 * @return string primary key for the table
	 */
	public function primaryKey() {
		return 'RM_PATIENT_NO';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('RM_PATIENT_NO, PDS_SCN', 'numerical', 'integerOnly' => true),
			array('FICT_CLIENT, DISC_CLIENT, MTEL_CS, EMAIL_CS, PDS_FLAG, PDS_DCPL, CTS_FLAG', 'length', 'max' => 1),
			array('SEX, BIOLOGICAL_SEX, MARITAL_STAT, RELIGION, BIRTH_NOTIFICATION, PLACE_OF_DEATH, DEATH_NOTIFICATION, OUTCOME_PM, BLOOD_GRP, RHESUS, ETHNIC_GRP, LANGUAGE, OLANG, INTERPRETER_REQD, IMM_STAT, ENG_SPK, STAFF_MEMBER, EMP_CATEGORY, SMOKER', 'length', 'max' => 4),
			array('TIME_OF_BIRTH, TIME_OF_DEATH, OCCUPATION_CODE', 'length', 'max' => 5),
			array('PLACE_OF_BIRTH', 'length', 'max' => 30),
			array('DEATH_CAUSE, OCCUPATION_DESC, DAY_PHONE_NO, WTEL, MTEL', 'length', 'max' => 35),
			array('HDDR_GROUP', 'length', 'max' => 48),
			array('NHS_STAT', 'length', 'max' => 2),
			array('EMAIL', 'length', 'max' => 50),
			array('CTS_TEXT', 'length', 'max' => 500),
			array('NOTES', 'length', 'max' => 2000),
			array('DATE_OF_BIRTH, DATE_OF_DEATH, DATE_DOD_NOTIFIED, DATE_POST_MORTEM, DATE_REGISTERED, DATE_REG_SICK_DISABLED, PDS_SYNC', 'safe'),
			array('RM_PATIENT_NO, DATE_OF_BIRTH', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			'names' => array(self::HAS_MANY, 'PAS_PatientSurname', 'RM_PATIENT_NO'),
			'name' => array(self::HAS_ONE, 'PAS_PatientSurname', 'RM_PATIENT_NO', 'on' => '"name"."SURNAME_TYPE" = \'NO\''),
			'numbers' => array(self::HAS_MANY, 'PAS_PatientNumber', 'RM_PATIENT_NO'),
			'nhs_number' => array(self::HAS_ONE, 'PAS_PatientNumber', 'RM_PATIENT_NO', 'on' => '"nhs_number"."NUM_ID_TYPE" = \'NHS\''),
			'hos_number' => array(self::HAS_ONE, 'PAS_PatientNumber', 'RM_PATIENT_NO', 'on' => 'REGEXP_LIKE("hos_number"."NUM_ID_TYPE", \'[[:digit:]]\')'), 
			'addresses' => array(self::HAS_MANY, 'PAS_PatientAddress', 'RM_PATIENT_NO',
				// Exclude expired and future addresses
				'condition' => '("addresses"."DATE_END" IS NULL OR "addresses"."DATE_END" >= SYSDATE) AND ("addresses"."DATE_START" IS NULL OR "addresses"."DATE_START" <= SYSDATE)',
			),
			'name' => array(self::HAS_ONE, 'PAS_PatientSurname', 'RM_PATIENT_NO', 'on' => '"name"."SURNAME_TYPE" = \'NO\''),
			'address' => array(self::HAS_ONE, 'PAS_PatientAddress', 'RM_PATIENT_NO',
				// Address preference is (Home, Correspond, other), and DATE_START is the tiebreaker
				'order' => 'DECODE("address"."ADDR_TYPE", \'H\', 1, \'C\', 2, 3), "address"."DATE_START" DESC',
				// Exclude expired and future addresses
				'condition' => '("address"."DATE_END" IS NULL OR "address"."DATE_END" >= SYSDATE) AND ("address"."DATE_START" IS NULL OR "address"."DATE_START" <= SYSDATE)',
			),
			'PatientGp' => array(self::HAS_ONE, 'PAS_PatientGps', 'RM_PATIENT_NO',
				// DATE_START is the tiebreaker
				'order' => 'DATE_FROM DESC',
				// Exclude expired and future gps
				'condition' => '("PatientGp"."DATE_TO" IS NULL OR "PatientGp"."DATE_TO" >= SYSDATE) AND ("PatientGp"."DATE_FROM" IS NULL OR "PatientGp"."DATE_FROM" <= SYSDATE)',
			),
		);
	}
	
	/**
	 * @return array customized attribute labels (name => label)
	 */
	public function attributeLabels() {
		return array(
			'RM_PATIENT_NO' => 'Patient ID',
			'FICT_CLIENT' => 'Fict Client',
			'DISC_CLIENT' => 'Disc Client',
			'SEX' => 'Gender',
			'BIOLOGICAL_SEX' => 'Biological Gender',
			'MARITAL_STAT' => 'Marital Status',
			'RELIGION' => 'Religion',
			'DATE_OF_BIRTH' => 'Date Of Birth',
			'TIME_OF_BIRTH' => 'Time Of Birth',
			'PLACE_OF_BIRTH' => 'Place Of Birth',
			'BIRTH_NOTIFICATION' => 'Birth Notification',
			'DATE_OF_DEATH' => 'Date Of Death',
			'TIME_OF_DEATH' => 'Time Of Death',
			'PLACE_OF_DEATH' => 'Place Of Death',
			'DEATH_NOTIFICATION' => 'Death Notification',
			'DATE_DOD_NOTIFIED' => 'Date Dod Notified',
			'DATE_POST_MORTEM' => 'Date Post Mortem',
			'OUTCOME_PM' => 'Outcome PM',
			'DEATH_CAUSE' => 'Death Cause',
			'BLOOD_GRP' => 'Blood Group',
			'RHESUS' => 'Rhesus',
			'ETHNIC_GRP' => 'Ethnic Group',
			'LANGUAGE' => 'Language',
			'OLANG' => 'Other Language',
			'INTERPRETER_REQD' => 'Interpreter Required?',
			'IMM_STAT' => 'Immigration Status',
			'ENG_SPK' => 'Eng Speaker?',
			'STAFF_MEMBER' => 'Staff Member',
			'EMP_CATEGORY' => 'Emp Category',
			'OCCUPATION_CODE' => 'Occupation Code',
			'OCCUPATION_DESC' => 'Occupation Description',
			'DAY_PHONE_NO' => 'Daytime Telephone',
			'DATE_REGISTERED' => 'Date Registered',
			'DATE_REG_SICK_DISABLED' => 'Date Registered Sick/Disabled',
			'HDDR_GROUP' => 'Hddr Group',
			'NHS_STAT' => 'NHS Stat',
			'WTEL' => 'Work Telephone',
			'MTEL' => 'Mobile Telephone',
			'MTEL_CS' => 'Mtel Cs',
			'EMAIL' => 'Email',
			'EMAIL_CS' => 'Email Cs',
			'PDS_FLAG' => 'Pds Flag',
			'PDS_SCN' => 'Pds Scn',
			'PDS_SYNC' => 'Pds Sync',
			'PDS_DCPL' => 'Pds Dcpl',
			'CTS_FLAG' => 'Cts Flag',
			'CTS_TEXT' => 'Cts Text',
			'SMOKER' => 'Smoker?',
			'NOTES' => 'Notes',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$criteria=new CDbCriteria;
		
		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}
}
