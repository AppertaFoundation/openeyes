<?php

/**
 * This is the model class for table "element_operation".
 *
 * The followings are the available columns in table 'element_operation':
 * @property string $id
 * @property string $event_id
 * @property integer $eye
 * @property string $comments
 * @property integer $total_duration
 * @property integer $consultant_required
 * @property integer $anaesthetist_required
 * @property integer $anaesthetic_type
 * @property integer $overnight_stay
 * @property integer $schedule_timeframe
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property Procedure[] $procedures
 */
class ElementOperation extends BaseElement
{
	const EYE_LEFT = 0;
	const EYE_RIGHT = 1;
	const EYE_BOTH = 2;

	const CONSULTANT_NOT_REQUIRED = 0;
	const CONSULTANT_REQUIRED = 1;

	const ANAESTHETIC_TOPICAL = 0;
	const ANAESTHETIC_LOCAL_WITH_COVER = 1;
	const ANAESTHETIC_LOCAL = 2;
	const ANAESTHETIC_LOCAL_WITH_SEDATION = 3;
	const ANAESTHETIC_GENERAL = 4;

	const SCHEDULE_IMMEDIATELY = 0;
	const SCHEDULE_AFTER_1MO = 1;
	const SCHEDULE_AFTER_2MO = 2;
	const SCHEDULE_AFTER_3MO = 3;
	
	const STATUS_PENDING = 0;
	const STATUS_SCHEDULED = 1;
	const STATUS_NEEDS_RESCHEDULING = 2;
	const STATUS_RESCHEDULED = 3;
	const STATUS_CANCELLED = 4;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementOperation the static model class
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
		return 'element_operation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('eye', 'required', 'message' => 'Please select an eye option'),
			array('eye', 'matchDiagnosisEye'),
			array('decision_date', 'required', 'message' => 'Please enter a decision date'),
			array('eye, total_duration, consultant_required, anaesthetist_required, anaesthetic_type, overnight_stay, schedule_timeframe', 'numerical', 'integerOnly'=>true),
			array('eye, event_id, comments, decision_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, eye, comments, total_duration, decision_date, consultant_required, anaesthetist_required, anaesthetic_type, overnight_stay, schedule_timeframe', 'safe', 'on'=>'search'),
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
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'procedures' => array(self::MANY_MANY, 'Procedure', 'operation_procedure_assignment(operation_id, procedure_id)', 'order' => 'display_order ASC'),
			'booking' => array(self::HAS_ONE, 'Booking', 'element_operation_id'),
			'cancellation' => array(self::HAS_ONE, 'CancelledOperation', 'element_operation_id')
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
			'eye' => 'Eye(s)',
			'comments' => 'Comments',
			'total_duration' => 'Total Duration',
			'consultant_required' => 'Consultant Required',
			'anaesthetist_required' => 'Anaesthetist Required',
			'anaesthetic_type' => 'Anaesthetic Type',
			'overnight_stay' => 'Overnight Stay',
			'schedule_timeframe' => 'Schedule Timeframe',
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
		$criteria->compare('eye',$this->eye);
		$criteria->compare('comments',$this->comments,true);
		$criteria->compare('total_duration',$this->total_duration);
		$criteria->compare('consultant_required',$this->consultant_required);
		$criteria->compare('anaesthetist_required',$this->anaesthetist_required);
		$criteria->compare('anaesthetic_type',$this->anaesthetic_type);
		$criteria->compare('overnight_stay',$this->overnight_stay);
		$criteria->compare('schedule_timeframe',$this->schedule_timeframe);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Set default values for forms on create
	 */
	public function setDefaultOptions()
	{
		$this->consultant_required = self::CONSULTANT_REQUIRED;
		$this->anaesthetic_type = self::ANAESTHETIC_TOPICAL;
		$this->overnight_stay = 0;
		$this->total_duration = 0;
		$this->schedule_timeframe = self::SCHEDULE_IMMEDIATELY;
		$this->status = self::STATUS_PENDING;
	}

	/**
	 * Return list of options for eye
	 * @return array
	 */
	public function getEyeOptions()
	{
		return array(
			self::EYE_LEFT => 'Left',
			self::EYE_RIGHT => 'Right',
			self::EYE_BOTH => 'Both',
		);
	}

