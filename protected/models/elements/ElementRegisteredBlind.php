<?php

/**
 * This is the model class for table "element_registered_blind".
 *
 * The followings are the available columns in table 'element_registered_blind':
 * @property string $id
 * @property string $event_id
 * @property integer $status
 */
class ElementRegisteredBlind extends BaseElement
{
	const NOT_REGISTERED = 1;
	const SIGHT_IMPAIRED = 2;
	const SEVERELY_SIGHT_IMPAIRED = 3;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementRegisteredBlind the static model class
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
		return 'element_registered_blind';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, status', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'event_id' => 'Event',
			'status' => 'Status',
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
		$criteria->compare('event_id',$this->event_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Fetch the options for registering as sight impaired
	 * @return array
	 */
	public function getSelectOptions()
	{
		return array(
			self::NOT_REGISTERED => 'Not Registered',
			self::SIGHT_IMPAIRED => 'Sight Impaired',
			self::SEVERELY_SIGHT_IMPAIRED => 'Severely Sight Impaired'
		);
	}

	/**
	 * Translate status constant into text value
	 *
	 * @return string
	 */
	public function getStatusText()
	{
		$text = '';
		switch ($this->status) {
			case self::NOT_REGISTERED:
				$text = 'Not Registered';
				break;
			case self::SIGHT_IMPAIRED:
				$text = 'Sight Impaired';
				break;
			case self::SEVERELY_SIGHT_IMPAIRED:
				$text = 'Severely Sight Impared';
				break;
		}
		return $text;
	}
}