<?php

/**
 * A base controller class that helps display the firm dropdown and the patient name.
 * It is extended by all non-admin controllers.
 */

class BaseController extends Controller
{
	public $selectedFirmId;
	public $firms;
	public $showForm = false;
	public $patientId;
	public $patientName;
	
	/**
	 * Default all access rule filters to a deny-basis to prevent accidental 
	 * allowing of actions that don't have access rules defined yet
	 * 
	 * @param $filterChain
	 * @return type 
	 */
	public function filterAccessControl($filterChain)
	{
		$rules = $this->accessRules();

		if (Yii::app()->params['ab_testing']) {
			$rules = array(
				array('allow',
					'users'=>array('@','?')
				)
			);
		} else {
			// default deny
			$rules[] = array('deny', 'users'=>array('?'));
		}

		$filter = new CAccessControlFilter;
		$filter->setRules($rules);
		$filter->filter($filterChain);
	}

	protected function beforeAction($action)
	{
		$app = Yii::app();

		if (Yii::app()->params['ab_testing']) {
			if (Yii::app()->user->isGuest) {
				$identity=new UserIdentity('admin', 'admin');
				$identity->authenticate();
				Yii::app()->user->login($identity,0);
				$this->selectedFirmId = 1;
				$app->session['patient_id'] = 1;
				$app->session['patient_name'] = 'John Smith';
			}
		}

		if (isset($app->session['firms']) && count($app->session['firms'])) {
			$this->showForm = true;

			$this->firms = $app->session['firms'];
			$this->selectedFirmId = $app->session['selected_firm_id'];
		}

		if (isset($app->session['patient_name'])) {
			$this->patientName = $app->session['patient_name'];
		}

		return parent::beforeAction($action);
	}

	public function checkPatientId()
	{
		$app = Yii::app();

		if (Yii::app()->params['ab_testing']) {
			if (Yii::app()->user->isGuest) {
				$identity=new UserIdentity('admin', 'admin');
				$identity->authenticate();
				Yii::app()->user->login($identity,0);
				$this->selectedFirmId = 1;
				$app->session['patient_id'] = 1;
				$app->session['patient_name'] = 'John Smith';
			}
			$app->session['patient_id'] = 1;
			$app->session['patient_name'] = 'John Smith';
		}

		if (isset($app->session['patient_id'])) {
			$this->patientId = $app->session['patient_id'];
			$this->patientName = $app->session['patient_name'];
		} else {
			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}
	}

	public function storeData()
	{
		$app = Yii::app();

		if (!empty($app->session['firms'])) {
			$this->showForm = true;

			$this->firms = $app->session['firms'];
			$this->selectedFirmId = $app->session['selected_firm_id'];
		}

		if (isset($app->session['patient_name'])) {
			$this->patientName = $app->session['patient_name'];
		}
	}

	public function logActivity($message)
	{
		$addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

		Yii::log($message . ' from ' . $addr, "user", "userActivity");
	}
}
