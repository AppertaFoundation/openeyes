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
 * This is the model class for table "SILVER.ENV040_PROFDETS".
 *
 * The followings are the available columns in table 'SILVER.ENV040_PROFDETS':
 * @property string $OBJ_TYPE
 * @property string $OBJ_PROF
 * @property string $DATE_FR
 * @property string $SN
 * @property string $FN1
 * @property string $FN2
 * @property string $TITLE
 * @property string $JOB_TTL
 * @property string $BLEEP
 * @property string $REG_ID
 * @property string $NAT_ID
 * @property string $GRADE
 * @property string $ADD_FORM
 * @property string $ADD_NAM
 * @property string $ADD_NUM
 * @property string $ADD_ST
 * @property string $ADD_DIS
 * @property string $ADD_TWN
 * @property string $ADD_CTY
 * @property string $ADD_CNT
 * @property string $PC
 * @property string $CONTACT
 * @property string $TEL_1
 * @property string $TEL_2
 * @property string $FAX_1
 * @property string $FAX_2
 * @property string $COMMS
 * @property string $INTER
 * @property string $EMAIL
 * @property string $PERS_NO
 * @property string $COST
 * @property string $SURG
 * @property string $C_SPEC
 * @property string $DATE_TO
 * @property string $HDDR_GROUP
 */
class PAS_Gp extends MultiActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PAS_Gp the static model class
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
		return 'SILVER.ENV040_PROFDETS';
	}

	/**
	 * @return string primary key for the table
	 */
	public function primaryKey()
	{
		return 'OBJ_PROF';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('OBJ_TYPE, GRADE, ADD_FORM, COMMS', 'length', 'max'=>4),
			array('OBJ_PROF, BLEEP, REG_ID, NAT_ID, ADD_NUM, PC, PERS_NO, SURG, C_SPEC', 'length', 'max'=>10),
			array('SN, JOB_TTL, ADD_NAM, ADD_ST, ADD_DIS, ADD_TWN, ADD_CTY, ADD_CNT, CONTACT, TEL_1, TEL_2, FAX_1, FAX_2, INTER, EMAIL', 'length', 'max'=>35),
			array('FN1, FN2, COST', 'length', 'max'=>20),
			array('TITLE', 'length', 'max'=>5),
			array('HDDR_GROUP', 'length', 'max'=>48),
			array('DATE_FR, DATE_TO', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('OBJ_TYPE, OBJ_PROF, DATE_FR, SN, FN1, FN2, TITLE, JOB_TTL, BLEEP, REG_ID, NAT_ID, GRADE, ADD_FORM, ADD_NAM, ADD_NUM, ADD_ST, ADD_DIS, ADD_TWN, ADD_CTY, ADD_CNT, PC, CONTACT, TEL_1, TEL_2, FAX_1, FAX_2, COMMS, INTER, EMAIL, PERS_NO, COST, SURG, C_SPEC, DATE_TO, HDDR_GROUP', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'OBJ_TYPE' => 'Obj Type',
			'OBJ_PROF' => 'Obj Prof',
			'DATE_FR' => 'Date Fr',
			'SN' => 'Sn',
			'FN1' => 'Fn1',
			'FN2' => 'Fn2',
			'TITLE' => 'Title',
			'JOB_TTL' => 'Job Ttl',
			'BLEEP' => 'Bleep',
			'REG_ID' => 'Reg',
			'NAT_ID' => 'Nat',
			'GRADE' => 'Grade',
			'ADD_FORM' => 'Add Form',
			'ADD_NAM' => 'Add Nam',
			'ADD_NUM' => 'Add Num',
			'ADD_ST' => 'Add St',
			'ADD_DIS' => 'Add Dis',
			'ADD_TWN' => 'Add Twn',
			'ADD_CTY' => 'Add Cty',
			'ADD_CNT' => 'Add Cnt',
			'PC' => 'Pc',
			'CONTACT' => 'Contact',
			'TEL_1' => 'Tel 1',
			'TEL_2' => 'Tel 2',
			'FAX_1' => 'Fax 1',
			'FAX_2' => 'Fax 2',
			'COMMS' => 'Comms',
			'INTER' => 'Inter',
			'EMAIL' => 'Email',
			'PERS_NO' => 'Pers No',
			'COST' => 'Cost',
			'SURG' => 'Surg',
			'C_SPEC' => 'C Spec',
			'DATE_TO' => 'Date To',
			'HDDR_GROUP' => 'Hddr Group',
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

		$criteria->compare('OBJ_TYPE',$this->OBJ_TYPE,true);
		$criteria->compare('OBJ_PROF',$this->OBJ_PROF,true);
		$criteria->compare('DATE_FR',$this->DATE_FR,true);
		$criteria->compare('SN',$this->SN,true);
		$criteria->compare('FN1',$this->FN1,true);
		$criteria->compare('FN2',$this->FN2,true);
		$criteria->compare('TITLE',$this->TITLE,true);
		$criteria->compare('JOB_TTL',$this->JOB_TTL,true);
		$criteria->compare('BLEEP',$this->BLEEP,true);
		$criteria->compare('REG_ID',$this->REG_ID,true);
		$criteria->compare('NAT_ID',$this->NAT_ID,true);
		$criteria->compare('GRADE',$this->GRADE,true);
		$criteria->compare('ADD_FORM',$this->ADD_FORM,true);
		$criteria->compare('ADD_NAM',$this->ADD_NAM,true);
		$criteria->compare('ADD_NUM',$this->ADD_NUM,true);
		$criteria->compare('ADD_ST',$this->ADD_ST,true);
		$criteria->compare('ADD_DIS',$this->ADD_DIS,true);
		$criteria->compare('ADD_TWN',$this->ADD_TWN,true);
		$criteria->compare('ADD_CTY',$this->ADD_CTY,true);
		$criteria->compare('ADD_CNT',$this->ADD_CNT,true);
		$criteria->compare('PC',$this->PC,true);
		$criteria->compare('CONTACT',$this->CONTACT,true);
		$criteria->compare('TEL_1',$this->TEL_1,true);
		$criteria->compare('TEL_2',$this->TEL_2,true);
		$criteria->compare('FAX_1',$this->FAX_1,true);
		$criteria->compare('FAX_2',$this->FAX_2,true);
		$criteria->compare('COMMS',$this->COMMS,true);
		$criteria->compare('INTER',$this->INTER,true);
		$criteria->compare('EMAIL',$this->EMAIL,true);
		$criteria->compare('PERS_NO',$this->PERS_NO,true);
		$criteria->compare('COST',$this->COST,true);
		$criteria->compare('SURG',$this->SURG,true);
		$criteria->compare('C_SPEC',$this->C_SPEC,true);
		$criteria->compare('DATE_TO',$this->DATE_TO,true);
		$criteria->compare('HDDR_GROUP',$this->HDDR_GROUP,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
