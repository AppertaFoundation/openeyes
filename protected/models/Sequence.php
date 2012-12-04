<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
 * @property integer $repeat_interval
 * @property integer $weekday
 * @property integer $week_selection
 * @property boolean $consultant
 * @property boolean $paediatric
 * @property boolean $anaesthetist
 * @property boolean $general_anaesthetic
 *
 * The followings are the available model relations:
 * @property Theatre $theatre
 * @property SequenceFirmAssignment $firmAssignment
 * @property Session[] $sessions
 */
class Sequence extends BaseActiveRecord {
	
	const FREQUENCY_ONCE = 0;
	const FREQUENCY_1WEEK = 1;
	const FREQUENCY_2WEEKS = 2;
	const FREQUENCY_3WEEKS = 3;
	const FREQUENCY_4WEEKS = 4;
	const FREQUENCY_MONTHLY = 5;
	
	const SELECT_1STWEEK = 1;
	const SELECT_2NDWEEK = 2;
	const SELECT_3RDWEEK = 4;
	const SELECT_4THWEEK = 8;
	const SELECT_5THWEEK = 16;

	public $firm_id;
	public $site_id;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Sequence the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'sequence';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('theatre_id, start_date, start_time, end_time, repeat_interval', 'required'),
			array('repeat_interval', 'numerical', 'integerOnly'=>true),
			array('theatre_id', 'length', 'max'=>10),
			array('end_date, week_selection, consultant, paediatric, anaesthetist, general_anaesthetic', 'safe'),
			array('start_date', 'date', 'format'=>'yyyy-MM-dd'),
			array('start_time', 'date', 'format'=>array('H:mm', 'H:mm:ss')),
			array('end_time', 'date', 'format'=>array('H:mm', 'H:mm:ss')),
			array('end_date', 'checkDates'),
			array('end_time', 'checkTimes'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, theatre_id, start_date, start_time, end_time, end_date, consultant, paediatric, anaesthetist, repeat_interval, weekday, week_selection, firm_id, site_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'theatre' => array(self::BELONGS_TO, 'Theatre', 'theatre_id'),
			'firmAssignment' => array(self::HAS_ONE, 'SequenceFirmAssignment', 'sequence_id'),
			'firm' => array(self::HAS_ONE, 'Firm', 'firm_id', 'through' => 'firmAssignment'),
			'sessions' => array(self::HAS_MANY, 'Session', 'sequence_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'theatre_id' => 'Theatre',
			'start_date' => 'Start Date',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'end_date' => 'End Date',
			'repeat_interval' => 'Repeat',
			'anaesthetist' => 'Anaesthetist Present',
			'consultant' => 'Consultant Present',
			'general_anaesthetic' => 'GA Available',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('theatre_id',$this->theatre_id,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('weekday',$this->weekday);
		$criteria->with = array();
		if (!empty($this->week_selection) && in_array($this->week_selection, array_keys($this->getWeekSelectionOptions()))) {
			$criteria->addCondition("(week_selection & {$this->week_selection}) = {$this->week_selection}");
		} elseif ($this->repeat_interval != '') {
			$criteria->compare('repeat_interval',$this->repeat_interval);
			$criteria->compare('week_selection', 0);
		}
		if ($this->firm_id) {
			$criteria->together = true;
			$criteria->with[] = 'firmAssignment';
			$criteria->compare('firmAssignment.firm_id', (int)$this->firm_id);
		}
		if ($this->site_id) {
			$criteria->together = true;
			$criteria->with[] = 'theatre';
			$criteria->compare('theatre.site_id', (int)$this->site_id);
		}

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getFrequencyOptions() {
		return array(
			self::FREQUENCY_1WEEK => 'Every week',
			self::FREQUENCY_2WEEKS => 'Every 2 weeks',
			self::FREQUENCY_3WEEKS => 'Every 3 weeks',
			self::FREQUENCY_4WEEKS => 'Every 4 weeks',
			self::FREQUENCY_ONCE => 'One time',
			self::FREQUENCY_MONTHLY => 'Monthly',
		);
	}

	public function getWeekSelectionOptions() {
		return array(
			self::SELECT_1STWEEK => '1st in month',
			self::SELECT_2NDWEEK => '2nd in month',
			self::SELECT_3RDWEEK => '3rd in month',
			self::SELECT_4THWEEK => '4th in month',
			self::SELECT_5THWEEK => '5th in month',
		);
	}

	public function getFrequencyAndWeekOptions() {
		return array(
			self::FREQUENCY_1WEEK => 'Every week',
			self::FREQUENCY_2WEEKS => 'Every 2 weeks',
			self::FREQUENCY_3WEEKS => 'Every 3 weeks',
			self::FREQUENCY_4WEEKS => 'Every 4 weeks',
			self::FREQUENCY_ONCE => 'One time',
			(self::FREQUENCY_MONTHLY + self::SELECT_1STWEEK) => '1st in month',
			(self::FREQUENCY_MONTHLY + self::SELECT_2NDWEEK) => '2nd in month',
			(self::FREQUENCY_MONTHLY + self::SELECT_3RDWEEK) => '3rd in month',
			(self::FREQUENCY_MONTHLY + self::SELECT_4THWEEK) => '4th in month',
			(self::FREQUENCY_MONTHLY + self::SELECT_5THWEEK) => '5th in month',
		);
	}

	public function getSelectedFrequencyWeekOption() {
		if (!empty($this->week_selection)) {
			return self::FREQUENCY_MONTHLY + $this->week_selection;
		} else {
			return $this->repeat_interval;
		}
	}
	
	public function getWeekdayOptions() {
		return array(
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday',
			7 => 'Sunday',
		);
	}

	protected function beforeSave() {
		$startTime = strtotime($this->start_date);
		$this->start_date = date('Y-m-d', $startTime);
		$this->weekday = date('N', $startTime);
		if (empty($this->end_date)) {
			$this->setAttribute('end_date', null);
		} else {
			$this->end_date = date('Y-m-d', strtotime($this->end_date));
		}

		if (!empty($_POST['Sequence']['week_selection'])) {
			$selection = 0;
			foreach ($_POST['Sequence']['week_selection'] as $value) {
				$selection += $value;
			}
			$this->week_selection = $selection;
			$this->repeat_interval = self::FREQUENCY_MONTHLY;
		} else {
			$this->week_selection = 0;
		}

		return parent::beforeSave();
	}

	public function checkDates() {
		if (!empty($this->end_date)) {
			$start = strtotime($this->start_date);
			$end = strtotime($this->end_date);

			if ($end < $start) {
				$this->addError('end_date', 'End date must be after the start date.');
			}
		}
	}

	public function checkTimes() {
		$start = strtotime($this->start_time);
		$end = strtotime($this->end_time);

		if ($end <= $start) {
			$this->addError('end_time', 'End time must be after the start time.');
		}
	}

	protected function beforeValidate() {
		$startTimestamp = strtotime($this->start_date);
		$endTimestamp = !empty($this->end_date) ? strtotime($this->end_date) : strtotime('+100 years');

		$startDate = date('Y-m-d', $startTimestamp);
		$endDate = date('Y-m-d', $endTimestamp);
		$startTime = date('H:i:s', strtotime($this->start_time));
		$endTime = date('H:i:s', strtotime($this->end_time));

		$weekday = date('N', $startTimestamp);
		$endTimeLimit = strtotime('+12 weeks', $startTimestamp);

		if (empty($this->week_selection)) {
			$interval = $this->getFrequencyInteger($this->repeat_interval, $endTimestamp);

			$dateList = array();
			for ($i = $startTimestamp; $i <= $endTimestamp && $i <= $endTimeLimit; $i += $interval) {
				$dateList[] = date('Y-m-d H:i:s', $i);
			}
		} else {
			$dateList = $this->getWeekOccurrences($weekday, $this->week_selection, $startTimestamp, $endTimestamp, $startDate, $endDate);
		}

		$bookedList = $this->getBookedList($this->theatre_id, $weekday, $startDate, $endDate, $startTime, $endTime, $startTimestamp, $endTimestamp, $endTimeLimit);

		$conflicts = array_intersect($bookedList, $dateList);

		$valid = true;
		if (!empty($conflicts)) {
			$valid = false;
			$this->addError('start_date, end_date', 'There is a conflict with an existing sequence.');
		}

		// check for one-off sequence and empty end date
		if (!empty($this->start_date) && empty($this->week_selection) && $this->repeat_interval == self::FREQUENCY_ONCE && empty($this->end_date)) {
			$valid = false;
			$this->addError('end_date', 'End date must be set if repeat is set to one time.');
		}

		$valid = $valid && parent::beforeValidate();
		return $valid;
	}

	protected function getBookedList($theatreId, $weekday, $startDate, $endDate, $startTime, $endTime, $scheduleStartTime, $scheduleEndTime, $endTimeLimit) {
		$sequences = Yii::app()->db->createCommand()
			->select('*')
			->from('sequence s')
			->where('theatre_id = :tid AND start_date <= :end_date AND
				id != :sequence_id AND weekday = :weekday AND
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
			// if we're doing every X weeks
			if (empty($value['week_selection'])) {
				$interval = $this->getFrequencyInteger($value['repeat_interval'], $endTimestamp);

				for ($i = $startTimestamp; $i <= $endTimestamp; $i += $interval) {
					$bookedList[] = date('Y-m-d H:i:s', $i);
				}
			// otherwise we're using specific weeks in every month
			} else {
				$bookedList = $this->getWeekOccurrences($value['weekday'], $value['week_selection'], $startTimestamp, $endTimestamp, $startDate, $endDate);
			}
		}

		return $bookedList;
	}

	public function getWeekOccurrences($weekday, $weekSelection, $startTimestamp, $endTimestamp, $startDate, $endDate) {
		$dates = array();
		$month = strtotime(date('Y-m-01',$startTimestamp));
		$weekday_options = $this->getWeekdayOptions();
		$weekday_string = $weekday_options[$weekday];
		while($month <= $endTimestamp) {
			$day = strtotime("first $weekday_string of", $month);
			for ($i = self::SELECT_1STWEEK; $i <= self::SELECT_5THWEEK; $i *= 2) {
				// Only add date if it is between start and end dates, and is a selected week. Also check we haven't rolled over into the next month (4 week months) 
				if($day >= $startTimestamp && $day <= $endTimestamp && $day <= strtotime('last day of', $month) && ($weekSelection & $i)) {
					$dates[] = date('Y-m-d',$day);
				}
				$day = strtotime("+1 week", $day);
			}
			$month = strtotime("+1 month", $month);
		}
		return $dates;
	}

	public function getFrequencyInteger($frequency, $endTimestamp) {
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
			case self::FREQUENCY_MONTHLY:
				$interval = $endTimestamp + 1;
				break;
		}
		return $interval;
	}

	public function getFirmName() {
		if($this->firm) {
			return $this->firm->name . ' (' . $this->firm->serviceSubspecialtyAssignment->subspecialty->name . ')';
		} else {
			return 'None';
		}
	}

	public function getTheatreName() {
		if($this->theatre) {
			return $this->theatre->name . ' (' . $this->theatre->site->short_name . ')';
		} else {
			return 'None';
		}
	}
	
	public function getTimeSlot() {
		return date('H:i',strtotime($this->start_time)) . ' - ' . date('H:i',strtotime($this->end_time));
	}
	
	public function getFrequencyText() {
		switch($this->repeat_interval) {
			case self::FREQUENCY_ONCE:
				$text = 'Once';
				break;
			case self::FREQUENCY_1WEEK:
				$text = 'Every week';
				break;
			case self::FREQUENCY_2WEEKS:
				$text = 'Every 2 weeks';
				break;
			case self::FREQUENCY_3WEEKS:
				$text = 'Every 3 weeks';
				break;
			case self::FREQUENCY_4WEEKS:
				$text = 'Every 4 weeks';
				break;
			case self::FREQUENCY_MONTHLY:
				$text = 'Monthly';
				break;
			default:
				$text = 'Unknown';
				break;
		}

		return $text;
	}

	public function getWeekText() {
		$weeks = array();
		foreach ($this->getWeekSelectionOptions() as $id => $text) {
			if ($this->week_selection & $id) {
				$weeks[] = substr($text, 0, 3);
			}
		}

		$result = implode(' & ', $weeks);
		$result .= date(' l', strtotime($this->start_date));

		return $result;
	}

	public function getWeekdayText() {
		$options = $this->getWeekdayOptions();
		if(isset($options[$this->weekday])) {
			return $options[$this->weekday];
		} else {
			return '-';
		}
	}

	public function getRepeatText() {
		if (!empty($this->week_selection)) {
			return $this->getWeekText();
		} else {
			return $this->getFrequencyText() . ' on ' . $this->getWeekdayText();
		}
	}

	public function getBookingCount() {
		return Yii::app()->db->createCommand()
			->select('COUNT(b.id) AS bookings_count')
			->from('sequence q')
			->join('session s', 'q.id = s.sequence_id')
			->join('booking b', 's.id = b.session_id')
			->where('q.id = :id' , array(':id' => $this->id))
			->queryScalar();
	}
}
