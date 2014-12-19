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

	public function accessRules()
	{
		return array(array('allow', 'roles' => array('admin')));
	}

	protected function beforeAction($action)
	{
		Yii::app()->assetManager->registerCssFile('css/admin.css', null, 10);
		Yii::app()->assetManager->registerScriptFile('js/admin.js', null, 10);
		$this->jsVars['items_per_page'] = $this->items_per_page;

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

	/**
	 * @param string $title
	 * @param string $model
	 * @param array $options
	 * @param integer $key - if provided will only generate a single row for a null instance of the $model (for ajax additions)
	 *
	 */
	protected function genericAdmin($title, $model, array $options = array(), $key = null)
	{
		$options += array(
			'label_field' => $model::SELECTION_LABEL_FIELD,
			'extra_fields' => array(),
			'filter_fields' => array(),
			'filters_ready' => true,
		);

		$columns = $model::model()->metadata->columns;

		foreach ($options['extra_fields'] as &$extra_field) {
			switch ($extra_field['type']) {
				case 'lookup':
					$extra_field['allow_null'] = $columns[$extra_field['field']]->allowNull;
					break;
			}
		}

		foreach ($options['filter_fields'] as &$filter_field) {
			$filter_field['value'] = @$_GET[$filter_field['field']];

			if (!$filter_field['value'] && !$columns[$filter_field['field']]->allowNull) {
				$options['filters_ready'] = false;
			}
		}

		$items = array();
		$errors = array();

		if ($key !== null) {
			$items = array($key => new $model);
			$options['get_row'] = true;
			$this->renderPartial('//admin/generic_admin', array(
					'title' => $title,
					'model' => $model,
					'items' => $items,
					'errors' => $errors,
					'options' => $options,
				), false, true);
		}
		else {
			if ($options['filters_ready']) {
				if (Yii::app()->request->isPostRequest) {
					$j = 0;
					foreach ((array) @$_POST['id'] as $i => $id) {
						if ($id) {
							$item = $model::model()->findByPk($id);
						} else {
							$item = new $model;
						}

						$item->{$options['label_field']} = $_POST[$options['label_field']][$i];
						$item->display_order = $_POST['display_order'][$i];
						//handle models with active flag
						$attributes = $item->getAttributes();
						if (array_key_exists('active',$attributes)) {
							$item->active = (isset($_POST['active'][$i]) || $item->isNewRecord)? 1 : 0;
						}

						foreach ($options['extra_fields'] as $field) {
							$name = $field['field'];
							$item->$name = @$_POST[$name][$i];
						}

						if ($item->hasAttribute('default')) {
							if (isset($_POST['default']) && $_POST['default'] != 'NONE' && $_POST['default'] == $j) {
								$item->default = 1;
							} else {
								$item->default = 0;
							}
						}

						foreach ($options['filter_fields'] as $field) {
							$item->{$field['field']} = $field['value'];
						}

						if (!$item->validate()) {
							$errors = $item->getErrors();
							foreach ($errors as $error) {
								$errors[$i] = $error[0];
							}
						}

						$items[] = $item;
						$j++;
					}

					if (empty($errors)) {
						$tx = Yii::app()->db->beginTransaction();

						$ids = array();

						foreach ($items as $item) {
							if (!$item->save()) {
								throw new Exception("Unable to save admin list item: ".print_r($item->getErrors(),true));
							}
							$ids[] = $item->id;
						}

						$criteria = new CDbCriteria;

						!empty($ids) && $criteria->addNotInCondition('id',$ids);
						$this->addFilterCriteria($criteria, $options['filter_fields']);

						$model::model()->deleteAll($criteria);
						$tx->commit();

						Yii::app()->user->setFlash('success', "List updated.");

						$this->redirect(Yii::app()->request->url);
					}
				} else {
					$crit = new CDbCriteria(array('order' => 'display_order'));
					$this->addFilterCriteria($crit, $options['filter_fields']);
					$items = $model::model()->findAll($crit);
				}
			}

			$this->render('//admin/generic_admin', array(
				'title' => $title,
				'model' => $model,
				'items' => $items,
				'errors' => $errors,
				'options' => $options,
			));
		}
	}

	private function addFilterCriteria(CDbCriteria $crit, array $filter_fields)
	{
		foreach ($filter_fields as $filter_field) {
			$crit->compare($filter_field['field'], $filter_field['value']);
		}
	}
}
