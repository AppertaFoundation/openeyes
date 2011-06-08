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
		$appointments = Appointment::model()->findAllByAttributes(
			array('session_id'=>$sessionId));
		
		$this->renderPartial('/appointment/_list', 
	array('operation'=>$operation, 'session'=>$session, 'appointments'=>$appointments), false, true);
	}
}