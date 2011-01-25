<?php

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
		$criteria->compare('SURNAME_TYPE',$this->SURNAME_TYPE,true);
		$criteria->compare('SURNAME_ID',$this->SURNAME_ID,true);
		$criteria->compare('NAME1',$this->NAME1,true);
		$criteria->compare('NAME2',$this->NAME2,true);
		$criteria->compare('TITLE',$this->TITLE,true);
		$criteria->compare('SURNAME_ID_SOUNDEX',$this->SURNAME_ID_SOUNDEX,true);
		$criteria->compare('NAME1_SOUNDEX',$this->NAME1_SOUNDEX,true);
		$criteria->compare('NAME2_SOUNDEX',$this->NAME2_SOUNDEX,true);
		$criteria->compare('HDDR_GROUP',$this->HDDR_GROUP,true);
		$criteria->compare('NAME3',$this->NAME3,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}