<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

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
 * @property data $decision_date
 * @property integer $schedule_timeframe
 * @property boolean $urgent
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

	const LETTER_INVITE = 0;
	const LETTER_REMINDER_1 = 1;
	const LETTER_REMINDER_2 = 2;
	const LETTER_GP = 3;
	const LETTER_REMOVAL = 4;
	
	const URGENT = 1;
	const ROUTINE = 0;

	// these reflect an actual status, relating to actions required rather than letters sent
	const STATUS_WHITE = 0; // no action required.  the default status.
	const STATUS_PURPLE = 1; // no invitation letter has been sent
	const STATUS_GREEN1 = 2; // it's two weeks since an invitation letter was sent with no further letters going out
	const STATUS_GREEN2 = 3; // it's two weeks since 1st reminder was sent with no further letters going out
	const STATUS_ORANGE = 4; // it's two weeks since 2nd reminder was sent with no further letters going out
	const STATUS_RED = 5; // it's one week since gp letter was sent and they're still on the list
	const STATUS_NOTWAITING = null;

	public $service;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementOperation the static model class
	 */
	public static function model($className = __CLASS__)
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
			array('decision_date', 'OeDateValidator', 'message' => 'Please enter a valid decision date (e.g. '.Helper::NHS_DATE_EXAMPLE.')'),
			array('eye, total_duration, consultant_required, anaesthetist_required, anaesthetic_type, overnight_stay, schedule_timeframe, urgent', 'numerical', 'integerOnly' => true),
			array('eye, event_id, comments, decision_date, site_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, eye, comments, total_duration, decision_date, consultant_required, anaesthetist_required, anaesthetic_type, overnight_stay, schedule_timeframe, urgent, site_id', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * Define date fields which should be converted when saving to (or fetching from) the database
	 */
	/*
	public function behaviors() {
		return array(
			'OeDateFormat' => array(
				'class' => 'application.components.OeDateFormat',
				'date_columns' => array('decision_date'),
			),
		);
	}
	*/
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'procedures' => array(self::MANY_MANY, 'Procedure', 'operation_procedure_assignment(operation_id, proc_id)', 'order' => 'display_order ASC'),
			'booking' => array(self::HAS_ONE, 'Booking', 'element_operation_id'),
			'cancellation' => array(self::HAS_ONE, 'CancelledOperation', 'element_operation_id'),
			'cancelledBooking' => array(self::HAS_ONE, 'CancelledBooking', 'element_operation_id'),
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
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
			'decision_date' => 'Decision Date',
			'schedule_timeframe' => 'Schedule Timeframe',
			'urgent' => 'Priority',
			'site_id' => 'Site that this will be booked for'
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

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);
		$criteria->compare('eye', $this->eye);
		$criteria->compare('comments', $this->comments, true);
		$criteria->compare('total_duration', $this->total_duration);
		$criteria->compare('consultant_required', $this->consultant_required);
		$criteria->compare('anaesthetist_required', $this->anaesthetist_required);
		$criteria->compare('anaesthetic_type', $this->anaesthetic_type);
		$criteria->compare('overnight_stay', $this->overnight_stay);
		$criteria->compare('decision_date', $this->decision_date);
		$criteria->compare('schedule_timeframe', $this->schedule_timeframe);
		$criteria->compare('urgent', $this->urgent);
		$criteria->compare('site_id', $this->site_id);
		
		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	/**
	 * Set default values for forms on create
	 */
	public function setDefaultOptions()
	{
		$this->consultant_required = self::CONSULTANT_NOT_REQUIRED;
		$this->anaesthetic_type = self::ANAESTHETIC_TOPICAL;
		$this->overnight_stay = 0;
		$this->decision_date = date('Y-m-d', time());
		$this->total_duration = 0;
		$this->schedule_timeframe = self::SCHEDULE_IMMEDIATELY;
		$this->status = self::STATUS_PENDING;
		$this->urgent = self::ROUTINE;
	}

	/**
	 * Return list of options for eye
	 * @return array
	 */
	public function getEyeOptions()
	{
		return array(
			self::EYE_RIGHT => 'Right',
			self::EYE_LEFT => 'Left',
			self::EYE_BOTH => 'Both',
		);
	}

	public function getEyeLabelText()
	{
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

	public function getEyeText()
	{
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

	/**
	 * Return list of priority options
	 * @return array
	 */
	public function getPriorityOptions() {
		return array(
			self::URGENT => 'Urgent',
			self::ROUTINE => 'Routine',
		);
	}

	public function getBooleanText($field)
	{
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
			self::ANAESTHETIC_LOCAL => 'LA',
			self::ANAESTHETIC_LOCAL_WITH_COVER => 'LAC',
			self::ANAESTHETIC_LOCAL_WITH_SEDATION => 'LAS',
			self::ANAESTHETIC_GENERAL => 'GA'
		);
	}

	public function getAnaestheticText()
	{
		switch ($this->anaesthetic_type) {
			case self::ANAESTHETIC_TOPICAL:
				$text = 'Topical';
				break;
			case self::ANAESTHETIC_LOCAL:
				$text = 'LA';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_COVER:
				$text = 'LAC';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_SEDATION:
				$text = 'LAS';
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

	public function getAnaestheticAbbreviation()
	{
		switch ($this->anaesthetic_type) {
			case self::ANAESTHETIC_TOPICAL:
				$text = 'Topical';
				break;
			case self::ANAESTHETIC_LOCAL:
				$text = 'LA';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_COVER:
				$text = 'LAC';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_SEDATION:
				$text = 'LAS';
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

	public function getScheduleText()
	{
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
			isset($_POST['ElementOperation']['eye'])
		) {
			$diagnosis = $_POST['ElementDiagnosis']['eye'];
			$operation = $_POST['ElementOperation']['eye'];
			if ($diagnosis != ElementDiagnosis::EYE_BOTH &&
				$diagnosis != $operation
			) {
				$this->addError('eye', 'Operation eye must match diagnosis eye!');
			}
		}
	}

	protected function beforeSave()
	{
		# echo $_POST['site_id'] . "fish"; exit;
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

		$operationId = $this->id;
		$order = 1;

		if (!empty($_POST['Procedures'])) {
			// first wipe out any existing procedures so we start from scratch
			OperationProcedureAssignment::model()->deleteAll('operation_id = :id', array(':id' => $operationId));

			foreach ($_POST['Procedures'] as $id) {
				$procedure = new OperationProcedureAssignment;
				$procedure->operation_id = $operationId;
				$procedure->proc_id = $id;
				$procedure->display_order = $order;
				if (!$procedure->save()) {
					throw new Exception('Unable to save procedure');
				}

				$order++;
			}
		}
		return parent::afterSave();
	}

	protected function beforeValidate()
	{
		if (!empty($_POST['action']) && empty($_POST['Procedures'])) {
			$this->addError('eye', 'At least one procedure must be entered');
		}

		return parent::beforeValidate();
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

	public function getSessions($emergency = false)
	{
		$minDate = $this->getMinDate();
		$thisMonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
		if ($minDate < $thisMonth) {
			$minDate = $thisMonth;
		}

		$monthStart = empty($_GET['date']) ? date('Y-m-01', $minDate) : $_GET['date'];

		if (!$emergency) {
			$firmId = empty($_GET['firm']) ? $this->event->episode->firm_id : $_GET['firm'];
		} else {
			$firmId = null;
		}

		$service = $this->getBookingService();
		$sessions = $service->findSessions($monthStart, $minDate, $firmId);

		$results = array();
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
			$dateList = array_keys($dates);
			while (date('N', strtotime($dateList[0])) != date('N', $firstWeekday)) {
				$firstWeekday -= 60 * 60 * 24;
			}

			for ($weekCounter = 1; $weekCounter < 8; $weekCounter++) {
				$addDays = ($weekCounter - 1) * 7;
				$selectedDay = date('Y-m-d', mktime(0, 0, 0, date('m', $firstWeekday), date('d', $firstWeekday) + $addDays, date('Y', $firstWeekday)));
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

	public function getTheatres($date, $emergency = false)
	{
		if (empty($date)) {
			throw new Exception('Date is required.');
		}

		if (empty($emergency) || $emergency == 'EMG') {
			$firmId = null;
		} else {
			$firmId = $emergency;
		}

		$service = $this->getBookingService();
		$sessions = $service->findTheatres($date, $firmId);

		$results = array();
		$names = array();
		foreach ($sessions as $session) {
			$theatre = Theatre::model()->findByPk($session['id']);

			$name = $session['name'] . ' (' . $theatre->site->short_name . ')';
			$sessionTime = explode(':', $session['session_duration']);
			$session['duration'] = ($sessionTime[0] * 60) + $sessionTime[1];
			$session['time_available'] = $session['duration'] - $session['bookings_duration'];
			$session['id'] = $session['session_id'];
			unset($session['session_duration'], $session['date'], $session['name']);

			// Add status field to indicate if session is full or not
			if ($session['time_available'] <= 0) {
				$session['status'] = 'full';
			} else {
				$session['status'] = 'available';
			}

			// Add bookable field to indicate if session can be booked for this operation
			$bookable = ($session['time_available'] > 0);
			if($bookable) {
				if($this->anaesthetist_required && !$session['anaesthetist']) {
					$bookable = false;
				}
				if($this->consultant_required && !$session['consultant']) {
					$bookable = false;
				}
				$paediatric = ($this->event->episode->patient->getAge() < 16);
				if($paediatric && !$session['paediatric']) {
					$bookable = false;
				}
			}
			$session['bookable'] = $bookable;
			
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
		switch ($index) {
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

	public function getWardOptions($siteId, $theatreId = null)
	{
		if (empty($siteId)) {
			throw new Exception('Site id is required.');
		}
		$results = array();
		// if we have a theatre id, see if it has an associated ward
		if (!empty($theatreId)) {
			$ward = Yii::app()->db->createCommand()
				->select('t.ward_id AS id, w.name')
				->from('theatre_ward_assignment t')
				->join('ward w', 't.ward_id = w.id')
				->where('t.theatre_id = :id', array(':id' => $theatreId))
				->queryRow();

			if (!empty($ward)) {
				$results[$ward['id']] = $ward['name'];
			}
		}

		if (empty($results)) {
			// otherwise select by site and patient age/gender
			$patient = $this->event->episode->patient;

			$genderRestrict = $ageRestrict = 0;
			$genderRestrict = ('M' == $patient->gender) ? Ward::RESTRICTION_MALE : Ward::RESTRICTION_FEMALE;
			$ageRestrict = ($patient->getAge() < 16) ? Ward::RESTRICTION_UNDER_16 : Ward::RESTRICTION_ATLEAST_16;

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
		}

		return $results;
	}

	public function getService()
	{
		if (empty($this->service)) {
			$this->service = new LetterOutService($this->event->episode->firm);
		}

		return $this->service;
	}

	public function getPhrase($name)
	{
		return $this->getService()->getPhrase('LetterOut', $name);
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

	public function getStatusText()
	{
		switch ($this->status) {
			case self::STATUS_PENDING:
				$status = 'Requires scheduling';
				break;
			case self::STATUS_SCHEDULED:
				$status = 'Scheduled';
				break;
			case self::STATUS_NEEDS_RESCHEDULING:
				$status = 'Requires rescheduling';
				break;
			case self::STATUS_RESCHEDULED:
				$status = 'Rescheduled';
				break;
			case self::STATUS_CANCELLED:
				$status = 'Cancelled';
				break;
			default:
				$status = 'Unknown status';
				break;
		}

		return $status;
	}

	/**
	 * Returns the letter status for an operation.
	 *
	 * Checks to see if it's an operation to be scheduled or an operation to be rescheduled. If it's the former it bases its calculation
	 *	 on the operation creation date. If it's the latter it bases it on the most recent cancelled_booking creation date.
  	 *
	 * return int
	 */
	public function getWaitingListStatus()
	{
		# these reflect an actual status, relating to actions required rather than letters sent
		# const STATUS_WHITE = 0; // no action required.  the default status.
		# const STATUS_PURPLE = 1; // no invitation letter has been sent
		# const STATUS_GREEN1 = 2; // it's two weeks since an invitation letter was sent with no further letters going out
		# const STATUS_GREEN2 = 3; // it's two weeks since 1st reminder was sent with no further letters going out
		# const STATUS_ORANGE = 4; // it's two weeks since 2nd reminder was sent with no further letters going out
		# const STATUS_RED = 5; // it's one week since gp letter was sent and they're still on the list
		# const STATUS_NOTWAITING = null;


	}
	public function getWaitingListLetterStatus()
	{
		echo var_export($this->date_letter_sent,true); exit;
	}
	public function getLastLetter()
	{
		
	}
	public function getNextLetter()
	{

	}
	public function getLetterStatus()
	{
		if ($this->status == self::STATUS_NEEDS_RESCHEDULING && !empty($this->cancelledBooking)) {
			$criteria = new CDbCriteria;
			$criteria->addCondition('element_operation_id = :eoid');
			$criteria->params = array('eoid' => $this->id);
			$criteria->order = 'id DESC';
			$criteria->limit = 1;
                	$cancelledBooking = CancelledBooking::model()->find($criteria);

			$datetime = strtotime($cancelledBooking->cancelled_date);
		} else {
			$datetime = strtotime($this->event->datetime);
		}

		$now = time();
		$week = 86400 * 7;

		if ($datetime >= ($now - 2 * $week)) {
			$letterStatus = self::LETTER_INVITE;
		} elseif (
			$datetime >= ($now - 4 * $week) &&
			$datetime < ($now - 2 * $week)
		) {
			$letterStatus = self::LETTER_REMINDER_1;
		} elseif (
			$datetime >= ($now - 6 * $week) &&
			$datetime < ($now - 4 * $week)
		) {
			$letterStatus = self::LETTER_REMINDER_2;
		} elseif (
			$datetime >= ($now - 8 * $week) &&
			$datetime < ($now - 6 * $week)
		) {
			$letterStatus = self::LETTER_GP;
		} elseif (
			$datetime < ($now - 8 * $week)
		) {
			$letterStatus = self::LETTER_REMOVAL;
		}

		return $letterStatus;
	}

	public static function getLetterOptions()
	{
		return array(
			'' => 'Any',
			self::LETTER_INVITE => 'Invitation',
			self::LETTER_REMINDER_1 => '1st Reminder',
			self::LETTER_REMINDER_2 => '2nd Reminder',
			self::LETTER_GP => 'Refer to GP',
			self::LETTER_REMOVAL => 'To be removed'
		);
	}

	/**
	 * Get the diagnosis for this operation. Used by the booking event type template to create the admission form.
	 *
	 * @return string
	 */
	public function getDisorder()
	{
		$eventId = $this->event_id;

		$elementDiagnosis = ElementDiagnosis::model()->find('event_id = ?', array($eventId));

		if (empty($elementDiagnosis)) {
			return null;
		} else {
			return $elementDiagnosis->disorder->term;
		}
	}

	public function getDisorderEyeText() {
		$eventId = $this->event_id;

		$elementDiagnosis = ElementDiagnosis::model()->find('event_id = ?', array($eventId));

		if (empty($elementDiagnosis)) {
			return null;
		} else {
			return $elementDiagnosis->getEyeText();
		}
	}

	/**
	 * Returns an array of cancelled bookings
	 *
	 * @return array
	 */
	public function getCancelledBookings()
	{
		if ($this->status == self::STATUS_PENDING || $this->status == self::STATUS_SCHEDULED) {
			// Can't be any cancelled bookings, return empty array
			return array();
		}

		$cbs = CancelledBooking::model()->findAll(
			array(
				'order' => 'id DESC',
				'condition' => 'element_operation_id = :eoid',
				'params' => array(
					':eoid' => $this->id
				)
			)
		);

		return $cbs;
	}

	/**
	 * Used by the booking event type template to format the date.
	 *
	 * @param string date
	 * @return string
	 */
	public function convertDate($date)
	{
		return date('l jS F Y', strtotime($date));
	}

	/**
	 * Used by the booking event to display the admission time (session start time minus one hour)
	 *
	 * @param string $time
	 * @return string
	 */
	public function convertTime($time)
	{
		return date('G:i:s', strtotime('-1 hour', strtotime($time)));
	}

	/**
	 * Move the operation up or down within the session
	 *
	 * @param boolean $up
	 */
	public function move($up)
	{
		$booking = $this->booking;

		$criteria=new CDbCriteria;
		$criteria->addCondition('session_id = :sid');

		if ($up) {
			// Moving up the page means moving down the display_order
			$criteria->addCondition('display_order < :do');
			$criteria->order = 'display_order DESC';
		} else {
			$criteria->addCondition('display_order > :do');
			$criteria->order = 'display_order ASC';
		}

		$criteria->params = array(':sid' => $booking->session_id, ':do' => $booking->display_order);
		$criteria->limit = 1;

		$otherBooking = Booking::model()->find($criteria);

		if (empty($otherBooking)) {
			return false;
		}

		$otherDisplayOrder = $otherBooking->display_order;

		$otherBooking->display_order = $booking->display_order;
		$booking->display_order = $otherDisplayOrder;

		if (!$booking->save()) {
			throw new SystemException('Unable to save booking: '.print_r($booking->getErrors(),true));
		}

		if (!$otherBooking->save()) {
			throw new SystemException('Unable to save booking: '.print_r($otherBooking->getErrors(),true));
		}

		return true;
	}

}
