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

class AdminController extends BaseController
{
	public $layout = 'admin';
	public $items_per_page = 30;
	public $jsVars = array();

	public function filters()
	{
		return array('accessControl');
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@'),
			),
			// non-logged in can't view anything
			array('deny',
				'users'=>array('?')
			),
		);
	}

	protected function beforeAction($action) {
		Yii::app()->clientScript->registerCssFile("/css/admin.css");
		Yii::app()->clientScript->registerScriptFile("/js/admin.js");

		$this->jsVars['items_per_page'] = $this->items_per_page;

		return parent::beforeAction($action);
	}

	public function actionIndex() {
		$this->render('/admin/index');
	}

	public function actionUsers($id=false) {
		if ((integer)$id) {
			$page = $id;
		} else {
			$page = 1;
		}

		$this->render('/admin/users',array(
			'users' => $this->getItems(array(
				'model' => 'User',
				'page' => $page,
			)),
		));
	}

	public function actionAddUser() {
		$user = new User;

		if (!empty($_POST)) {
			$user->attributes = $_POST['User'];

			if (!$user->validate()) {
				$errors = $user->getErrors();
			} else {
				if (!$user->save()) {
					throw new Exception("Unable to save user: ".print_r($user->getErrors(),true));
				}
				$this->redirect('/admin/users/'.ceil($user->id/$this->items_per_page));
			}
		}

		$user->password = '';

		$this->render('/admin/adduser',array(
			'user' => $user,
			'errors' => @$errors,
		));
	}

	public function actionEditUser($id) {
		if (!$user = User::model()->findByPk($id)) {
			throw new Exception("User not found: $id");
		}

		if (!empty($_POST)) {
			if (!$_POST['User']['password']) {
				unset($_POST['User']['password']);
			}

			$user->attributes = $_POST['User'];

			if (!$user->validate()) {
				$errors = $user->getErrors();
			} else {
				if (!$user->save()) {
					throw new Exception("Unable to save user: ".print_r($user->getErrors(),true));
				}
				$this->redirect('/admin/users/'.ceil($user->id/$this->items_per_page));
			}
		}

		$user->password = '';

		$this->render('/admin/edituser',array(
			'user' => $user,
			'errors' => @$errors,
		));
	}

	public function getItems($params) {
		$pages = ceil(count($params['model']::model()->findAll()) / $this->items_per_page);

		if ($params['page'] <1) {
			$page = 1;
		} else if ($params['page'] > $pages) {
			$page = $pages;
		} else {
			$page = $params['page'];
		}

		$criteria = new CDbCriteria;
		$criteria->order = 'id asc';
		$criteria->offset = ($page-1) * $this->items_per_page;
		$criteria->limit = $this->items_per_page;

		return array(
			'items' => $params['model']::model()->findAll($criteria),
			'page' => $page,
			'pages' => ceil(count($params['model']::model()->findAll()) / $this->items_per_page),
		);
	}
}
