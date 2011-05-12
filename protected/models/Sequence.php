<?php

/**
 * This is the model class for table "sequence".
 *
 * The followings are the available columns in table 'sequence':
 * @property string $id
 * @property string $theatre_id
 * @property string $start_date
 * @property string $start_time
 * @property string $end_time
 * @property string $end_date
 * @property integer $frequency
 *
 * The followings are the available model relations:
 * @property Theatre $theatre
 * @property SequenceFirmAssignment[] $sequenceFirmAssignments
 * @property Session[] $sessions
 */
class Sequence extends CActiveRecord
{
	const FREQUENCY_ONCE = 0;
	const FREQUENCY_1WEEK = 1;
	const FREQUENCY_2WEEKS = 2;
	const FREQUENCY_3WEEKS = 3;
	const FREQUENCY_4WEEKS = 4;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Sequence the static model class
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
		return 'sequence';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('theatre_id, start_date, start_time, end_time, frequency', 'required'),
			array('frequency', 'numerical', 'integerOnly'=>true),
			array('theatre_id', 'length', 'max'=>10),
			array('end_date', 'safe'),
			array('start_date', 'date', 'format'=>'yyyy-MM-dd'),
			array('start_time', 'date', 'format'=>array('H:mm', 'H:mm:ss')),
			array('end_time', 'date', 'format'=>array('H:mm', 'H:mm:ss')),
			array('end_date', 'checkDates'),
			array('end_time', 'checkTimes'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, theatre_id, start_date, start_time, end_time, end_date, frequency', 'safe', 'on'=>'search'),
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
			'theatre' => array(self::BELONGS_TO, 'Theatre', 'theatre_id'),
			'sequenceFirmAssignment' => array(self::HAS_ONE, 'SequenceFirmAssignment', 'sequence_id'),
			'sessions' => array(self::HAS_MANY, 'Session', 'sequence_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'theatre_id' => 'Theatre',
			'start_date' => 'Start Date',
			'start_time' => 'Start Time (HH:MM or HH:MM:SS)',
			'end_time' => 'End Time (HH:MM or HH:MM:SS)',
			'end_date' => 'End Date',
			'frequency' => 'Frequency',
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
		$criteria->compare('theatre_id',$this->theatre_id,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('frequency',$this->frequency);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	public function getTheatreOptions()
	{
		$options = Yii::app()->db->createCommand()
			->select('t.id, t.name, s.name AS site')
			->from('theatre t')
			->join('site s', 't.site_id = s.id')
			->queryAll();

		$result = array();
		foreach ($options as $value) {
			$result[$value['id']] = $value['site'] . ' - ' . $value['name'];
		}

		return $result;
	}
	
	public function getFrequencyOptions()
	{
		return array(
			self::FREQUENCY_1WEEK => 'Every week',
			self::FREQUENCY_2WEEKS => 'Every 2 weeks',
			self::FREQUENCY_3WEEKS => 'Every 3 weeks',
			self::FREQUENCY_4WEEKS => 'Every 4 weeks',
			self::FREQUENCY_ONCE => 'One time',
		);
	}
	
	protected function beforeSave()
	{
		$this->start_date = date('Y-m-d', strtotime($this->start_date));
		if (empty($this->end_date)) {
			$this->setAttribute('end_date', null);
		} else {
			$this->end_date = date('Y-m-d', strtotime($this->end_date));
		}
		
		return parent::beforeSave();
	}
	
	public function checkDates($attribute, $params)
	{
		if (!empty($this->end_date)) {
			$start = strtotime($this->start_date);
			$end = strtotime($this->end_date);
			
			if ($end < $start) {
				$this->addError('end_date', 'End date must be after the start date.');
			}
		}
	}
	
	public function checkTimes()
	{
		$start = strtotime($this->start_time);
		$end = strtotime($this->end_time);
		
		if ($end <= $start) {
			$this->addError('end_time', 'End time must be after the start time.');
		}
	}
	
	protected function beforeValidate()
	{
		$startTimestamp = strtotime($this->start_date);
		$endTimestamp = !empty($this->end_date) ? strtotime($this->end_date) : strtotime('+100 years');
		
		$startDate = date('Y-m-d', $startTimestamp);
		$endDate = date('Y-m-d', $endTimestamp);
		$startTime = date('H:i:s', strtotime($this->start_time));
		$endTime = date('H:i:s', strtotime($this->end_time));
		
		$endTimeLimit = strtotime('+12 weeks');
		
		$interval = $this->getFrequencyInteger($this->frequency, $endTimestamp);
		
		$dateList = array();
		for ($i = $startTimestamp; $i <= $endTimestamp && $i <= $endTimeLimit; $i += $interval) {
			$dateList[] = date('Y-m-d H:i:s', $i);
		}
		
		$weekday = (date('N', $startTimestamp) - 1);
		$bookedList = $this->getBookedList($this->theatre_id, $weekday, $startDate, $endDate, $startTime, $endTime, $startTimestamp, $endTimestamp, $endTimeLimit);
		
		$conflicts = array_intersect($bookedList, $dateList);
		
		$valid = true;
		if (!empty($conflicts)) {
			$valid = false;
			$this->addError('start_date, end_date', 'There is a conflict with an existing sequence.');
		}
		
		$valid = $valid && parent::beforeValidate();
		return $valid;
	}
	
	protected function getBookedList($theatreId, $weekday, $startDate, $endDate, $startTime, $endTime, $scheduleStartTime, $scheduleEndTime, $endTimeLimit)
	{
		$sequences = Yii::app()->db->createCommand()
			->select('*')
			->from('sequence s')
			->where('theatre_id = :tid AND start_date <= :end_date AND 
				id != :sequence_id AND WEEKDAY(start_date) = :weekday AND 
				(end_date >= :start_date OR end_date IS NULL) AND 
				(start_time <= :end_time AND end_time >= :start_time)', 
				array(
					':tid' => $theatreId,
					':weekday' => $weekday,
					':end_date' => $endDate,
					':start_date' => $startDate,
					':start_time' => $startTime,
					':end_time' => $endTime,
					':sequence_id' => !empty($this->id) ? $this->id : 0))
			->queryAll();
		
		$bookedList = array();
		foreach ($sequences as $value) {
			$startTimestamp = strtotime($value['start_date']);
			if ($startTimestamp < $scheduleStartTime) {
				$startTimestamp = $scheduleStartTime;
			}
			$endTimestamp = !empty($value['end_date']) ? strtotime($value['end_date']) : $scheduleEndTime;
			if ($endTimestamp > $endTimeLimit) {
				$endTimestamp = $endTimeLimit;
			}
			$interval = $this->getFrequencyInteger($value['frequency'], $endTimestamp);
			
			for ($i = $startTimestamp; $i <= $endTimestamp; $i += $interval) {
				$bookedList[] = date('Y-m-d H:i:s', $i);
			}
		}
		
		return $bookedList;
	}
	
	public function getFrequencyInteger($frequency, $endTimestamp)
	{
		switch($frequency) {
			case self::FREQUENCY_1WEEK:
				$interval = 60 * 60 * 24 * 7;
				break;
			case self::FREQUENCY_2WEEKS:
				$interval = 60 * 60 * 24 * 14;
				break;
			case self::FREQUENCY_3WEEKS:
				$interval = 60 * 60 * 24 * 21;
				break;
			case self::FREQUENCY_4WEEKS:
				$interval = 60 * 60 * 24 * 28;
				break;
			case self::FREQUENCY_ONCE:
				$interval = $endTimestamp + 1;
				break;
		}
		
		return $interval;
	}
	
	public function getFirmName()
	{
		if (!empty($this->sequenceFirmAssignment)) {
			return $this->sequenceFirmAssignment->firm->name;
		} else {
			return 'None';
		}
	}
}