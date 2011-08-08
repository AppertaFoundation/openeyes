<?php

class SiteController extends BaseController
{
	/**
	 * Updates the selected firm if need be.
	 * Calls the BaseController beforeAction method to set up displaying the firm form if need be.
	 */
	protected function beforeAction($action)
	{
		$this->storeData();

		return parent::beforeAction($action);
	}

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	
	public function filters()
	{
		return array('accessControl');
	}
	
	public function accessRules()
	{
		return array(
			// non-logged in can't view index or logout
			array('deny', 
				'actions'=>array('index', 'logout'),
				'users'=>array('?')
			),
			// everyone can view errors
			array('allow',
				'actions'=>array('error'),
				'users'=>array('*')
			),
			// non-logged in can view login
			array('allow',
				'actions'=>array('login'),
				'users'=>array('?')
			),
			// logged in can view logout
			array('allow',
				'actions'=>array('logout'),
				'users'=>array('@')
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->render('index', array('patientSearchError' => isset($_REQUEST['patientSearchError'])));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()) {
				// Set the site cookie
				Yii::app()->request->cookies['site_id'] = new CHttpCookie('site_id', $model->siteId);

				$this->redirect(Yii::app()->user->returnUrl);
			}
		} else {
			// Get the site id currently stored in the cookie, if any
			$model->siteId = (isset(Yii::app()->request->cookies['site_id']->value)) ? Yii::app()->request->cookies['site_id']->value : '';
		}

		$sites = Site::model()->findAll();

		// display the login form
		$this->render('login',
			array(
				'model'=>$model,
				'sites' => CHtml::listData($sites, 'id', 'name')
			)
		);
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/*
	 * Store session data based on what action we're performing
	 */
	public function storeData()
	{
		$action = $this->getAction();
		if ($action->getId() == 'index' && !empty($_POST['selected_firm_id'])) {
			$session = Yii::app()->session;

			$firms = $session['firms'];
			$firmId = intval($_POST['selected_firm_id']);

			if ($firms[$firmId]) {
				$session['selected_firm_id'] = $firmId;
			}
		}

		parent::storeData();
	}
}