	public function getEyeLabelText() {
		switch ($this->eye) {
			case self::EYE_BOTH:
				$text = 'Eyes:';
				break;
			default:
				$text = 'Eye:';
				break;
		}

		return $text;
	}

	public function getEyeText() {
		switch ($this->eye) {
			case self::EYE_LEFT:
				$text = 'Left';
				break;
			case self::EYE_RIGHT:
				$text = 'Right';
				break;
			case self::EYE_BOTH:
				$text = 'Both';
				break;
			default:
				$text = 'Unknown';
				break;
		}

		return $text;
	}

	/**
	 * Return list of options for consultant
	 * @return array
	 */
	public function getConsultantOptions()
	{
		return array(
			self::CONSULTANT_REQUIRED => 'Yes',
			self::CONSULTANT_NOT_REQUIRED => 'No',
		);
	}

	public function getBooleanText($field) {
		switch ($this->$field) {
			case 1:
				$text = 'Yes';
				break;
			default:
				$text = 'No';
				break;
		}

		return $text;
	}

	/**
	 * Return list of options for anaesthetic type
	 * @return array
	 */
	public function getAnaestheticOptions()
	{
		return array(
			self::ANAESTHETIC_TOPICAL => 'Topical',
			self::ANAESTHETIC_LOCAL => 'Local',
			self::ANAESTHETIC_LOCAL_WITH_COVER => 'Local with cover',
			self::ANAESTHETIC_LOCAL_WITH_SEDATION => 'Local with sedation',
			self::ANAESTHETIC_GENERAL => 'General'
		);
	}

	public function getAnaestheticText() {
		switch ($this->anaesthetic_type) {
			case self::ANAESTHETIC_TOPICAL:
				$text = 'Topical';
				break;
			case self::ANAESTHETIC_LOCAL:
				$text = 'Local';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_COVER:
				$text = 'Local with cover';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_SEDATION:
				$text = 'Local with sedation';
				break;
			case self::ANAESTHETIC_GENERAL:
				$text = 'General';
				break;
			default:
				$text = 'Unknown';
				break;
		}

		return $text;
	}

	public function getAnaestheticAbbreviation() {
		switch ($this->anaesthetic_type) {
			case self::ANAESTHETIC_TOPICAL:
				$text = 'TOP';
				break;
			case self::ANAESTHETIC_LOCAL:
				$text = 'LOC';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_COVER:
				$text = 'LWC';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_SEDATION:
				$text = 'LWS';
				break;
			case self::ANAESTHETIC_GENERAL:
				$text = 'GA';
				break;
			default:
				$text = 'Unknown';
				break;
		}

		return $text;
	}

	/**
	 * Return list of options for schedule
	 * @return array
	 */
	public function getScheduleOptions()
	{
		return array(
			self::SCHEDULE_IMMEDIATELY => 'As soon as possible',
			1 => 'Within timeframe specified by patient'
		);
	}

	/**
	 * Return list of options for schedule timeframe
	 * @return array
	 */
	public function getScheduleDelayOptions()
	{
		return array(
			self::SCHEDULE_AFTER_1MO => 'After 1 Month',
			self::SCHEDULE_AFTER_2MO => 'After 2 Months',
			self::SCHEDULE_AFTER_3MO => 'After 3 Months',
		);
	}

	public function getScheduleText() {
		switch ($this->schedule_timeframe) {
			case self::SCHEDULE_IMMEDIATELY:
				$text = 'Immediately';
				break;
			case self::SCHEDULE_AFTER_1MO:
				$text = 'After 1 month';
				break;
			case self::SCHEDULE_AFTER_2MO:
				$text = 'After 2 months';
				break;
			case self::SCHEDULE_AFTER_3MO:
				$text = 'After 3 months';
				break;
			default:
				$text = 'Unknown';
				break;
		}

		return $text;
	}

	/**
	 * Return list of options for overnight stay
	 * @return array
	 */
	public function getOvernightOptions()
	{
		return array(
			1 => 'Yes',
			0 => 'No',
		);
	}

	public function matchDiagnosisEye()
	{
		if (isset($_POST['ElementDiagnosis']['eye']) &&
			isset($_POST['ElementOperation']['eye'])) {
			$diagnosis = $_POST['ElementDiagnosis']['eye'];
			$operation = $_POST['ElementOperation']['eye'];
			if ($diagnosis != ElementDiagnosis::EYE_BOTH &&
				$diagnosis != $operation) {
				$this->addError('eye', 'Operation eye must match diagnosis eye!');
			}
		}
	}

