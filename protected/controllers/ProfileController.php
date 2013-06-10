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

	public function accessRules() {
		return array(
			array('deny'),
		);
	}

	protected function beforeAction($action) {
		$this->registerCssFile('profile.css', Yii::app()->createUrl("css/profile.css"));
		Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl("js/profile.js"));

		$this->jsVars['items_per_page'] = $this->items_per_page;

		return parent::beforeAction($action);
	}

	public function actionIndex() {
		$this->redirect(array('/profile/info'));
	}

	public function actionInfo() {
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

	public function actionPassword() {
		$errors = array();

		$user = User::model()->findByPk(Yii::app()->user->id);

		if (!empty($_POST)) {
			if (Yii::app()->params['profile_user_can_change_password']) {
				if (empty($_POST['User']['password_old'])) {
					$errors['Current password'] = array('Please enter your current password');
				} else if ($user->password !== md5($user->salt.$_POST['User']['password_old'])) {
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

	public function actionSites() {
		$user = User::model()->findByPk(Yii::app()->user->id);

		$this->render('/profile/sites',array(
			'sites' => $user->siteSelections,
		));
	}
}
