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
 * This is the model class for table "SILVER.SURNAME_IDS".
 *
 * The followings are the available columns in table 'SILVER.SURNAME_IDS':
 * @property integer $RM_PATIENT_NO
 * @property string $SURNAME_TYPE
 * @property string $SURNAME_ID
 * @property string $NAME1
 * @property string $NAME2
 * @property string $TITLE
 * @property string $SURNAME_ID_SOUNDEX
 * @property string $NAME1_SOUNDEX
 * @property string $NAME2_SOUNDEX
 * @property string $HDDR_GROUP
 * @property string $NAME3
 */
class PAS_PatientSurname extends MultiActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PAS_PatientSurname the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated db connection name
	 */
	public function connectionId()
	{
		return 'db_pas';
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'SILVER.SURNAME_IDS';
	}

	/**
	 * @return string primary key for the table
	 */
	public function primaryKey()
	{
		return array('RM_PATIENT_NO','SURNAME_TYPE','SURNAME_ID');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('RM_PATIENT_NO', 'numerical', 'integerOnly'=>true),
			array('SURNAME_TYPE', 'length', 'max'=>2),
			array('SURNAME_ID', 'length', 'max'=>35),
			array('NAME1, NAME2, NAME3', 'length', 'max'=>20),
			array('TITLE', 'length', 'max'=>5),
			array('SURNAME_ID_SOUNDEX, NAME1_SOUNDEX, NAME2_SOUNDEX', 'length', 'max'=>4),
			array('HDDR_GROUP', 'length', 'max'=>48),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('RM_PATIENT_NO, SURNAME_TYPE, SURNAME_ID, NAME1, NAME2, TITLE, SURNAME_ID_SOUNDEX, NAME1_SOUNDEX, NAME2_SOUNDEX, HDDR_GROUP, NAME3', 'safe', 'on'=>'search'),
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
			'patient'=>array(self::BELONGS_TO, 'PAS_Patient', 'RM_PATIENT_NO')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'RM_PATIENT_NO' => 'Rm Patient No',
			'SURNAME_TYPE' => 'Surname Type',
			'SURNAME_ID' => 'Surname',
			'NAME1' => 'Name1',
			'NAME2' => 'Name2',
			'TITLE' => 'Title',
			'SURNAME_ID_SOUNDEX' => 'Surname Id Soundex',
			'NAME1_SOUNDEX' => 'Name1 Soundex',
			'NAME2_SOUNDEX' => 'Name2 Soundex',
			'HDDR_GROUP' => 'Hddr Group',
			'NAME3' => 'Name3',
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

		$criteria=new CDbCriteria;

		$criteria->compare('RM_PATIENT_NO',$this->RM_PATIENT_NO);
		$criteria->compare('LOWER(SURNAME_TYPE)',strtolower($this->SURNAME_TYPE),true);
		$criteria->compare('LOWER(SURNAME_ID)',strtolower($this->SURNAME_ID),true);
		$criteria->compare('LOWER(NAME1)',strtolower($this->NAME1),true);
		$criteria->compare('LOWER(NAME2)',strtolower($this->NAME2),true);
		$criteria->compare('LOWER(TITLE)',strtolower($this->TITLE),true);
		$criteria->compare('LOWER(SURNAME_ID_SOUNDEX)',strtolower($this->SURNAME_ID_SOUNDEX),true);
		$criteria->compare('LOWER(NAME1_SOUNDEX)',strtolower($this->NAME1_SOUNDEX),true);
		$criteria->compare('LOWER(NAME2_SOUNDEX)',strtolower($this->NAME2_SOUNDEX),true);
		$criteria->compare('HDDR_GROUP',$this->HDDR_GROUP,true);
		$criteria->compare('LOWER(NAME3)',strtolower($this->NAME3),true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
