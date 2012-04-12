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
 * This is the model class for table "SILVER.NEXT_OF_KINS".
 *
 * The followings are the available columns in table 'SILVER.NEXT_OF_KINS':
 * @property integer $RM_PATIENT_NO
 * @property integer $SEQ_NO
 * @property string $NEXT_OF_KIN_RELATION
 * @property string $REL_TEXT
 * @property string $DISC_IND
 * @property string $TITLE
 * @property string $SURNAME
 * @property string $FORENAME1
 * @property string $ADDR_FORMAT
 * @property string $PROPERTY_NAME
 * @property string $PROPERTY_NO
 * @property string $ADDRESS_LINE_1
 * @property string $ADDRESS_LINE_2
 * @property string $ADDRESS_LINE_3
 * @property string $ADDRESS_LINE_4
 * @property string $POSTCODE
 * @property string $HOME_PHONE_NO
 * @property string $DAY_PHONE_NO
 * @property string $DATE_START
 * @property string $DATE_END
 * @property string $HDDR_GROUP
 * @property string $REL_STAT
 */
class PAS_NextOfKin extends MultiActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @return PAS_NextOfKin the static model class
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
		return 'SILVER.NEXT_OF_KINS';
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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('RM_PATIENT_NO, SEQ_NO', 'numerical', 'integerOnly'=>true),
				array('NEXT_OF_KIN_RELATION, DISC_IND, TITLE, ADDR_FORMAT, REL_STAT', 'length', 'max'=>4),
				array('REL_TEXT, SURNAME, PROPERTY_NAME, ADDRESS_LINE_1, ADDRESS_LINE_2, ADDRESS_LINE_3, ADDRESS_LINE_4, HOME_PHONE_NO, DAY_PHONE_NO', 'length', 'max'=>35),
				array('FORENAME1', 'length', 'max'=>20),
				array('PROPERTY_NO', 'length', 'max'=>10),
				array('POSTCODE', 'length', 'max'=>9),
				array('HDDR_GROUP', 'length', 'max'=>48),
				array('DATE_START, DATE_END', 'safe'),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('RM_PATIENT_NO, SEQ_NO, NEXT_OF_KIN_RELATION, REL_TEXT, DISC_IND, TITLE, SURNAME, FORENAME1, ADDR_FORMAT, PROPERTY_NAME, PROPERTY_NO, ADDRESS_LINE_1, ADDRESS_LINE_2, ADDRESS_LINE_3, ADDRESS_LINE_4, POSTCODE, HOME_PHONE_NO, DAY_PHONE_NO, DATE_START, DATE_END, HDDR_GROUP, REL_STAT', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'RM_PATIENT_NO' => 'Rm Patient No',
				'SEQ_NO' => 'Seq No',
				'NEXT_OF_KIN_RELATION' => 'Next Of Kin Relation',
				'REL_TEXT' => 'Rel Text',
				'DISC_IND' => 'Disc Ind',
				'TITLE' => 'Title',
				'SURNAME' => 'Surname',
				'FORENAME1' => 'Forename1',
				'ADDR_FORMAT' => 'Addr Format',
				'PROPERTY_NAME' => 'Property Name',
				'PROPERTY_NO' => 'Property No',
				'ADDRESS_LINE_1' => 'Address Line 1',
				'ADDRESS_LINE_2' => 'Address Line 2',
				'ADDRESS_LINE_3' => 'Address Line 3',
				'ADDRESS_LINE_4' => 'Address Line 4',
				'POSTCODE' => 'Postcode',
				'HOME_PHONE_NO' => 'Home Phone No',
				'DAY_PHONE_NO' => 'Day Phone No',
				'DATE_START' => 'Date Start',
				'DATE_END' => 'Date End',
				'HDDR_GROUP' => 'Hddr Group',
				'REL_STAT' => 'Rel Stat',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('RM_PATIENT_NO',$this->RM_PATIENT_NO);
		$criteria->compare('SEQ_NO',$this->SEQ_NO);
		$criteria->compare('NEXT_OF_KIN_RELATION',$this->NEXT_OF_KIN_RELATION,true);
		$criteria->compare('REL_TEXT',$this->REL_TEXT,true);
		$criteria->compare('DISC_IND',$this->DISC_IND,true);
		$criteria->compare('LOWER(TITLE)',strtolower($this->TITLE),true);
		$criteria->compare('LOWER(SURNAME)',strtolower($this->SURNAME),true);
		$criteria->compare('LOWER(FORENAME1)',strtolower($this->FORENAME1),true);
		$criteria->compare('ADDR_FORMAT',$this->ADDR_FORMAT,true);
		$criteria->compare('LOWER(PROPERTY_NAME)',strtolower($this->PROPERTY_NAME),true);
		$criteria->compare('PROPERTY_NO',$this->PROPERTY_NO,true);
		$criteria->compare('LOWER(ADDRESS_LINE_1)',strtolower($this->ADDRESS_LINE_1),true);
		$criteria->compare('LOWER(ADDRESS_LINE_2)',strtolower($this->ADDRESS_LINE_2),true);
		$criteria->compare('LOWER(ADDRESS_LINE_3)',strtolower($this->ADDRESS_LINE_3),true);
		$criteria->compare('LOWER(ADDRESS_LINE_4)',strtolower($this->ADDRESS_LINE_4),true);
		$criteria->compare('LOWER(POSTCODE)',strtolower($this->POSTCODE),true);
		$criteria->compare('HOME_PHONE_NO',$this->HOME_PHONE_NO,true);
		$criteria->compare('DAY_PHONE_NO',$this->DAY_PHONE_NO,true);
		$criteria->compare('DATE_START',$this->DATE_START,true);
		$criteria->compare('DATE_END',$this->DATE_END,true);
		$criteria->compare('HDDR_GROUP',$this->HDDR_GROUP,true);
		$criteria->compare('REL_STAT',$this->REL_STAT,true);

		return new CActiveDataProvider(get_class($this), array(
				'criteria'=>$criteria,
		));
	}

}
