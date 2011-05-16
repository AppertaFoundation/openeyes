<?php

/**
 * This is the model class for table "phrase_by_firm".
 *
 * The followings are the available columns in table 'phrase_by_firm':
 * @property string $id
 * @property string $name
 * @property string $phrase
 * @property string $section_id
 * @property string $display_order
 * @property string $firm_id
 *
 * The followings are the available model relations:
 * @property Firm $firm
 * @property Section $section
 */
class PhraseByFirm extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PhraseByFirm the static model class
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
		return 'phrase_by_firm';
	}

        public function relevantSections()
        {
                return array('Letter');
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('section_id, firm_id', 'required'),
			array('name', 'length', 'max'=>255),
			array('section_id, display_order, firm_id', 'length', 'max'=>10),
			array('phrase', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, phrase, section_id, display_order, firm_id', 'safe', 'on'=>'search'),
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
			'section' => array(self::BELONGS_TO, 'Section', 'section_id'),
			'name' => array(self::BELONGS_TO, 'PhraseName', 'phrase_name_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'phrase' => 'Phrase',
			'section_id' => 'Section',
			'display_order' => 'Display Order',
			'firm_id' => 'Firm',
			'phrase_name_id' => 'Name',
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
		$criteria->compare('section_id',$this->section_id,true);
		$criteria->compare('display_order',$this->display_order,true);
		$criteria->compare('firm_id',$this->firm_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
