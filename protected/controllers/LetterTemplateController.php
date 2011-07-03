<?php

// @todo - (largely unrelated to letter templates!) How to we prevent contact records having records in multiple other
//	contact tables, e.g. both gp and consultant?

class LetterTemplateController extends BaseController
{
	public $layout = '//layouts/patientMode/column2';
	public $firm;

	protected function beforeAction($action)
	{
		// Only internal consultants are allowed to use this page
                if (!User::isConsultant()) {
                        throw new CHttpException(403, 'You are not permitted to administrate letter templates.');
                }

		$this->storeData();

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

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
		$model=new LetterTemplate;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['LetterTemplate']))
		{
			$model->attributes=$_POST['LetterTemplate'];
			$model->specialty_id = $this->firm->serviceSpecialtyAssignment->specialty_id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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

		if ($model->specialty_id != $this->firm->serviceSpecialtyAssignment->specialty_id) {
			throw new CHttpException(403, 'You are not permitted to alter this letter template.');
		}

		if(isset($_POST['LetterTemplate']))
		{
			$model->attributes=$_POST['LetterTemplate'];
			$model->specialty_id = $this->firm->serviceSpecialtyAssignment->specialty_id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('LetterTemplate', array(
			'criteria'=>array(
				'condition'=>'specialty_id=' . $this->firm->serviceSpecialtyAssignment->specialty_id
			)
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=LetterTemplate::model()->findByPk((int)$id);

		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');

                if ($model->specialty_id != $this->firm->serviceSpecialtyAssignment->specialty_id) {
                        throw new CHttpException(403, 'You are not permitted to view this letter template.');
                }

		return $model;
	}
}
