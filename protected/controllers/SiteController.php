<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
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
	 * Omnibox search form
	 */
	public function actionIndex()	{
		$this->layout = 'main';
		$this->render('index');
	}

	/**
	 * Omnibox search handler
	 */
	public function actionSearch() {
		if(isset($_POST['query']) && $query = trim($_POST['query'])) {
				
			// Event ID
			if(preg_match('/^(E|Event)\s*[:;]\s*([0-9]+)$/i',$query,$matches)) {
				$event_id = $matches[2];
				if($event = Event::model()->findByPk($event_id)) {
					$event_class_name = $event->eventType->class_name;
					if($event_class_name == 'OphTrOperation') {
						// TODO: This can go away once we modularise Booking
						$this->redirect(array('/patient/event/'.$event_id));
					} else {
						$this->redirect(array($event_class_name.'/default/view/'.$event_id));
					}
				} else {
					Yii::app()->user->setFlash('warning.search_error', 'Event ID not found');
					$this->redirect('/');
				}
				return;
			}
	
			// NHS number (assume 10 digit number is an NHS number)
			if(preg_match('/^(N|NHS)\s*[:;]\s*([0-9\- ]+)$/i',$query,$matches)
					|| preg_match('/^([0-9]{3}[- ]?[0-9]{3}[- ]?[0-9]{4})$/i',$query,$matches)) {
				$nhs = (isset($matches[2])) ? $matches[2] : $matches[1];
				$nhs = str_replace(array('-',' '),'',$nhs);
				$this->redirect(array('patient/search', 'nhs_num' => $nhs));
				return;
			}
	
			// Hospital number (assume a < 10 digit number is a hosnum)
			if(preg_match('/^(H|Hosnum)\s*[:;]\s*([0-9a-zA-Z\-]+)$/i',$query,$matches)
					|| preg_match(Yii::app()->params['hos_num_regex'],$query,$matches)) {
				$hosnum = (isset($matches[2])) ? $matches[2] : $matches[1];
				$this->redirect(array('patient/search', 'hos_num' => $hosnum));
				return;
			}
			
			// Patient name (assume two strings separated by space and/or comma is a name)
			if(preg_match('/^(P|Patient)\s*[:;]\s*([^\s,]+)(\s*[\s,]+\s*)([^\s,]+)$/i',$query,$matches)
					|| preg_match('/^([^\s,]+)(\s*[\s,]+\s*)([^\s,]+)$/i',$query,$matches)) {
				$delimiter = (isset($matches[4])) ? trim($matches[3]) : trim($matches[2]);
				if($delimiter) {
					$firstname = (isset($matches[4])) ? $matches[4] : $matches[3];
					$surname = (isset($matches[4])) ? $matches[2] : $matches[1];
				} else {
					$firstname = (isset($matches[4])) ? $matches[2] : $matches[1];
					$surname = (isset($matches[4])) ? $matches[4] : $matches[3];
				}
				$this->redirect(array('patient/search', 'first_name' => $firstname, 'last_name' => $surname));
				return;
			}
		}

		Audit::add('search','search-error');

		if (isset($query)) {
			if (strlen($query) == 0) {
				Yii::app()->user->setFlash('warning.search_error', "Please enter either a hospital number or a firstname and lastname.");
			} else {
				Yii::app()->user->setFlash('warning.search_error', '<strong>"'.CHtml::encode($query).'"</strong> is not a valid search.');
			}
		}

		$this->redirect('/');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
		if($error = Yii::app()->errorHandler->error) {
			if(Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
				$error_code = (int) $error['code'];
				if ($error_code == 403) {
					$this->redirect(Yii::app()->baseUrl.'/');
					Yii::app()->exit();
				}
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
			$this->redirect(Yii::app()->baseUrl.'/');
			Yii::app()->end();
		}

		if (Yii::app()->params['required_user_agent'] && !preg_match(Yii::app()->params['required_user_agent'],@$_SERVER['HTTP_USER_AGENT'])) {
			if (!Yii::app()->params['required_user_agent_message']) {
				throw new Exception('You must define the required_user_agent_message parameter.');
			}
			return $this->render('login_wrong_browser');
		}

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm'])) {
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()) {
				// Set the site cookie
				Yii::app()->request->cookies['site_id'] = new CHttpCookie('site_id', $model->siteId);

				$this->redirect(Yii::app()->user->returnUrl);
			}
		} else {
			// Get the site id currently stored in the cookie, or the default site id
			$default_site = Site::model()->getDefaultSite();
			$default_site_id = ($default_site) ? $default_site->id : null;
			$model->siteId = (isset(Yii::app()->request->cookies['site_id']->value)) ? Yii::app()->request->cookies['site_id']->value : $default_site_id;
		}

		$institution = Institution::model()->find('code=?',array('RP6'));

		$criteria = new CDbCriteria;
		$criteria->compare('institution_id',$institution->id);
		$criteria->order = 'short_name asc';

		$sites = Site::model()->findAll($criteria);

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

		$user->audit('logout','logout');

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

			$user->audit('user','change-firm',$user->last_firm_id);

			$session = Yii::app()->session;

			$firms = $session['firms'];
			$firmId = intval($_POST['selected_firm_id']);

			if ($firms[$firmId]) {
				$session['selected_firm_id'] = $firmId;
			}

			$so = Yii::app()->session['theatre_searchoptions'];
			if (isset($so['firm-id'])) unset($so['firm-id']);
			if (isset($so['specialty-id'])) unset($so['specialty-id']);
			if (isset($so['site-id'])) unset($so['site-id']);
			if (isset($so['date-filter'])) unset($so['date-filter']);
			if (isset($so['date-start'])) unset($so['date-start']);
			if (isset($so['date-end'])) unset($so['date-end']);
			Yii::app()->session['theatre_searchoptions'] = $so;

			Yii::app()->session['waitinglist_searchoptions'] = null;

			echo "change-firm-succeeded";
			Yii::app()->end();
		}

		parent::storeData();
	}
}
