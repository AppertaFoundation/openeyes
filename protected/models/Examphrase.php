<?php

/**
 * This is the model class for table "examphrase".
 *
 * The followings are the available columns in table 'examphrase':
 * @property string $id
 * @property string $specialty_id
 * @property integer $part
 * @property string $phrase
 * @property string $order
 *
 * The followings are the available model relations:
 * @property Specialty $specialty
 */
class Examphrase extends CActiveRecord
{
	const PART_HISTORY = 0;
	const PART_PMH = 1;
	const PART_POH = 2;
	const PART_DRUGS = 3;
	const PART_ALLERGIES = 4;
	const PART_ANTSEG = 5;
	const PART_POSTSEG = 6;
	const PART_CONCLUSION = 7;
	const PART_TREATMENT = 8;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Examphrase the static model class
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
		return 'Examphrase';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('specialty_id, phrase', 'required'),
			array('part', 'numerical', 'integerOnly'=>true),
			array('specialty_id, order', 'length', 'max'=>10),
			array('phrase', 'length', 'max'=>80),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, specialty_id, part, phrase, order', 'safe', 'on'=>'search'),
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
			'part' => 'Part',
			'phrase' => 'Phrase',
			'order' => 'Order',
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
		$criteria->compare('part',$this->part);
		$criteria->compare('phrase',$this->phrase,true);
		$criteria->compare('order',$this->order,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getSpecialtyOptions()
	{
		return CHtml::listData(Specialty::Model()->findAll(), 'id', 'name');
	}

	public function getPartOptions()
	{
		return array(
			self::PART_HISTORY => 'History',
			self::PART_PMH => 'PMH',
			self::PART_POH => 'POH',
			self::PART_DRUGS => 'Drugs',
			self::PART_ALLERGIES => 'Allergies',
			self::PART_ANTSEG => 'Antseg',
			self::PART_POSTSEG => 'Postseg',
			self::PART_CONCLUSION => 'Conclusion',
			self::PART_TREATMENT => 'Treatment'
		);
	}

	public function getPartText()
	{
		$partOptions = $this->getPartOptions();

		return $partOptions[$this->part];
	}
}
