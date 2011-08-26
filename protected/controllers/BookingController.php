<?php

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
		$sessions = $operation->getSessions();

		$this->renderPartial('/booking/_schedule',
	array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions), false, true);
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
		$sessions = $operation->getSessions();

		$this->renderPartial('/booking/_reschedule',
	array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions), false, true);
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
		$sessions = $operation->getSessions();

		$this->renderPartial('/booking/_reschedule_later',
	array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions), false, true);
	}

	public function actionCancelOperation()
	{
		if (isset($_POST['cancellation_reason'])) {
			$operationId = $_POST['operation_id'];

			$cancel = new CancelledOperation;
			$cancel->element_operation_id = $operationId;
			$cancel->cancelled_date = date('Y-m-d H:i:s');
			$cancel->cancelled_reason_id = $_POST['cancellation_reason'];
			$cancel->user_id = Yii::app()->user->id;

			$operation = ElementOperation::model()->findByPk($operationId);
			$operation->status = ElementOperation::STATUS_CANCELLED;

			Booking::model()->deleteAll('element_operation_id = :id', array(':id'=>$operationId));

			if ($cancel->save() && $operation->save()) {
				$patientId = $operation->event->episode->patient->id;
				$this->redirect(array('patient/view', 'id'=>$patientId, 'tabId'=>1,
					'eventId'=>$operation->event->id));
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
		$sessions = $operation->getSessions();

		$this->renderPartial('/booking/_calendar',
			array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions), false, true);
	}

	public function actionTheatres()
	{
		$operationId = !empty($_GET['operation']) ? $_GET['operation'] : 0;
		$operation = ElementOperation::model()->findByPk($operationId);
		if (empty($operation)) {
			throw new Exception('Operation id is invalid.');
		}
		$month = !empty($_GET['month']) ? $_GET['month'] : null;
		if (empty($month)) {
			throw new Exception('Month is required.');
		}
		$day = !empty($_GET['day']) ? $_GET['day'] : null;
		if (empty($day)) {
			throw new Exception('Day is required.');
		}
		$time = strtotime($month);
		$date = date('Y-m-d', mktime(0,0,0,date('m', $time), $day, date('Y', $time)));
		$theatres = $operation->getTheatres($date);

		$this->renderPartial('/booking/_theatre_times',
			array('operation'=>$operation, 'date'=>$date, 'theatres'=>$theatres), false, true);
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

		$criteria = new CDbCriteria;
		$criteria->compare('session_id', $sessionId);
		$criteria->order = 'display_order ASC';
		$bookings = Booking::model()->findAll($criteria);

		if ($session['time_available'] >= 0) {
			$minutesStatus = 'available';
		} else {
			$minutesStatus = 'overbooked';
		}

		$reschedule = !empty($_GET['reschedule']);

		$this->renderPartial('/booking/_list',
			array('operation'=>$operation, 'session'=>$session,
				'bookings'=>$bookings, 'minutesStatus'=>$minutesStatus,
				'reschedule'=>$reschedule), false, true);
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
			if (!empty($_POST['wardType'])) {
				$observationWard = Ward::model()->findByAttributes(
					array('site_id' => $session->sequence->theatre->site_id,
						'restriction' => Ward::RESTRICTION_OBSERVATION));
				if (!empty($observationWard)) {
					$model->ward_id = $observationWard->id;
				} else {
					$wards = $operation->getWardOptions($session->sequence->theatre->site_id);
					$model->ward_id = key($wards);
				}
			} elseif (!empty($operation) && !empty($session)) {
				$wards = $operation->getWardOptions($session->sequence->theatre->site_id);
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
				if ($operation->status == ElementOperation::STATUS_NEEDS_RESCHEDULING) {
					$operation->status = ElementOperation::STATUS_RESCHEDULED;
				} else {
					$operation->status = ElementOperation::STATUS_SCHEDULED;
				}
				$operation->save();

				Yii::app()->user->setFlash('success','Booking saved.');
				$patientId = $model->elementOperation->event->episode->patient->id;
				$this->redirect(array('patient/view','id'=>$patientId, 'tabId'=>1,
					'eventId'=>$model->elementOperation->event->id));
			}
		}
	}

	public function actionUpdate()
	{
		if (isset($_POST['booking_id'])) {
			$model = Booking::model()->findByPk($_POST['booking_id']);

			$operationId = $model->elementOperation->id;

			$reason = CancellationReason::model()->findByPk($_POST['cancellation_reason']);

			$cancellation = new CancelledBooking;
			$cancellation->element_operation_id = $operationId;
			$cancellation->date = $model->session->date;
			$cancellation->start_time = $model->session->start_time;
			$cancellation->end_time = $model->session->end_time;
			$cancellation->theatre_id = $model->session->sequence->theatre_id;
			$cancellation->cancelled_date = date('Y-m-d H:i:s');
			$cancellation->user_id = Yii::app()->user->id;
			$cancellation->cancelled_reason_id = $reason->id;

			if ($cancellation->save()) {
				if (!empty($_POST['Booking'])) {
					$model->attributes = $_POST['Booking'];
					$model->save();

					$operation = ElementOperation::model()->findByPk($operationId);
					$operation->status = ElementOperation::STATUS_RESCHEDULED;
					$operation->save();
				} else {
					$model->delete();

					$operation = ElementOperation::model()->findByPk($operationId);
					$operation->status = ElementOperation::STATUS_NEEDS_RESCHEDULING;
					$operation->save();
				}

				Yii::app()->user->setFlash('success','Booking updated.');
				$patientId = $model->elementOperation->event->episode->patient->id;
				$this->redirect(array('patient/view','id'=>$patientId, 'tabId'=>1,
					'eventId'=>$model->elementOperation->event->id));
			}
		}
	}
}
