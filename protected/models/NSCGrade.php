<?php

/**
 * This is the model class for table "nsc_grade".
 *
 * The followings are the available columns in table 'nsc_grade':
 * @property string $id
 * @property string $name
 * @property integer $type
 * @property string $medical_phrase
 * @property string $layman_phrase
 */
class NSCGrade extends BaseActiveRecord
{
	const RETINOPATHY = 1;
	const MACULOPATHY = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @return NSCGrade the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'nsc_grade';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, medical_phrase, layman_phrase', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>3),
			array('medical_phrase', 'length', 'max'=>5000),
			array('layman_phrase', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, type, medical_phrase, layman_phrase', 'safe', 'on'=>'search'),
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
			'retinopathyElements' => array(self::HAS_MANY, 'ElementNSCGrade', 'retinopathy_grade_id'),
			'maculopathyElements' => array(self::HAS_MANY, 'ElementNSCGrade', 'maculopathy_grade_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'type' => 'Type',
			'medical_phrase' => 'Medical Phrase',
			'layman_phrase' => 'Layman Phrase',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('medical_phrase',$this->medical_phrase,true);
		$criteria->compare('layman_phrase',$this->layman_phrase,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}