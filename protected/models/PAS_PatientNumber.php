<?php

/**
 * This is the model class for table "SILVER.NUMBER_IDS".
 *
 * The followings are the available columns in table 'SILVER.NUMBER_IDS':
 * @property string $NUMBER_ID
 * @property string $NUM_ID_TYPE
 * @property integer $RM_PATIENT_NO
 */
class PAS_PatientNumber extends MultiActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PAS_NumberId the static model class
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
		return 'SILVER.NUMBER_IDS';
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
			array('NUMBER_ID', 'length', 'max'=>10),
			array('NUM_ID_TYPE', 'length', 'max'=>4),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('NUMBER_ID, NUM_ID_TYPE, RM_PATIENT_NO', 'safe', 'on'=>'search'),
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
			'NUMBER_ID' => 'Number',
			'NUM_ID_TYPE' => 'Num Id Type',
			'RM_PATIENT_NO' => 'Rm Patient No',
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

		$criteria->compare('NUMBER_ID',$this->NUMBER_ID,true);
		$criteria->compare('NUM_ID_TYPE',$this->NUM_ID_TYPE,true);
		$criteria->compare('RM_PATIENT_NO',$this->RM_PATIENT_NO);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}