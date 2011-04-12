<?php

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property string $id
 * @property string $episode_id
 * @property string $user_id
 * @property string $event_type_id
 * @property string $datetime
 *
 * The followings are the available model relations:
 * @property Episode $episode
 * @property User $user
 * @property EventType $eventType
 */
class Event extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Event the static model class
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
		return 'event';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('episode_id, user_id, event_type_id', 'required'),
			array('episode_id, user_id, event_type_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, episode_id, user_id, event_type_id, datetime', 'safe', 'on'=>'search'),
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
			'episode' => array(self::BELONGS_TO, 'Episode', 'episode_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'episode_id' => 'Episode',
			'user_id' => 'User',
			'event_type_id' => 'Event Type',
			'datetime' => 'Datetime',
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
		$criteria->compare('episode_id',$this->episode_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('event_type_id',$this->event_type_id,true);
		$criteria->compare('datetime',$this->datetime,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}