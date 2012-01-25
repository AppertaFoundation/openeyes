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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

class BookingController extends BaseController
{
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

	public function actionCancelOperation()
	{
		if (isset($_POST['cancellation_reason'])) {
			$operationId = $_POST['operation_id'];

			$cancel = new CancelledOperation;
			$cancel->element_operation_id = $operationId;
			$cancel->cancelled_date = date('Y-m-d H:i:s');
			$cancel->cancelled_reason_id = $_POST['cancellation_reason'];
			$cancel->cancellation_comment = strip_tags($_POST['cancellation_comment']);

			$operation = ElementOperation::model()->findByPk($operationId);
			$operation->status = ElementOperation::STATUS_CANCELLED;

			//Booking::model()->deleteAll('element_operation_id = :id', array(':id'=>$operationId));

			if ($cancel->save() && $operation->save()) {

				OELog::log("element_operation $operation->id cancelled");

				$patientId = $operation->event->episode->patient->id;

				$this->updateEvent($operation->event);

				if ($model = $operation->booking) {
					// If there was a booking for this operation, create a CancelledBooking row
					$cancellation = new CancelledBooking;
					$cancellation->element_operation_id = $operationId;
					$cancellation->date = $model->session->date;
					$cancellation->start_time = $model->session->start_time;
					$cancellation->end_time = $model->session->end_time;
					$cancellation->theatre_id = $model->session->sequence->theatre_id;
					$cancellation->cancelled_date = date('Y-m-d H:i:s');
					$cancellation->cancelled_reason_id = $_POST['cancellation_reason'];
					$cancellation->cancellation_comment = strip_tags($_POST['cancellation_comment']);

					if (!$cancellation->save()) {
						throw new SystemException('Unable to save cancelled_booking: '.print_r($cancellation->getErrors(),true));
					}

					OELog::log("Booking cancelled: $model->id (booking_cancellation=$cancellation->id");

					if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
						if (strtotime($model->session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
							mail(Yii::app()->params['urgent_booking_notify_email'],"[OpenEyes] Urgent cancellation made","A cancellation was made with a TCI date within the next 24 hours.\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.","From: OpenEyes <help@openeyes.org.uk>\r\n");
						}
					}

					if (!$model->delete()) {
						throw new SystemException('Unable to save cancelled_booking: '.print_r($model->getErrors(),true));
					}
				}

				$this->redirect(array('patient/episodes','id'=>$patientId,
					'event'=>$operation->event->id));
			}
		} else {
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
		}

		$this->renderPartial('/booking/_cancel_operation',
			array('operation'=>$operation, 'date'=>$minDate), false, true);
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
		$sequence = Sequence::model()->findByPk($sessionModel->sequence_id);
		$theatre = Theatre::model()->findByPk($sequence->theatre_id);
		$site = Site::model()->findByPk($theatre->site_id);

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
			$model->attributes=$_POST['Booking'];

			$session = Session::model()->findByPk($model->session_id);

			$operation = ElementOperation::model()->findByPk($model->element_operation_id);

			if (!empty($operation->booking)) {
				// This operation already has a booking. There must be two users creating an episode at once
				//	or suchlike. Ignore and return.
				$this->redirect(array('patient/episodes','id'=>$operation->event->episode->patient->id,
					'event'=>$operation->event->id));

				return;
			}

			if (!empty($_POST['wardType'])) {
				/* currently not in use, but if we want to allow a checkbox for
				 * booking into an observational ward, it would be handled here
				 */
				$observationWard = Ward::model()->findByAttributes(
					array('site_id' => $session->sequence->theatre->site_id,
						'restriction' => Ward::RESTRICTION_OBSERVATION));
				if (!empty($observationWard)) {
					$model->ward_id = $observationWard->id;
				} else {
					$wards = $operation->getWardOptions(
						$session->sequence->theatre->site_id, $session->sequence->theatre->id);
					$model->ward_id = key($wards);
				}
			} elseif (!empty($operation) && !empty($session)) {
				$wards = $operation->getWardOptions(
					$session->sequence->theatre->site_id, $session->sequence->theatre->id);
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
				OELog::log("Booking made $model->id");

				if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
					if (strtotime($session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
						mail(Yii::app()->params['urgent_booking_notify_email'],"[OpenEyes] Urgent booking made","A patient booking was made with a TCI date within the next 24 hours.\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.","From: OpenEyes <help@openeyes.org.uk>\r\n");
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

				$this->redirect(array('patient/episodes','id'=>$patientId,
					'event'=>$model->elementOperation->event->id));
			}
		}
	}

	public function actionUpdate()
	{
		if (isset($_POST['booking_id'])) {
			$model = Booking::model()->findByPk($_POST['booking_id']);

			$operationId = $model->elementOperation->id;

			$reason = CancellationReason::model()->findByPk($_POST['cancellation_reason']);

			$cancellation = new CancelledBooking();
			$cancellation->element_operation_id = $operationId;
			$cancellation->date = $model->session->date;
			$cancellation->start_time = $model->session->start_time;
			$cancellation->end_time = $model->session->end_time;
			$cancellation->theatre_id = $model->session->sequence->theatre_id;
			$cancellation->cancelled_date = date('Y-m-d H:i:s');
			$cancellation->cancelled_reason_id = $reason->id;
			$cancellation->cancellation_comment = strip_tags($_POST['cancellation_comment']);

			if ($cancellation->save()) {
				OELog::log("Booking cancelled: $model->id, cancelled_booking=$cancellation->id");

				if (!empty($_POST['Booking'])) {
					$model->attributes = $_POST['Booking'];
					if (!$model->save()) {
						throw new SystemException('Unable to save booking: '.print_r($model->getErrors(),true));
					}

					OELog::log("Booking rescheduled: $model->id, cancelled_booking=$cancellation->id");

					if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
						if (strtotime($model->session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
							mail(Yii::app()->params['urgent_booking_notify_email'],"[OpenEyes] Urgent reschedule made","A patient booking was rescheduled with a TCI date within the next 24 hours.\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.","From: OpenEyes <help@openeyes.org.uk>\r\n");
						}
					}

					// Looking for a matching row in transport_list and remove it so the entry in the transport list isn't grey
					if ($tl = TransportList::model()->find('item_table = ? and item_id = ?',array('booking',$model->id))) {
						$tl->delete();
					}

					$operation = ElementOperation::model()->findByPk($operationId);
					$operation->status = ElementOperation::STATUS_RESCHEDULED;
					if (!$operation->save()) {
						throw new SystemException('Unable to update operation status: '.print_r($operation->getErrors(),true));
					}

					if (!empty($_POST['Session']['comments'])) {
						$model->session->comments = $_POST['Session']['comments'];
						if (!$model->session->save()) {
							throw new SystemException('Unable to save session comments: '.print_r($model->session->getErrors(),true));
						}
					}
				} else {
					if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
						if (strtotime($model->session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
							mail(Yii::app()->params['urgent_booking_notify_email'],"[OpenEyes] Urgent cancellation made","A cancellation was made with a TCI date within the next 24 hours.\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.","From: OpenEyes <help@openeyes.org.uk>\r\n");
						}
					}

					$operation = ElementOperation::model()->findByPk($operationId);
					// we need to update the element_operation with a more accurate site_id based on what /was/ in the operation scheduled before it gets nuked.	this gives better information on the waiting list when it comes to rescheduling
					$operation->site_id = $model->ward->site->id;

					$model->delete();

					$operation->status = ElementOperation::STATUS_NEEDS_RESCHEDULING;

					// we've just removed a booking and updated the element_operation status to 'needs rescheduling'
					// any time we do that we need to add a new record to date_letter_sent
					$date_letter_sent = new DateLetterSent();
					$date_letter_sent->element_operation_id = $operation->id;
					$date_letter_sent->save();


					if (!$operation->save()) {
						throw new SystemException('Unable to update operation status: '.print_r($operation->getErrors(),true));
					} else {
					}
				}

				$patientId = $model->elementOperation->event->episode->patient->id;

				$this->updateEvent($model->elementOperation->event);

				$this->redirect(array('patient/episodes','id'=>$patientId,
					'event'=>$model->elementOperation->event->id));
			}
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
		$event->datetime = date("Y-m-d H:i:s");
		if (!$event->save()) {
			throw new SystemException('Unable to update event datetime: '.print_r($event->getErrors(),true));
		}
	}
}
