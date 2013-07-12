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

	public function accessRules()
	{
		return array(
			// Allow unauthenticated users to view certain pages
			array('allow',
				'actions'=>array('error', 'login', 'debuginfo'),
				'users'=>array('?')
			),
		);
	}

	/**
	 * Omnibox search form
	 */
	public function actionIndex()
	{
		$this->layout = 'main';
		$this->render('index');
	}

	/**
	 * Omnibox search handler
	 */
	public function actionSearch()
	{
		if (isset($_POST['query']) && $query = trim($_POST['query'])) {

			// Event ID
			if (preg_match('/^(E|Event)\s*[:;]\s*([0-9]+)$/i',$query,$matches)) {
				$event_id = $matches[2];
				if ($event = Event::model()->findByPk($event_id)) {
					$event_class_name = $event->eventType->class_name;
					$this->redirect(array($event_class_name.'/default/view/'.$event_id));
				} else {
					Yii::app()->user->setFlash('warning.search_error', 'Event ID not found');
					$this->redirect(array('/'));
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
				if ($delimiter) {
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

		$this->redirect(array('/'));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
				$error_code = (int) $error['code'];
				/*
				if ($error_code == 403) {
					$this->redirect(Yii::app()->baseUrl.'/');
					Yii::app()->exit();
				}
				*/
				if (($view = $this->getViewFile('/error/error'.$error_code)) !== false) {
					$this->render('/error/error'.$error_code, $error);
				} else {
					$this->render('/error/error', $error);
				}
			}
		}
	}

	/**
	 * Display form to change site/firm
	 * @throws CHttpException
	 */
	public function actionChangeSiteAndFirm()
	{
		if (empty($_GET['returnUrl'])) {
			throw new CHttpException(500, 'Return URL must be specified');
		}
		if (@$_GET['patient_id']) {
			$patient = Patient::model()->findByPk(@$_GET['patient_id']);
		}
		$this->renderPartial('/site/change_site_and_firm', array('returnUrl' => $_GET['returnUrl'], 'patient'=>@$patient), false, true);
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (!Yii::app()->user->isGuest) {
			$this->redirect(array('/'));
			Yii::app()->end();
		}

		if (Yii::app()->params['required_user_agent'] && !preg_match(Yii::app()->params['required_user_agent'],@$_SERVER['HTTP_USER_AGENT'])) {
			if (!Yii::app()->params['required_user_agent_message']) {
				throw new Exception('You must define the required_user_agent_message parameter.');
			}
			return $this->render('login_wrong_browser');
		}

		$model = new LoginForm;

		// collect user input data
		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if ($model->validate() && $model->login()) {

				// Flag site for confirmation
				Yii::app()->session['confirm_site_and_firm'] = true;

				$this->redirect(Yii::app()->user->returnUrl);
			}
		}

		// FIXME this needs more thought
		if (isset(Yii::app()->params['institution_code'])) {
			$institution = Institution::model()->find('source_id=? and remote_id=?',array(1,Yii::app()->params['institution_code']));
		} else {
			$institution = Institution::model()->find('source_id=? and remote_id=?',array(1,'RP6'));
		}

		$criteria = new CDbCriteria;
		$criteria->compare('institution_id',$institution->id);
		$criteria->order = 'short_name asc';

		$sites = Site::model()->findAll($criteria);

		// display the login form
		$this->render('login',
			array(
				'model'=>$model,
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

	public function actionDebuginfo()
	{
		$this->renderPartial('/site/debuginfo',array());
	}

}