	protected function beforeSave()
	{
		$anaesthetistRequired = array(
			self::ANAESTHETIC_LOCAL_WITH_COVER, self::ANAESTHETIC_LOCAL_WITH_SEDATION,
			self::ANAESTHETIC_GENERAL
		);
		$this->anaesthetist_required = in_array($this->anaesthetic_type, $anaesthetistRequired);

		if (!empty($_POST['schedule_timeframe2'])) {
			$this->schedule_timeframe = $_POST['schedule_timeframe2'];
		} else {
			$this->schedule_timeframe = self::SCHEDULE_IMMEDIATELY;
		}

		return parent::beforeSave();
	}

	protected function afterSave()
	{
		parent::afterSave();

		$operationId = $this->id;
		$order = 1;

		if (!empty($_POST['Procedures'])) {
			// first wipe out any existing procedures so we start from scratch
			OperationProcedureAssignment::model()->deleteAll('operation_id = :id',
				array(':id' => $operationId));
			
			foreach ($_POST['Procedures'] as $id) {
				$procedure = new OperationProcedureAssignment;
				$procedure->operation_id = $operationId;
				$procedure->procedure_id = $id;
				$procedure->display_order = $order;
				if (!$procedure->save()) {
					throw new Exception('Unable to save procedure');
				}

				$order++;
			}
		}
	}
	
	public function getMinDate()
	{
		$date = strtotime($this->event->datetime);
		
		if ($this->schedule_timeframe != self::SCHEDULE_IMMEDIATELY) {
			$interval = str_replace('After ', '+', $this->getScheduleText());
			$date = strtotime($interval, $date);
		}
		
		return $date;
	}
	
