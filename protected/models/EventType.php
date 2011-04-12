<?php

/**
 * This is the model class for table "event_type".
 *
 * The followings are the available columns in table 'event_type':
 * @property string $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property Event[] $events
 * @property EventTypeElementTypeAssignment[] $eventTypeElementTypeAssignments
 */
class EventType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return EventType the static model class
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
		return 'event_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>40),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
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
			'events' => array(self::HAS_MANY, 'Event', 'event_type_id'),
			'possibleElementTypes' => array(self::HAS_MANY, 'PossibleElementType', 'event_type_id'),
			'elementTypes' => array(self::MANY_MANY, 'ElementType', 'possible_element_type(event_type_id, element_type_id)')
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

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Retrieves dataobjects for all EventTypes that PossibleElementType/SiteElementType suggest are possible
	 */
	public function getAllPossible($specialtyId)
	{
		$criteria = new CDbCriteria;

		$criteria->distinct=true;
		$criteria->join = 'LEFT JOIN possible_element_type possibleElementType ON possibleElementType.event_type_id = t.id INNER JOIN site_element_type ON site_element_type.possible_element_type_id=possibleElementType.id';
		$criteria->addCondition('site_element_type.specialty_id = :specialty_id');
		$criteria->order = 't.id';
		$criteria->params = array(
			':specialty_id' => $specialtyId
		);

		$eventTypeObjects = EventType::model()->findAll($criteria);
		return $eventTypeObjects;
	}
}
