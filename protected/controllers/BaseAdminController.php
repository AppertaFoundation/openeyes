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

class BaseAdminController extends BaseController
{
	public $layout = '//layouts/admin';
	public $items_per_page = 30;
	public $form_errors;

	public function accessRules()
	{
		return array(array('allow', 'roles' => array('admin')));
	}

	protected function beforeAction($action)
	{
		Yii::app()->assetManager->registerCssFile('css/admin.css', null, 10);
		Yii::app()->assetManager->registerScriptFile('js/admin.js', null, 10);
		$this->jsVars['items_per_page'] = $this->items_per_page;

		if (!empty($_POST['GenericAdminModel'])) {
			$this->handleGenericAdmin();
		}

		return parent::beforeAction($action);
	}

	/**
	 *	@description Initialise and handle admin pagination
	 *	@author bizmate
	 *	@param class $model
	 *	@param string $criteria
	 *	@return CPagination
	 */
	protected function initPagination($model, $criteria = null)
	{
		$criteria = is_null($criteria) ? new CDbCriteria() : $criteria;
		$itemsCount = $model->count($criteria);
		$pagination = new CPagination($itemsCount);
		$pagination->pageSize = $this->items_per_page;
		$pagination->applyLimit($criteria);
		return $pagination;
	}

	public function handleGenericAdmin()
	{
		$model = $_POST['GenericAdminModel'];

		$ids = array();

		$to_save = array();

		if (!empty($_POST['id'])) {
			foreach ($_POST['id'] as $i => $id) {
				if ($id) {
					$item = $model::model()->findByPk($id);
				} else {
					$item = new $model;
				}

				$item->name = $_POST['name'][$i];
				$item->display_order = $i+1;
				//handle models with active flag
				$attributes = $item->getAttributes();
				if (array_key_exists('active',$attributes)) {
					$item->active = (isset($_POST['active'][$i]) || $item->isNewRecord)? 1 : 0;
				}

				if (!empty($_POST['_extra_fields'])) {
					foreach ($_POST['_extra_fields'] as $field) {
						$item->$field = $_POST[$field][$i];
					}
				}

				if ($item->hasAttribute('default')) {
					if (isset($_POST['default']) && $_POST['default'] != 'NONE' && $_POST['default'] == $i) {
						$item->default = 1;
					} else {
						$item->default = 0;
					}
				}

				if (!$item->validate()) {
					$errors = $item->getErrors();
					foreach ($errors as $error) {
						$this->form_errors[$i] = $error[0];
					}
				} else {
					$to_save[] = $item;
				}

				$ids[] = $item->id;
			}
		}

		if (empty($this->form_errors)) {
			foreach ($to_save as $item) {
				if (!$item->save()) {
					throw new Exception("Unable to save admin list item: ".print_r($item->getErrors(),true));
				}
			}

			$criteria = new CDbCriteria;

			!empty($ids) && $criteria->addNotInCondition('id',$ids);

			$model::model()->deleteAll($criteria);

			Yii::app()->user->setFlash('success', "List updated.");

			$this->redirect('/' . $this->route);
		}
	}
}
