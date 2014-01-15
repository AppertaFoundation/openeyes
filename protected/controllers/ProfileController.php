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

class ProfileController extends BaseController
{
	public $layout = 'profile';
	public $items_per_page = 30;

	public function accessRules()
	{
		return array(
			array('allow','users'=>array('@')),
		);
	}

	protected function beforeAction($action)
	{
		if (!Yii::app()->params['profile_user_can_edit']) {
			$this->redirect('/');
		}

		Yii::app()->assetManager->registerCssFile('css/profile.css');
		Yii::app()->assetManager->registerScriptFile('js/profile.js');

		$this->jsVars['items_per_page'] = $this->items_per_page;

		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		$this->redirect(array('/profile/info'));
	}

	public function actionInfo()
	{
		if (!Yii::app()->params['profile_user_can_edit']) {
			$this->redirect(array('/profile/password'));
		}

		$errors = array();

		$user = User::model()->findByPk(Yii::app()->user->id);

		if (!empty($_POST)) {
			if (Yii::app()->params['profile_user_can_edit']) {
				foreach (array('title','first_name','last_name','email','qualifications') as $field) {
					if (isset($_POST['User'][$field])) {
						$user->{$field} = $_POST['User'][$field];
					}
				}
				if (!$user->save()) {
					$errors = $user->getErrors();
				} else {
					Yii::app()->user->setFlash('success', "Your profile has been updated.");
				}
			}
		}

		$this->render('/profile/info',array(
			'user' => $user,
			'errors' => $errors,
		));
	}

	public function actionPassword()
	{
		if (!Yii::app()->params['profile_user_can_change_password']) {
			$this->redirect(array('/profile/sites'));
		}

		$errors = array();

		$user = User::model()->findByPk(Yii::app()->user->id);

		if (!empty($_POST)) {
			if (Yii::app()->params['profile_user_can_change_password']) {
				if (empty($_POST['User']['password_old'])) {
					$errors['Current password'] = array('Please enter your current password');
				} elseif ($user->password !== md5($user->salt.$_POST['User']['password_old'])) {
					$errors['Current password'] = array('Password is incorrect');
				}

				if (empty($_POST['User']['password_new'])) {
					$errors['New password'] = array('Please enter your new password');
				}

				if (empty($_POST['User']['password_confirm'])) {
					$errors['Confirm password'] = array('Please confirm your new password');
				}

				if ($_POST['User']['password_new'] != $_POST['User']['password_confirm']) {
					$errors['Confirm password'] = array("Passwords don't match");
				}

				if (empty($errors)) {
					$user->password = $user->password_repeat = $_POST['User']['password_new'];
					if (!$user->save()) {
						$errors = $user->getErrors();
					} else {
						Yii::app()->user->setFlash('success', "Your password has been changed.");
					}
				}
			}

			unset($_POST['User']['password_old']);
			unset($_POST['User']['password_new']);
			unset($_POST['User']['password_confirm']);
		}

		$this->render('/profile/password',array(
			'user' => $user,
			'errors' => $errors,
		));
	}

	public function actionSites()
	{
		$user = User::model()->findByPk(Yii::app()->user->id);

		if (!empty($_POST['sites'])) {
			foreach ($_POST['sites'] as $site_id) {
				if ($us = UserSite::model()->find('user_id=? and site_id=?',array($user->id,$site_id))) {
					if (!$us->delete()) {
						throw new Exception("Unable to delete UserSite: ".print_r($us->getErrors(),true));
					}
				}
			}
		}

		$this->render('/profile/sites',array(
			'user' => $user,
		));
	}

	public function actionAddSite()
	{
		if (@$_POST['site_id'] == 'all') {
			if (!$institution = Institution::model()->find('remote_id=?',array(Yii::app()->params['institution_code']))) {
				throw new Exception("Can't find institution: ".Yii::app()->params['institution_code']);
			}
			$sites = Site::model()->findAll('institution_id=?',array($institution->id));
		} else {
			$sites = Site::model()->findAllByPk(@$_POST['site_id']);
		}

		foreach ($sites as $site) {
			if (!$us = UserSite::model()->find('site_id=? and user_id=?',array($site->id,Yii::app()->user->id))) {
				$us = new UserSite;
				$us->site_id = $site->id;
				$us->user_id = Yii::app()->user->id;
				if (!$us->save()) {
					throw new Exception("Unable to save UserSite: ".print_r($us->getErrors(),true));
				}
			}
		}

		echo "1";
	}

	public function actionFirms()
	{
		$user = User::model()->findByPk(Yii::app()->user->id);

		if (!empty($_POST['firms'])) {
			foreach ($_POST['firms'] as $firm_id) {
				if ($uf = UserFirm::model()->find('user_id=? and firm_id=?',array($user->id,$firm_id))) {
					if (!$uf->delete()) {
						throw new Exception("Unable to delete UserFirm: ".print_r($uf->getErrors(),true));
					}
				}
			}

			if (!UserFirm::model()->find('user_id=?',array(Yii::app()->user->id))) {
				$user = User::model()->findByPk(Yii::app()->user->id);
				if ($user->has_selected_firms) {
					$user->has_selected_firms = 0;
					if (!$user->save()) {
						throw new Exception("Unable to save user: ".print_r($user->getErrors(),true));
					}
				}
			}
		}

		$this->render('/profile/firms',array(
			'user' => $user,
		));
	}

	public function actionAddFirm()
	{
		if (@$_POST['firm_id'] == 'all') {
			$firms = Firm::model()->findAll();
		} else {
			$firms = Firm::model()->findAllByPk(@$_POST['firm_id']);
		}

		foreach ($firms as $firm) {
			if (!$us = UserFirm::model()->find('firm_id=? and user_id=?',array($firm->id,Yii::app()->user->id))) {
				$us = new UserFirm;
				$us->firm_id = $firm->id;
				$us->user_id = Yii::app()->user->id;
				if (!$us->save()) {
					throw new Exception("Unable to save UserFirm: ".print_r($us->getErrors(),true));
				}

				$user = User::model()->findByPk(Yii::app()->user->id);

				$user->has_selected_firms = 1;
				if (!$user->save()) {
					throw new Exception("Unable to save user: ".print_r($user->getErrors(),true));
				}
			}
		}

		echo "1";
	}
}
