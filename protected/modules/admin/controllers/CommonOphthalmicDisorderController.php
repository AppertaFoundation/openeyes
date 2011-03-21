<?php

class CommonOphthalmicDisorderController extends Controller
{
	public $layout='column2';

	protected function beforeAction(CAction $action)
	{
		// Sample code to be used when RBAC is fully implemented.
		if (!Yii::app()->user->checkAccess('admin')) {
			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}

		return parent::beforeAction($action);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new CommonOphthalmicDisorder;

		if(isset($_POST['CommonOphthalmicDisorder']))
		{
			if (isset($_POST['term'])) {
				$model->specialty_id = $_POST['CommonOphthalmicDisorder']['specialty_id'];

				$disorder = Disorder::Model()->find('term = ?', array($_POST['term']));
				if (isset($disorder)) {
					$model->disorder_id = $disorder->id;
				}

				if($model->save())
					$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['CommonOphthalmicDisorder']))
		{
			$model->disorder_id = '';

			if (isset($_POST['term'])) {
				$model->specialty_id = $_POST['CommonOphthalmicDisorder']['specialty_id'];

				// Look up the term's id from the disorder table, if any
				$disorder = Disorder::Model()->find('term = ?', array($_POST['term']));
				if (isset($disorder)) {
					$model->disorder_id = $disorder->id;
				}
				// @todo - display error correctly here and in sytemicDisorder admin controller,
				// diagnosisController

				if($model->save())
					$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
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
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('CommonOphthalmicDisorder');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new CommonOphthalmicDisorder('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CommonOphthalmicDisorder']))
			$model->attributes=$_GET['CommonOphthalmicDisorder'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=CommonOphthalmicDisorder::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='common-ophthalmic-disorder-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
