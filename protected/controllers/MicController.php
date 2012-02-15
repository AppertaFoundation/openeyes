<?php
class MicController extends BaseController {

	public $layout='column2';

	protected function beforeAction($action) {
		if (!Yii::app()->user->checkAccess('admin')) {
			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}
		return parent::beforeAction($action);
	}
	
	public function actionIndex() {
		
		$results = array();
		
		// Find
		$start = microtime(true);
		for($i = 1; $i <= 100; $i++) {
			$patients = Patient::model()->findAll(array(
				'limit' => 100
			));
		}
		$results['Find 100 patients 100 times'] = microtime(true) - $start;
		
		// Save
		$patients = Patient::model()->findAll(array(
			'limit' => 200
		));
		$count = count($patients);
		$start = microtime(true);
		foreach($patients as $patient) {
			$patient->save();
		}
		$results["Save $count patients"] = microtime(true) - $start;
		
		$this->render('index', array(
			'results' => $results,
		));
	}
}
