<?php

/**
 * This is the model class for table "phrase_by_specialty".
 *
 * The followings are the available columns in table 'phrase_by_specialty':
 * @property string $id
 * @property string $name
 * @property string $phrase
 * @property string $section_by_specialty_id
 * @property string $display_order
 * @property string $specialty_id
 *
 * The followings are the available model relations:
 * @property Specialty $specialty
 * @property SectionBySpecialty $sectionBySpecialty
 */
class PhraseBySpecialty extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PhraseBySpecialty the static model class
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
		return 'phrase_by_specialty';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('section_by_specialty_id, specialty_id', 'required'),
			array('name', 'length', 'max'=>255),
			array('section_by_specialty_id, display_order, specialty_id', 'length', 'max'=>10),
			array('phrase', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, phrase, section_by_specialty_id, display_order, specialty_id', 'safe', 'on'=>'search'),
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
			'sectionBySpecialty' => array(self::BELONGS_TO, 'SectionBySpecialty', 'section_by_specialty_id'),
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
			'section_by_specialty_id' => 'Section By Specialty',
			'display_order' => 'Display Order',
			'specialty_id' => 'Specialty',
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
		$criteria->compare('section_by_specialty_id',$this->section_by_specialty_id,true);
		$criteria->compare('display_order',$this->display_order,true);
		$criteria->compare('specialty_id',$this->specialty_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}