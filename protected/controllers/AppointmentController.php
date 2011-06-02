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
		$sessions = $operation->getSessions();
		
		$this->renderPartial('/appointment/_schedule', 
	array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions), false, true);
	}
	
	public function actionSessions()
	{
		$operationId = !empty($_GET['operation']) ? $_GET['operation'] : 0;
		$operation = ElementOperation::model()->findByPk($operationId);
		if (empty($operation)) {
			throw new Exception('Operation id is invalid. - ' . $operationId);
		}
		$minDate = !empty($_GET['date']) ? strtotime($_GET['date']) : $operation->getMinDate();
		$sessions = $operation->getSessions();
		
		$this->renderPartial('/appointment/_calendar', 
	array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions), false, true);
	}
}