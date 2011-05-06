<?php

class DiagnosisController extends BaseController
{
	public $layout = '//layouts/patientMode/column2';

	public $firm;

	protected function beforeAction($action)
	{
		// Sample code to be used when RBAC is fully implemented.
//		if (!Yii::app()->user->checkAccess('admin')) {
//			throw new CHttpException(403, 'You are not authorised to perform this action.');
//		}

		$this->storeData();

		return parent::beforeAction($action);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Diagnosis;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Diagnosis']))
		{
			$model->attributes=$_POST['Diagnosis'];
			$model->patient_id = $this->patientId;
			$model->user_id = Yii::app()->user->id;
			$model->created_on = date("Y-m-d H:i:s");

			if (!empty($_POST['term'])) {
				$disorder = Disorder::Model()->find('term = ?', array($_POST['term']));

				if (isset($disorder)) {
					$model->disorder_id = $disorder->id;
				} else {
					$model->addError('disorder_id', 'There is no disorder of that name.');
				}
			} elseif (
				isset($_POST['Diagnosis']['common_ophthalmic_disorder_id']) &&
				$_POST['Diagnosis']['common_ophthalmic_disorder_id'] > 1
			) {
				$model->disorder_id = $_POST['Diagnosis']['common_ophthalmic_disorder_id'];
			} elseif (
				isset($_POST['Diagnosis']['common_systemic_disorder_id']) &&
				$_POST['Diagnosis']['common_systemic_disorder_id'] > 1
			) {
				$model->disorder_id = $_POST['Diagnosis']['common_systemic_disorder_id'];
			}

			if(!$model->hasErrors() && $model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$diagnosis = $this->loadModel($id);

		if (Yii::app()->user->id != $diagnosis->user_id) {
			throw new CHttpException(400, 'You are not the owner of that diagnosis.');
		}

		$diagnosis->delete();

		$this->redirect(array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$diagnoses = Diagnosis::Model()->findAll('patient_id = ?', array($this->patientId));

		$this->render('index',array('diagnoses' => $diagnoses));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Diagnosis::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='diagnosis-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function storeData()
	{
		parent::storeData();

		$this->checkPatientId();

		// Get the firm currently associated with this user
		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		if (!isset($this->firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}
	}
}
