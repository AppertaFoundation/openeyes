<?php

/**
 * This is the model class for table "letter_template".
 *
 * The followings are the available columns in table 'letter_template':
 * @property string $id
 * @property string $specialty_id
 * @property string $name
 * @property string $contact_type_id
 * @property string $text
 * @property string $cc
 *
 * The followings are the available model relations:
 * @property ContactType $contactType
 * @property Specialty $specialty
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
			array('specialty_id, contact_type_id', 'required'),
			array('specialty_id, contact_type_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>64),
			array('cc', 'length', 'max'=>128),
			array('text', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, specialty_id, name, contact_type_id, text, cc', 'safe', 'on'=>'search'),
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
			'contactType' => array(self::BELONGS_TO, 'ContactType', 'contact_type_id'),
			'specialty' => array(self::BELONGS_TO, 'Specialty', 'specialty_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'specialty_id' => 'Specialty',
			'name' => 'Name',
			'contact_type_id' => 'Contact Type',
			'text' => 'Text',
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
		$criteria->compare('specialty_id',$this->specialty_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('contact_type_id',$this->contact_type_id,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('cc',$this->cc,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
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