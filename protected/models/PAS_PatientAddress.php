<?php

/**
 * This is the model class for table "SILVER.PATIENT_ADDRS".
 *
 * The followings are the available columns in table 'SILVER.PATIENT_ADDRS':
 * @property integer $RM_PATIENT_NO
 * @property string $ADDR_TYPE
 * @property string $DATE_START
 * @property string $ADDR_FORMAT
 * @property string $PROPERTY_NAME
 * @property string $PROPERTY_NO
 * @property string $ADDR1
 * @property string $ADDR2
 * @property string $ADDR3
 * @property string $ADDR4
 * @property string $ADDR5
 * @property string $POSTCODE
 * @property string $HA_CODE
 * @property string $TEL_NO
 * @property string $DWELLING_TYPE
 * @property string $SHARING_ACCOM
 * @property string $DATE_END
 * @property string $HDDR_GROUP
 */
class PAS_PatientAddress extends MultiActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PAS_PatientAddress the static model class
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
		return 'SILVER.PATIENT_ADDRS';
	}

	/**
	 * @return string primary key for the table
	 */
	public function primaryKey()
	{
		return 'RM_PATIENT_NO';
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
			array('ADDR_TYPE', 'length', 'max'=>1),
			array('ADDR_FORMAT, HA_CODE, DWELLING_TYPE, SHARING_ACCOM', 'length', 'max'=>4),
			array('PROPERTY_NAME, ADDR1, ADDR2, ADDR3, ADDR4, ADDR5, TEL_NO', 'length', 'max'=>35),
			array('PROPERTY_NO', 'length', 'max'=>10),
			array('POSTCODE', 'length', 'max'=>9),
			array('HDDR_GROUP', 'length', 'max'=>48),
			array('DATE_START, DATE_END', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('RM_PATIENT_NO, ADDR_TYPE, DATE_START, ADDR_FORMAT, PROPERTY_NAME, PROPERTY_NO, ADDR1, ADDR2, ADDR3, ADDR4, ADDR5, POSTCODE, HA_CODE, TEL_NO, DWELLING_TYPE, SHARING_ACCOM, DATE_END, HDDR_GROUP', 'safe', 'on'=>'search'),
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
			'ADDR_TYPE' => 'Addr Type',
			'DATE_START' => 'Date Start',
			'ADDR_FORMAT' => 'Addr Format',
			'PROPERTY_NAME' => 'Property Name',
			'PROPERTY_NO' => 'Property No',
			'ADDR1' => 'Addr1',
			'ADDR2' => 'Addr2',
			'ADDR3' => 'Addr3',
			'ADDR4' => 'Addr4',
			'ADDR5' => 'Addr5',
			'POSTCODE' => 'Postcode',
			'HA_CODE' => 'Ha Code',
			'TEL_NO' => 'Tel No',
			'DWELLING_TYPE' => 'Dwelling Type',
			'SHARING_ACCOM' => 'Sharing Accom',
			'DATE_END' => 'Date End',
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

		$criteria->compare('RM_PATIENT_NO',$this->RM_PATIENT_NO);
		$criteria->compare('ADDR_TYPE',$this->ADDR_TYPE,true);
		$criteria->compare('DATE_START',$this->DATE_START,true);
		$criteria->compare('ADDR_FORMAT',$this->ADDR_FORMAT,true);
		$criteria->compare('LOWER(PROPERTY_NAME)',strtolower($this->PROPERTY_NAME),true);
		$criteria->compare('PROPERTY_NO',$this->PROPERTY_NO,true);
		$criteria->compare('LOWER(ADDR1)',strtolower($this->ADDR1),true);
		$criteria->compare('LOWER(ADDR2)',strtolower($this->ADDR2),true);
		$criteria->compare('LOWER(ADDR3)',strtolower($this->ADDR3),true);
		$criteria->compare('LOWER(ADDR4)',strtolower($this->ADDR4),true);
		$criteria->compare('LOWER(ADDR5)',strtolower($this->ADDR5),true);
		$criteria->compare('LOWER(POSTCODE)',strtolower($this->POSTCODE),true);
		$criteria->compare('LOWER(HA_CODE)',strtolower($this->HA_CODE),true);
		$criteria->compare('TEL_NO',$this->TEL_NO,true);
		$criteria->compare('LOWER(DWELLING_TYPE)',strtolower($this->DWELLING_TYPE),true);
		$criteria->compare('LOWER(SHARING_ACCOM)',strtolower($this->SHARING_ACCOM),true);
		$criteria->compare('DATE_END',$this->DATE_END,true);
		$criteria->compare('HDDR_GROUP',$this->HDDR_GROUP,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
