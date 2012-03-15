<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
			// non-logged in can view debug info
			array('allow',
				'actions'=>array('debuginfo'),
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
	public function actionError() {
		if($error = Yii::app()->errorHandler->error) {
			die("<pre>".print_r($error,true));
			if(Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
				$error_code = (int) $error['code'];
				if($error_code != 404) {
					//error_log("URI: ".@$_SERVER['REQUEST_URI']);
					//error_log("PHP Fatal error:  Uncaught exception '".@$error['type']."' with message '".@$error['message']."' in ".@$error['file'].":".@$error['line']."\nStack trace:\n".@$error['trace']);
				}
				if(($view = $this->getViewFile('/error/error'.$error_code)) !== false) {
					$this->render('/error/error'.$error_code, $error);
				} else {
					$this->render('/error/error', $error);
				}
			}
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (Yii::app()->session['user']) {
			header('Location: /');
			Yii::app()->end();
		}

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
				'sites' => CHtml::listData($sites, 'id', 'short_name')
			)
		);
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		$user = Yii::app()->session['user'];

		OELog::log("User $user->username logged out");

		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionDebuginfo() {
		$this->renderPartial('/site/debuginfo',array());
	}

	/*
	 * Store session data based on what action we're performing
	 */
	public function storeData()
	{
		$action = $this->getAction();
		if ($action->getId() == 'index' && !empty($_POST['selected_firm_id'])) {
			$user = Yii::app()->session['user'];
			$user = User::Model()->findByPk(Yii::app()->session['user']->id);
			$user->last_firm_id = intval($_POST['selected_firm_id']);
			$user->save(false);

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
