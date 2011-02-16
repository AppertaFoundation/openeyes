<?php

class BaseController extends Controller
{
	public $selectedFirmId;
	public $firms;
	public $showForm = false;

	protected function beforeAction(CAction $action)
	{
		$app = Yii::app();

		// @todo - this will need to be changed to whatever else indicates correct rights
		if (isset($app->session['firms']) && count($app->session['firms'])) {
			$this->showForm = true;

			$this->firms = $app->session['firms'];
			$this->selectedFirmId = $app->session['selected_firm_id'];
		}

		return parent::beforeAction($action);
	}
}