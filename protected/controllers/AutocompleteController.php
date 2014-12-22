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

class AutocompleteController extends BaseController
{
	public function accessRules()
	{
		return array(
			array('allow',
				'roles' => array('OprnViewClinical'),
			),
		);
	}

	/**
	 * Lists values for a given search term.
	 */
	public function actionSearch()
	{
		if (!isset($_GET['model'])) {
			throw new CHttpException(400, 'model required');
		}
		$class_name = $_GET['model'];
		if(!is_subclass_of($class_name, 'CActiveRecord')) {
			throw new CHttpException(400, 'invalid model');
		}
		$model = $class_name::model();
		if(!$model->canAutocomplete()) {
			throw new CHttpException(400, 'model does not support autocomplete');
		}

		if (isset($_GET['field'])) {
			if ($_GET['field'] && preg_match('/^[A-z]+$/', $_GET['field'])) {
				$search_field = strtolower($_GET['field']);
			} else {
				throw new CHttpException(400, 'invalid field name');
			}
		} else {
			$search_field = 'name';
		}

		// Construct criteria
		$criteria = new CDbCriteria();
		$params = array();
		if (isset($_GET['term']) && $term = $_GET['term']) {
			$criteria->addCondition("LOWER($search_field) LIKE :term");
			$params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
		}
		$criteria->order = $search_field;
		$criteria->limit = '50';
		$criteria->params = $params;

		$records = $model->active()->findAll($criteria);
		$return = array();
		$fields = array(
			'label' => $search_field,
			'value' => $search_field,
			'id' => 'id'
		);
		foreach ($records as $record) {
			$return_row = array();
			foreach($fields as $key => $value) {
				$return_row[$key] = $record->$value;
			}
			$return[] = $return_row;
		}
		echo CJSON::encode($return);
	}

}
