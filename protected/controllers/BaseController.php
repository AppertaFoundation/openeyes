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

	protected function beforeAction($action)
	{
		$this->storeData();
		
		return parent::beforeAction($action);
	}

	public function checkPatientId()
	{
		$app = Yii::app();

		// @todo - decide where precisely this should be available on UX
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

		// @todo - this will need to be changed to whatever else indicates correct rights
		if (!empty($app->session['firms'])) {
			$this->showForm = true;

			$this->firms = $app->session['firms'];
			$this->selectedFirmId = $app->session['selected_firm_id'];
		}

		// @todo - decide where precisely this should be available on UX
		// @todo - address the possibility of having two browser windows with different patients
		if (isset($app->session['patient_name'])) {
			$this->patientName = $app->session['patient_name'];
		}
	}
}
