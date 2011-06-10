<?php

Yii::import('application.controllers.*');

class PatientController extends BaseController
{
	public $layout = '//layouts/column2';

	protected function beforeAction($action)
	{
		// Sample code to be used when RBAC is fully implemented.
//		if (!Yii::app()->user->checkAccess('admin')) {
//			throw new CHttpException(403, 'You are not authorised to perform this action.');
//		}

		return parent::beforeAction($action);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$patient = $this->loadModel($id);

		$this->layout = '//layouts/patientMode/column2';

		$app = Yii::app();
		$app->session['patient_id'] = $patient->id;
		$app->session['patient_name'] = $patient->title . ' ' . $patient->first_name . ' ' . $patient->last_name;

		$this->render('view', array(
			'model' => $patient
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('Patient');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	/**
	 * Display a form to use for searching models
	 */
	public function actionSearch()
	{
		if (isset($_POST['Patient'])) {
			$this->forward('results');
		} else {
			$model = new Patient;
			$this->render('search', array(
				'model' => $model,
			));
		}
	}

	/**
	 * Display results based on a search submission
	 */
	public function actionResults()
	{
		if (empty($_POST['Patient'])) {
			unset($_POST);
			$this->forward('search');
		}
		if (!isset($_GET['Patient_page'])) {
			$page = 1;
		} else {
			$page = $_GET['Patient_page'];
		}

		$model = new Patient;
		$service = new PatientService;
		$criteria = $service->search($_POST['Patient']);

		$pages = new CPagination($model->count($criteria));
		$pages->applyLimit($criteria);

		$dataProvider = new CActiveDataProvider('Patient', array(
			'criteria' => $criteria,
			'pagination' => $pages));

		$this->render('results', array(
			'dataProvider' => $dataProvider
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new Patient('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Patient']))
			$model->attributes = $_GET['Patient'];

		$this->render('admin', array(
			'model' => $model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = Patient::model()->findByPk((int) $id);
		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'patient-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

}
