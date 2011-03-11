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

	protected function beforeAction(CAction $action)
	{
		$app = Yii::app();

		// @todo - this will need to be changed to whatever else indicates correct rights
		if (isset($app->session['firms']) && count($app->session['firms'])) {
			$this->showForm = true;

			$this->firms = $app->session['firms'];
			$this->selectedFirmId = $app->session['selected_firm_id'];
		}

		// @todo - decide where precisely this should be available on UX
		// @todo - address the possibility of having two browser windows with different patients
		if (isset($app->session['patient_name'])) {
			$this->patientName = $app->session['patient_name'];
		}

		return parent::beforeAction($action);
	}

	protected function checkPatientId()
	{
		$app = Yii::app();

		// @todo - decide where precisely this should be available on UX
		if (isset($app->session['patient_id'])) {
			$this->patientId = $app->session['patient_id'];
			$this->patientName = $app->session['patient_name'];
		} else {
// @todo - TAKE THIS OUT! It's only here so diagnosis works until merge with clinical.
			$this->patientId = 1;
			$this->patientName = 'Test patient, id 1';

//			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}
	}
}