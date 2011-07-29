<?php

class TheatreController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
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

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$theatres = array();
		if (!empty($_POST)) {
			$date = '2011-07-27';
			$service = new BookingService;
			$data = $service->findTheatresAndSessions($date, $date);
			
			foreach ($data as $values) {
				$sessionTime = explode(':', $values['session_duration']);
				$sessionDuration = ($sessionTime[0] * 60) + $sessionTime[1];

				$theatres[$values['name']][$values['date']][] = array(
					'startTime' => $values['start_time'],
					'endTime' => $values['end_time'],
					'sessionId' => $values['session_id'],
					'sessionDuration' => $sessionDuration,
					'timeAvailable' => $sessionDuration - $values['bookings_duration'],
					'displayOrder' => $values['display_order']
				);
			}
		}
		$this->render('index', array('theatres'=>$theatres));
	}
	
	/**
	 * Generates a firm list based on a service id provided via POST
	 * echoes form option tags for display
	 */
	public function actionFilterFirms()
	{
		echo CHtml::tag('option', array('value'=>''), 
			CHtml::encode('All firms'), true);
		if (!empty($_POST['service_id'])) {
			$firms = Yii::app()->db->createCommand()
				->select('f.id, f.name')
				->from('firm f')
				->join('service_specialty_assignment ssa', 'f.service_specialty_assignment_id = ssa.id')
				->join('service s', 'ssa.service_id = s.id')
				->where('ssa.service_id=:id', 
					array(':id'=>$_POST['service_id']))
				->queryAll();
			
			foreach ($firms as $values) {
				echo CHtml::tag('option', array('value'=>$values['id']), 
					CHtml::encode($values['name']), true);
			}
		}
	}
	
	/**
	 * Generates a theatre list based on a site id provided via POST
	 * echoes form option tags for display
	 */
	public function actionFilterTheatres()
	{
		echo CHtml::tag('option', array('value'=>''), 
			CHtml::encode('All theatres'), true);
		if (!empty($_POST['site_id'])) {
			$theatres = Yii::app()->db->createCommand()
				->select('t.id, t.name')
				->from('theatre t')
				->where('t.site_id = :id', 
					array(':id'=>$_POST['site_id']))
				->queryAll();
			
			foreach ($theatres as $values) {
				echo CHtml::tag('option', array('value'=>$values['id']), 
					CHtml::encode($values['name']), true);
			}
		}
	}
}