<?php

class AppointmentController extends BaseController
{
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
		
		$this->renderPartial('/appointment/_schedule', 
	array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions), false, true);
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
		
		$this->renderPartial('/appointment/_calendar', 
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
		
		$this->renderPartial('/appointment/_theatre_times', 
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
		$appointments = Appointment::model()->findAll($criteria);
		
		if ($session['time_available'] >= 0) {
			$minutesStatus = 'available';
		} else {
			$minutesStatus = 'overbooked';
		}
		
		$this->renderPartial('/appointment/_list', 
			array('operation'=>$operation, 'session'=>$session, 
				'appointments'=>$appointments, 'minutesStatus'=>$minutesStatus), false, true);
	}
	
	public function actionCreate()
	{
		$model=new Appointment;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Appointment']))
		{
			$model->attributes=$_POST['Appointment'];
			
			// figure out the max display_id
			if (!empty($_POST['Appointment']['session_id'])) {
				$criteria = new CDbCriteria;
				$criteria->condition='session_id = :id';
				$criteria->params = array(':id' => $_POST['Appointment']['session_id']);
				$criteria->order = 'display_order DESC';
				$criteria->limit = 1;
				$appointment = Appointment::model()->find($criteria);
			} else {
				$appointment = false;
			}
			
			$displayOrder = empty($appointment) ? 1 : $appointment->display_order + 1;
			$model->display_order = $displayOrder;
			
			if ($model->save()) {
//				$this->redirect(array('view','id'=>$model->id));
			}
		}

//		$this->render('create',array(
//			'model'=>$model,
//			'firm'=>$firmAssociation
//		));
	}
	
	public function actionUpdate()
	{
		$model = new Appointment;
		
		if (isset($_POST['Appointment'])) {
			$model->attributes = $_POST['Appointment'];
			
			if ($model->save()) {
//				$this->redirect(array('view','id'=>$model->id));
			}
		}
	}
}