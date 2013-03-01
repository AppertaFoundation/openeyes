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

class BookingController extends BaseController
{
	public $model;
	public $firm;
	public $patient;
	public $title;
	public $editing;
	public $event;
	public $editable;

	public function filters()
	{
		return array('accessControl');
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@')
			),
			// non-logged in can't view anything
			array('deny',
				'users'=>array('?')
			),
		);
	}

	protected function beforeAction($action)
	{
		// Sample code to be used when RBAC is fully implemented.
//		if (!Yii::app()->user->checkAccess('admin')) {
//			throw new CHttpException(403, 'You are not authorised to perform this action.');
//		}
		
		return parent::beforeAction($action);
	}

	public function actionSchedule()
	{
		$operationId = (isset($_GET['operation'])) ? (int) $_GET['operation'] : 0;
		if(!$operationId) {
			throw new Exception('Operation id is invalid.');
		}
		$operation = ElementOperation::model()->findByPk($operationId);
		if(empty($operation)) {
			throw new Exception('Operation id is invalid.');
		}
		$minDate = $operation->getMinDate();
		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));
		if($minDate < $thisMonth) {
			$minDate = $thisMonth;
		}

		$firmId = empty($_GET['firmId']) ? Yii::app()->session['selected_firm_id'] : $_GET['firmId'];
		if ($firmId != 'EMG') {
			$_GET['firm'] = $firmId;
			$firm = Firm::model()->findByPk($firmId);
		} else {
			$firm = new Firm;
			$firm->name = 'Emergency List';
		}

		$sessions = $operation->getSessions($firm->name == 'Emergency List');

		$firmList = Firm::model()->getListWithSpecialties();

		$this->patient = $operation->event->episode->patient;
		$this->title = 'Schedule';
		$this->event = $operation->event;

		$this->renderPartial('/booking/_schedule',
			array('operation'=>$operation, 'date'=>$minDate,
				'sessions'=>$sessions, 'firm' => $firm, 'firmList' => $firmList
			),
			false, true);
	}

	public function actionReschedule()
	{
		$operationId = !empty($_GET['operation']) ? $_GET['operation'] : 0;
		$operation = ElementOperation::model()->findByPk($operationId);
		if (empty($operation)) {
			throw new Exception('Operation id is invalid.');
		}
		$minDate = $operation->getMinDate();
		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));
		if ($minDate < $thisMonth) {
			$minDate = $thisMonth;
		}

		$firmId = empty($_GET['firmId']) ? Yii::app()->session['selected_firm_id'] : $_GET['firmId'];
		if ($firmId != 'EMG') {
			$_GET['firm'] = $firmId;
			$firm = Firm::model()->findByPk($firmId);
		} else {
			$firm = new Firm;
			$firm->name = 'Emergency List';
		}
		if ($firm->name != 'Emergency List') {
			$siteList = Session::model()->getSiteListByFirm($firmId);
		} else {
			$siteList = Site::model()->getList();
		}

		$sessions = $operation->getSessions($firm->name == 'Emergency List');

		$firmList = Firm::model()->getListWithSpecialties();

		$this->patient = $operation->event->episode->patient;
		$this->title = 'Reschedule';

		$this->renderPartial('/booking/_reschedule',
			array(
				'operation'=>$operation,
				'date'=>$minDate,
				'sessions'=>$sessions,
				'firm' => $firm,
				'firmList' => $firmList,
				'firmId' =>	$firmId
			),
			false,
			true
		);
	}

	public function actionRescheduleLater()
	{
		$operationId = !empty($_GET['operation']) ? $_GET['operation'] : 0;
		$operation = ElementOperation::model()->findByPk($operationId);
		if (empty($operation)) {
			throw new Exception('Operation id is invalid.');
		}
		$minDate = $operation->getMinDate();
		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));
		if ($minDate < $thisMonth) {
			$minDate = $thisMonth;
		}

		$firmId = $operation->event->episode->firm_id;
		$firm = Firm::model()->findByPk($firmId);
		$sessions = $operation->getSessions($firm->name == 'Emergency List');

		$this->patient = $operation->event->episode->patient;
		$this->title = 'Reschedule later';

		$this->renderPartial('/booking/_reschedule_later',
			array(
				'operation'=>$operation,
				'date'=>$minDate,
				'sessions'=>$sessions#
			),
			false,
			true
		);
	}

	public function actionCancelOperation() {
		$errors = array();

		if (isset($_POST['cancellation_reason']) && isset($_POST['operation_id'])) {
			$operation = ElementOperation::model()->findByPk($_POST['operation_id']);
			if(!$operation) {
				throw new CHttpException(500,'Operation not found');
			}

			$comment = (isset($_POST['cancellation_comment'])) ? strip_tags(@$_POST['cancellation_comment']) : '';
			$result = $operation->cancel(@$_POST['cancellation_reason'], $comment);

			if ($result['result']) {
				$operation->event->deleteIssues();

				$audit = new Audit;
				$audit->action = "cancel";
				$audit->target_type = "event";
				$audit->patient_id = $operation->event->episode->patient_id;
				$audit->episode_id = $operation->event->episode_id;
				$audit->event_id = $operation->event_id;
				$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
				$audit->save();

				die(json_encode(array()));
			}

			foreach ($result['errors'] as $form_errors) {
				foreach ($form_errors as $error) {
					$errors[] = $error;
				}
			}

			die(json_encode($errors));
		}

		$operationId = !empty($_REQUEST['operation']) ? $_REQUEST['operation'] : 0;
		$operation = ElementOperation::model()->findByPk($operationId);
		if (empty($operation)) {
			throw new CHttpException(500,'Operation not found');
		}
		$minDate = $operation->getMinDate();
		$thisMonth = mktime(0,0,0,date('m'),1,date('Y'));
		if ($minDate < $thisMonth) {
			$minDate = $thisMonth;
		}

		$this->patient = $operation->event->episode->patient;
		$this->title = 'Cancel operation';

		$this->renderPartial('/booking/_cancel_operation', array(
				'operation' => $operation,
				'date' => $minDate,
				'errors' => $errors
			), false, true);
	}

	public function actionSessions()
	{
		$operationId = !empty($_GET['operation']) ? $_GET['operation'] : 0;
		$operation = ElementOperation::model()->findByPk($operationId);
		if (empty($operation)) {
			throw new Exception('Operation id is invalid.');
		}
		$minDate = !empty($_GET['date']) ? strtotime($_GET['date']) : $operation->getMinDate();

		$firmId = empty($_GET['firmId']) ? $operation->event->episode->firm_id : $_GET['firmId'];
		if ($firmId != 'EMG') {
			$_GET['firm'] = $firmId;
			$firm = Firm::model()->findByPk($firmId);
		} else {
			$firm = new Firm;
			$firm->name = 'Emergency List';
		}

		if ($firm->name != 'Emergency List') {
			$siteList = Session::model()->getSiteListByFirm($firmId);
		} else {
			$siteList = Site::model()->getList();
		}
		if (!empty($_GET['siteId'])) {
			$siteId = $_GET['siteId'];
		} else { // grab the first (possibly only) site off the list
			$siteId = key($siteList);
		}
		if (!empty($siteId)) {
			unset($siteList[$siteId]);
			$site = Site::model()->findByPk($siteId);
			$sessions = $operation->getSessions($firm->name == 'Emergency List', $siteId);
		} else {
			$site = new Site;
			$sessions = array();
		}

		$this->renderPartial('/booking/_calendar',
			array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions, 'firmId'=>$firmId), false, true);
	}

	public function actionTheatres()
	{
		$operationId = !empty($_GET['operation']) ? $_GET['operation'] : 0;
		$operation = ElementOperation::model()->findByPk($operationId);
		if (empty($operation)) {
			throw new Exception('Operation id is invalid.');
		}
		$firmId = empty($_GET['firm']) ? 'EMG' : $_GET['firm'];
		$month = !empty($_GET['month']) ? $_GET['month'] : null;
		if (empty($month)) {
			throw new Exception('Month is required.');
		}
		$day = !empty($_GET['day']) ? $_GET['day'] : null;
		if (empty($day)) {
			throw new Exception('Day is required.');
		}
		if (empty($_REQUEST['reschedule']) || $_REQUEST['reschedule'] == 0) {
			$reschedule = 0;
		} else {
			$reschedule = 1;
		}

		$operation->getMinDate();

		$time = strtotime($month);
		$date = date('Y-m-d', mktime(0,0,0,date('m', $time), $day, date('Y', $time)));
		$theatres = $operation->getTheatres($date, $firmId);

		$this->renderPartial('/booking/_theatre_times',
			array('operation'=>$operation, 'date'=>$date, 'theatres'=>$theatres, 'reschedule' => $reschedule), false, true);
	}

	public function actionList()
	{
		$operationId = !empty($_GET['operation']) ? $_GET['operation'] : 0;
		$operation = ElementOperation::model()->findByPk($operationId);
		if (empty($operation)) {
			throw new Exception('Operation id is invalid.');
		}
		$sessionId = !empty($_GET['session']) ? $_GET['session'] : 0;
		$session = $operation->getSession($sessionId);
		if (empty($session)) {
			throw new Exception('Session id is invalid.');
		}
		$session['id'] = $sessionId;

		$sessionModel = Session::model()->findByPk($session['id']);
		$theatre = $sessionModel->theatre;
		$site = $theatre->site;

		$criteria = new CDbCriteria;
		$criteria->compare('session_id', $sessionId);
		$criteria->order = 'display_order ASC';
		$bookings = Booking::model()->findAll($criteria);

		if ($session['time_available'] >= 0) {
			$minutesStatus = 'available';
		} else {
			$minutesStatus = 'overbooked';
		}

		if (empty($_REQUEST['reschedule']) || $_REQUEST['reschedule'] == 0) {
			$reschedule = 0;
		} else {
			$reschedule = 1;
		}

		$this->renderPartial('/booking/_list',
			array('operation'=>$operation, 'session'=>$session,
				'bookings'=>$bookings, 'minutesStatus'=>$minutesStatus,
				'reschedule'=>$reschedule, 'site'=>$site), false, true);
	}

	public function actionCreate()
	{
		$model=new Booking;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Booking']))
		{
			// This is enforced in the model so no need to if ()
			preg_match('/(^[0-9]{1,2}).*?([0-9]{2})$/',$_POST['Booking']['admission_time'],$m);
			$_POST['Booking']['admission_time'] = $m[1].":".$m[2];

			$model->attributes=$_POST['Booking'];

			$session = Session::model()->findByPk($model->session_id);

			$operation = ElementOperation::model()->findByPk($model->element_operation_id);

			if (!empty($operation->booking)) {
				// This operation already has a booking. There must be two users creating an episode at once
				//	or suchlike. Ignore and return.
				$this->redirect(array('patient/event/'.$operation->event->id));
				return;
			}

			if (!empty($_POST['wardType'])) {
				/* currently not in use, but if we want to allow a checkbox for
				 * booking into an observational ward, it would be handled here
				 */
				$observationWard = Ward::model()->findByAttributes(
					array('site_id' => $session->theatre->site_id,
						'restriction' => Ward::RESTRICTION_OBSERVATION));
				if (!empty($observationWard)) {
					$model->ward_id = $observationWard->id;
				} else {
					$wards = $operation->getWardOptions(
						$session->theatre->site_id, $session->theatre_id);
					$model->ward_id = key($wards);
				}
			} elseif (!empty($operation) && !empty($session)) {
				$wards = $operation->getWardOptions(
					$session->theatre->site_id, $session->theatre_id);
				$model->ward_id = key($wards);
			}

			// figure out the max display_id
			if (!empty($_POST['Booking']['session_id'])) {
				$criteria = new CDbCriteria;
				$criteria->condition='session_id = :id';
				$criteria->params = array(':id' => $_POST['Booking']['session_id']);
				$criteria->order = 'display_order DESC';
				$criteria->limit = 1;
				$booking = Booking::model()->find($criteria);
			} else {
				$booking = false;
			}

			$displayOrder = empty($booking) ? 1 : $booking->display_order + 1;
			$model->display_order = $displayOrder;

			if ($model->save()) {
				if (!$operation->erod) {
					$operation->calculateEROD($session->id);
				}

				OELog::log("Booking made $model->id");

				$audit = new Audit;
				$audit->action = "create";
				$audit->target_type = "booking";
				$audit->patient_id = $operation->event->episode->patient->id;
				$audit->episode_id = $operation->event->episode_id;
				$audit->event_id = $operation->event_id;
				$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
				$audit->data = $model->getAuditAttributes();
				$audit->save();

				// Update episode status to 'listed'
				$operation->event->episode->episode_status_id = 3;
				if (!$operation->event->episode->save()) {
					throw new Exception('Unable to change episode status id for episode '.$operation->event->episode->id);
				}

				$operation->event->deleteIssues();

				if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
					if (strtotime($session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
						if (!is_array(Yii::app()->params['urgent_booking_notify_email'])) {
							$targets = array(Yii::app()->params['urgent_booking_notify_email']);
						} else {
							$targets = Yii::app()->params['urgent_booking_notify_email'];
						}
						foreach ($targets as $email) {
							mail($email, "[OpenEyes] Urgent booking made","A patient booking was made with a TCI date within the next 24 hours.\n\nDisorder: ".$operation->getDisorder()."\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.","From: ".Yii::app()->params['urgent_booking_notify_email_from']."\r\n");
						}
					}
				}

				if ($operation->status == ElementOperation::STATUS_NEEDS_RESCHEDULING) {
					$operation->status = ElementOperation::STATUS_RESCHEDULED;
				} else {
					$operation->status = ElementOperation::STATUS_SCHEDULED;
				}
				if (!empty($_POST['Operation']['comments'])) {
					$operation->comments = $_POST['Operation']['comments'];
				}
				
				// Update the proposed site for the operation to match the booked site (ward)
				// This gives better information on the waiting list when it comes to rescheduling
				$operation->site_id = $model->ward->site_id;
				
				if (!$operation->save()) {
					throw new SystemException('Unable to update operation data: '.print_r($operation->getErrors(),true));
				}

				if (!empty($_POST['Session']['comments'])) {
					$session->comments = $_POST['Session']['comments'];
					if (!$session->save()) {
						throw new SystemException('Unable to save session comments: '.print_r($session->getErrors(),true));
					}
				}

				$patientId = $model->elementOperation->event->episode->patient->id;

				$this->updateEvent($model->elementOperation->event);

				die(json_encode(array()));
			}
		}
	}

	public function actionUpdate()
	{
		if (isset($_POST['booking_id'])) {
			$model = Booking::model()->findByPk($_POST['booking_id']);

			$operation = $model->elementOperation;
			$operationId = $operation->id;

			$reason = CancellationReason::model()->findByPk($_POST['cancellation_reason']);

			if (!$reason) {
				die(json_encode(array('Please enter a cancellation reason')));
			}

			$cancellation = new CancelledBooking();
			$cancellation->element_operation_id = $operationId;
			$cancellation->date = $model->session->date;
			$cancellation->start_time = $model->session->start_time;
			$cancellation->end_time = $model->session->end_time;
			$cancellation->theatre_id = $model->session->theatre_id;
			$cancellation->cancelled_date = date('Y-m-d H:i:s');
			$cancellation->cancelled_reason_id = $reason->id;
			$cancellation->cancellation_comment = strip_tags($_POST['cancellation_comment']);

			if ($cancellation->save()) {
				OELog::log("Booking cancelled: $model->id, cancelled_booking=$cancellation->id");

				if (!empty($_POST['Booking'])) {

					// This is enforced in the model so no need to if ()
					preg_match('/(^[0-9]{1,2}).*?([0-9]{2})$/',$_POST['Booking']['admission_time'],$m);
					$_POST['Booking']['admission_time'] = $m[1].":".$m[2];

					$model->attributes = $_POST['Booking'];

					$new_session = Session::Model()->findByPk($model->session_id);

					$wards = $operation->getWardOptions(
						$new_session->theatre->site_id, $new_session->theatre_id);
					$model->ward_id = key($wards);

					if (!$model->save()) {
						throw new SystemException('Unable to save booking: '.print_r($model->getErrors(),true));
					}

					OELog::log("Booking rescheduled: $model->id, cancelled_booking=$cancellation->id");

					$audit = new Audit;
					$audit->action = "reschedule";
					$audit->target_type = "booking";
					$audit->patient_id = $operation->event->episode->patient_id;
					$audit->episode_id = $operation->event->episode_id;
					$audit->event_id = $operation->event_id;
					$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
					$audit->data = $model->getAuditAttributes();
					$audit->save();

					$operation->event->episode->episode_status_id = 3;
					if (!$operation->event->episode->save()) {
						throw new Exception('Unable to change episode status for episode '.$operation->event->episode->id);
					}

					if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
						if (strtotime($model->session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
							if (!is_array(Yii::app()->params['urgent_booking_notify_email'])) {
								$targets = array(Yii::app()->params['urgent_booking_notify_email']);
							} else {
								$targets = Yii::app()->params['urgent_booking_notify_email'];
							}
							foreach ($targets as $email) {
								mail($email, "[OpenEyes] Urgent reschedule made","A patient booking was rescheduled with a TCI date within the next 24 hours.\n\nDisorder: ".$operation->getDisorder()."\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.","From: ".Yii::app()->params['urgent_booking_notify_email_from']."\r\n");
							}
						}
					}

					// Looking for a matching row in transport_list and remove it so the entry in the transport list isn't grey
					if ($tl = TransportList::model()->find('item_table = ? and item_id = ?',array('booking',$model->id))) {
						$tl->delete();
					}
	
					$operation->site_id = $new_session->theatre->site_id;
					$operation->status = ElementOperation::STATUS_RESCHEDULED;
					
					// Update operation comments
					if (!empty($_POST['Operation']['comments'])) {
						$operation->comments = $_POST['Operation']['comments'];
					}
					
					if (!$operation->save()) {
						throw new SystemException('Unable to update operation status: '.print_r($operation->getErrors(),true));
					}

					if (!empty($_POST['Session']['comments'])) {
						$new_session->comments = $_POST['Session']['comments'];
						if (!$new_session->save()) {
							throw new SystemException('Unable to save session comments: '.print_r($new_session->getErrors(),true));
						}
					}
				} else {
					if (!$operation->event->addIssue('Operation requires scheduling')) {
						throw new SystemException('Unable to save event_issue object for event: '.$operation->event->id);
					}

					if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
						if (strtotime($model->session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
							if (!is_array(Yii::app()->params['urgent_booking_notify_email'])) {
								$targets = array(Yii::app()->params['urgent_booking_notify_email']);
							} else {
								$targets = Yii::app()->params['urgent_booking_notify_email'];
							}
							foreach ($targets as $email) {
								mail($email, "[OpenEyes] Urgent cancellation made","A cancellation was made with a TCI date within the next 24 hours.\n\nDisorder: ".$operation->getDisorder()."\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.","From: ".Yii::app()->params['urgent_booking_notify_email_from']."\r\n");
							}
						}
					}

					if (!$model->delete()) {
						throw new Exception('Unable to delete booking: '.print_r($model->getErrors(),true));
					}

					$audit = new Audit;
					$audit->action = "delete";
					$audit->target_type = "booking";
					$audit->patient_id = $operation->event->episode->patient_id;
					$audit->episode_id = $operation->event->episode_id;
					$audit->event_id = $operation->event_id;
					$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
					$audit->data = $model->id;
					$audit->save();

					$operation->event->episode->episode_status_id = 3;

					if (!$operation->event->episode->save()) {
						throw new Exception('Unable to update episode status for episode '.$operation->event->episode->id);
					}

					$operation->status = ElementOperation::STATUS_NEEDS_RESCHEDULING;
					
					// we've just removed a booking and updated the element_operation status to 'needs rescheduling'
					// any time we do that we need to add a new record to date_letter_sent
					$date_letter_sent = new DateLetterSent();
					$date_letter_sent->element_operation_id = $operation->id;
					$date_letter_sent->save();

					if (!$operation->save()) {
						throw new SystemException('Unable to update operation status: '.print_r($operation->getErrors(),true));
					}
				}

				$patientId = $model->elementOperation->event->episode->patient->id;

				$this->updateEvent($model->elementOperation->event);

				die(json_encode(array()));
			}

			die(json_encode($cancellation->getErrors(),true));
		}
	}

	/**
	 * Update the event object with the datetime and the user id
	 *
	 * @param object $event
	 */
	public function updateEvent($event)
	{
		// Update event with this user and datetime
		// $event->datetime = date("Y-m-d H:i:s");
		// if (!$event->save()) {
		// 	throw new SystemException('Unable to update event datetime: '.print_r($event->getErrors(),true));
		// }
	}

	public function header($editable=false) {
		if (!$operation = ElementOperation::model()->findByPk($_GET['operation'])) {
			throw new SystemException('Operation not found: '.$_GET['operation']);
		}

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);
		$this->editable = $editable;

		$patient = $this->model = $operation->event->episode->patient;

		$ordered_episodes =  $patient->getOrderedEpisodes();
		
		$this->renderPartial('//patient/event_header',array(
			'ordered_episodes'=>$ordered_episodes,
			'eventTypes'=>EventType::model()->getEventTypeModules(),
			'title'=>'Schedule operation',
			'model'=>$patient,
		));
	}

	public function footer($editable=false) {
		if (!$operation = ElementOperation::model()->findByPk($_GET['operation'])) {
			throw new SystemException('Operation not found: '.$_GET['operation']);
		}

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		$patient = $this->model = $operation->event->episode->patient;

		$episodes = $patient->episodes;

		$this->renderPartial('//patient/event_footer',array(
			'episodes'=>$episodes,
			'eventTypes'=>EventType::model()->getEventTypeModules(),
			'editable'=>$editable
		));
	}

	/**
	 * Get all the elements for a the current module's event type
	 *
	 * @param $event_type_id
	 * @return array
	 */
	public function getDefaultElements($action, $event_type_id=false, $event=false) {
		$etc = new BaseEventTypeController(1);
		$etc->event = $event;
		return $etc->getDefaultElements($action, $event_type_id);
	}

	/**
	 * Get the optional elements for the current module's event type
	 * This will be overriden by the module
	 *
	 * @param $event_type_id
	 * @return array
	 */
	public function getOptionalElements($action, $event=false) {
		return array();
	}
}
