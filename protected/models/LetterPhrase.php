<?php

/**
 * This is the model class for table "letter_phrase".
 *
 * The followings are the available columns in table 'letter_phrase':
 * @property string $id
 * @property string $firm_id
 * @property string $name
 * @property string $phrase
 * @property string $display_order
 * @property integer $section
 *
 * The followings are the available model relations:
 * @property Firm $firm
 */
class LetterPhrase extends CActiveRecord
{
	const SECTION_INTRODUCTION = 0;
	const SECTION_FINDINGS = 1;
	const SECTION_DIAGNOSIS = 2;
	const SECTION_MANAGEMENT = 3;
	const SECTION_DRUGS = 4;
	const SECTION_OUTCOME = 5;

	/**
	 * Returns the static model of the specified AR class.
	 * @return LetterPhrase the static model class
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
		return 'letter_phrase';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('firm_id', 'required'),
			array('section', 'numerical', 'integerOnly'=>true),
			array('firm_id, display_order', 'length', 'max'=>10),
			array('name', 'length', 'max'=>64),
			array('phrase', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, firm_id, name, phrase, display_order, section', 'safe', 'on'=>'search'),
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
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'firm_id' => 'Firm',
			'name' => 'Name',
			'phrase' => 'Phrase',
			'display_order' => 'Display order',
			'section' => 'Section',
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
		$criteria->compare('firm_id',$this->firm_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phrase',$this->phrase,true);
		$criteria->compare('display_order',$this->display_order,true);
		$criteria->compare('section',$this->section);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getFirmOptions()
	{
		return CHtml::listData(Firm::Model()->findAll(), 'id', 'name');
	}

	public function getSectionOptions()
	{
		return array(
			self::SECTION_INTRODUCTION => 'Introduction',
			self::SECTION_FINDINGS => 'Findings',
			self::SECTION_DIAGNOSIS => 'Diagnosis',
			self::SECTION_MANAGEMENT => 'Management',
			self::SECTION_DRUGS => 'Drugs',
			self::SECTION_OUTCOME => 'Outcome'
		);
	}

	public function getSectionText()
	{
		$sectionOptions = $this->getSectionOptions();

		return $sectionOptions[$this->section];
	}
}
