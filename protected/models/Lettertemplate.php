<?php

/**
 * This is the model class for table "lettertemplate".
 *
 * The followings are the available columns in table 'lettertemplate':
 * @property string $id
 * @property string $specialty_id
 * @property string $name
 * @property string $contacttype_id
 * @property string $text
 * @property string $cc
 *
 * The followings are the available model relations:
 * @property Specialty $specialty
 * @property Contacttype $contacttype
 */
class Lettertemplate extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Lettertemplate the static model class
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
		return 'lettertemplate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('specialty_id, contacttype_id', 'required'),
			array('specialty_id, contacttype_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>64),
			array('cc', 'length', 'max'=>128),
			array('text', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, specialty_id, name, contacttype_id, text, cc', 'safe', 'on'=>'search'),
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
			'specialty' => array(self::BELONGS_TO, 'Specialty', 'specialty_id'),
			'contacttype' => array(self::BELONGS_TO, 'Contacttype', 'contacttype_id'),
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
			'contacttype_id' => 'Contacttype',
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
		$criteria->compare('contacttype_id',$this->contacttype_id,true);
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

	public function getContacttypeOptions()
	{
		return CHtml::listData(Contacttype::Model()->findAll(), 'id', 'name');
	}
}