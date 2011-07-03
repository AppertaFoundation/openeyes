<?php

/**
 * This is the model class for table "letter_template".
 *
 * The followings are the available columns in table 'letter_template':
 * @property string $id
 * @property string $name
 * @property string $phrase
 * @property string $specialty_id
 * @property string $to
 * @property string $cc
 *
 * The followings are the available model relations:
 * @property ContactType $cc0
 * @property Specialty $specialty
 * @property ContactType $to0
 */
class LetterTemplate extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return LetterTemplate the static model class
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
		return 'letter_template';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, phrase, specialty_id, to, cc', 'required'),
			array('name', 'length', 'max'=>255),
			array('phrase', 'length', 'max'=>2047),
			array('specialty_id, to, cc', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, phrase, specialty_id, to, cc', 'safe', 'on'=>'search'),
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
			'cc0' => array(self::BELONGS_TO, 'ContactType', 'cc'),
			'specialty' => array(self::BELONGS_TO, 'Specialty', 'specialty_id'),
			'to0' => array(self::BELONGS_TO, 'ContactType', 'to'),
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
			'phrase' => 'Phrase',
			'specialty_id' => 'Specialty',
			'to' => 'To',
			'cc' => 'Cc',
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
		$criteria->compare('phrase',$this->phrase,true);
		$criteria->compare('specialty_id',$this->specialty_id,true);
		$criteria->compare('to',$this->to,true);
		$criteria->compare('cc',$this->cc,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getSpecialtyText()
	{
		return $this->specialty->name;
	}

	public function getToText()
	{
		return $this->to0->name;
	}

	public function getCcText()
	{
		return $this->cc0->name;
	}

	public function getSpecialtyOptions()
	{
		return CHtml::listData(Specialty::Model()->findAll(), 'id', 'name');
	}

	public function getContactTypeOptions()
	{
		return CHtml::listData(ContactType::Model()->findAll(), 'id', 'name');
	}
}