	public function getSessions()
	{
		$minDate = $this->getMinDate();
		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));
		if ($minDate < $thisMonth) {
			$minDate = $thisMonth;
		}
		
		$monthStart = empty($_GET['date']) ? date('Y-m-01', $minDate) : $_GET['date'];
		
		$firmId = empty($_GET['firm']) ? $this->event->episode->firm_id : $_GET['firm'];
		
		$service = $this->getBookingService();
		$sessions = $service->findSessions($monthStart, $minDate, $firmId);
		
		$results = array();
		$prevWeekday = -1;
		foreach ($sessions as $session) {
			$date = $session['date'];
			$weekday = date('N', strtotime($date));
			$text = $this->getWeekdayText($weekday);
			
			$sessionTime = explode(':', $session['session_duration']);
			$session['duration'] = ($sessionTime[0] * 60) + $sessionTime[1];
			$session['time_available'] = $session['duration'] - $session['bookings_duration'];
			unset($session['session_duration'], $session['date']);
			
			$results[$text][$date]['sessions'][] = $session;
		}
		
		foreach ($results as $weekday => $dates) {
			$timestamp = strtotime($monthStart);
			$firstWeekday = strtotime(date('Y-m-t', $timestamp - (60 * 60 * 24)));
			$lastMonthday = strtotime(date('Y-m-t', $timestamp));
			$dateList = array_keys($dates);
			while (date('N', strtotime($dateList[0])) != date('N', $firstWeekday)) {
				$firstWeekday -= 60 * 60 * 24;
			}
			
			for ($weekCounter = 1; $weekCounter < 8; $weekCounter++) {
				$addDays = ($weekCounter - 1) * 7;
				$selectedDay = date('Y-m-d', mktime(0,0,0, date('m', $firstWeekday), date('d', $firstWeekday)+$addDays, date('Y', $firstWeekday)));
				if (in_array($selectedDay, $dateList)) {
					foreach ($dates[$selectedDay] as $sessions) {
						$totalSessions = count($sessions);
						$status = $totalSessions;

						$open = $full = 0;

						foreach ($sessions as $session) {
							if ($session['time_available'] >= $this->total_duration) {
								$open++;
							} else {
								$full++;
							}
						}
						if ($full == $totalSessions) {
							$status = 'full';
						} elseif ($full > 0 && $open > 0) {
							$status = 'limited';
						} elseif ($open == $totalSessions) {
							$status = 'available';
						}
					}
				} else {
					$status = 'closed';
				}
				$results[$weekday][$selectedDay]['status'] = $status;
			}
		}
		
		foreach ($results as $weekday => &$dates) {
			$dateSort = array();
			foreach ($dates as $date => $info) {
				$dateSort[] = $date;
			}
			
			array_multisort($dateSort, SORT_ASC, $dates);
		}
		
		return $results;
	}
	
	public function getTheatres($date)
	{
		if (empty($date)) {
			throw new Exception('Date is required.');
		}
		$firmId = empty($_GET['firm']) ? $this->event->episode->firm_id : $_GET['firm'];
		
		$service = $this->getBookingService();
		$sessions = $service->findTheatres($date, $firmId);
		
		$results = array();
		$names = array();
		foreach ($sessions as $session) {
			$name = $session['name'];
			$sessionTime = explode(':', $session['session_duration']);
			$session['duration'] = ($sessionTime[0] * 60) + $sessionTime[1];
			$session['time_available'] = $session['duration'] - $session['bookings_duration'];
			$session['id'] = $session['session_id'];
			unset($session['session_duration'], $session['date'], $session['name']);
			
			if ($session['time_available'] <= 0) {
				$status = 'full';
			} else {
				$status = 'available';
			}
			$session['status'] = $status;
			
			$results[$name][] = $session;
			if (!in_array($name, $names)) {
				$names[] = $name;
			}
		}
		
		if (count($results) > 1) {
			array_multisort($names, SORT_ASC, $results);
		}
		
		return $results;
	}
	
	public function getSession($sessionId)
	{
		if (empty($sessionId)) {
			throw new Exception('Session id is invalid.');
		}
		$service = $this->getBookingService();
		$results = $service->findSession($sessionId);
		
		$session = $results->read();
		if (!empty($session['name'])) {
			$name = $session['name'];
			$sessionTime = explode(':', $session['session_duration']);
			$session['duration'] = ($sessionTime[0] * 60) + $sessionTime[1];
			$session['time_available'] = $session['duration'] - $session['bookings_duration'];
			unset($session['session_duration'], $session['name']);
			
			if ($session['time_available'] <= 0) {
				$status = 'full';
			} else {
				$status = 'available';
			}
			$session['status'] = $status;
		} else {
			$session = false;
		}
		
		return $session;
	}
	
	public function getBookingService()
	{
		return new BookingService;
	}
	
	public function getWeekdayText($index)
	{
		switch($index) {
			case 1:
				$text = 'Monday';
				break;
			case 2:
				$text = 'Tuesday';
				break;
			case 3:
				$text = 'Wednesday';
				break;
			case 4:
				$text = 'Thursday';
				break;
			case 5:
				$text = 'Friday';
				break;
			case 6:
				$text = 'Saturday';
				break;
			case 7:
				$text = 'Sunday';
				break;
		}

		return $text;
	}

	public function getWardOptions($siteId)
	{
		if (empty($siteId)) {
			throw new Exception('Site id is required.');
		}
		$patient = $this->event->episode->patient;
		
		$genderRestrict = $ageRestrict = 0;
		$genderRestrict = ('M' == $patient->gender) 
			? Ward::RESTRICTION_MALE : Ward::RESTRICTION_FEMALE;
		$ageRestrict = ($patient->getAge() < 16)
			? Ward::RESTRICTION_UNDER_16 : Ward::RESTRICTION_ATLEAST_16;
		
		$whereSql = 's.id = :id AND 
			(w.restriction & :r1 > 0) AND (w.restriction & :r2 > 0)';
		$whereParams = array(
			':id' => $siteId,
			':r1' => $genderRestrict,
			':r2' => $ageRestrict
		);
		
		$wards = Yii::app()->db->createCommand()
			->select('w.id, w.name')
			->from('ward w')
			->join('site s', 's.id = w.site_id')
			->where($whereSql, $whereParams)
			->queryAll();
		
		$results = array();
		
		foreach ($wards as $ward) {
			$results[$ward['id']] = $ward['name'];
		}
		
		return $results;
	}
	
	public function getCancellationText()
	{
		$text = '';
		$cancellation = $this->cancellation;
		if (!empty($cancellation)) {
			$text = "Operation Cancelled: By " . $cancellation->user->first_name;
			$text .= ' ' . $cancellation->user->last_name . ' on ' . date('F j, Y', strtotime($cancellation->cancelled_date));
			$text .= ' [' . $cancellation->cancelledReason->text . ']';
		}
		
		return $text;
	}
}
