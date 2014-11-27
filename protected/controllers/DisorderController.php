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

class DisorderController extends BaseController
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
	 * Lists all disorders for a given search term.
	 */
	public function actionAutocomplete()
	{
		if (Yii::app()->request->isAjaxRequest) {
			$criteria = new CDbCriteria();
			$params = array();
			if (isset($_GET['term']) && $term = $_GET['term']) {
				$criteria->addCondition('LOWER(term) LIKE :term');
				$params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
			}
			$criteria->order = 'term';

			// Limit results
			$criteria->limit = '200';
			if (@$_GET['code']) {
				if (@$_GET['code'] == 'systemic') {
					$criteria->addCondition('specialty_id is null');
				} else {
					$criteria->join = 'join specialty on specialty_id = specialty.id AND specialty.code = :specode';
					$params[':specode'] = $_GET['code'];
				}
			}

			$criteria->params = $params;

			$disorders = Disorder::model()->active()->findAll($criteria);
			$return = array();
			foreach ($disorders as $disorder) {
				$return[] = array(
						'label' => $disorder->term,
						'value' => $disorder->term,
						'id' => $disorder->id,
				);
			}
			echo CJSON::encode($return);
		}
	}

	public function actionDetails()
	{
		if (!isset($_REQUEST['name'])) {
			echo CJavaScript::jsonEncode(false);
		} else {
			$disorder = Disorder::model()->find('fully_specified_name = ? OR term = ?', array($_REQUEST['name'], $_REQUEST['name']));
			if ($disorder) {
				echo $disorder->id;
			} else {
				echo CJavaScript::jsonEncode(false);
			}
		}
	}

	public function actionIsCommonOphthalmic($id)
	{
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		if ($cd = CommonOphthalmicDisorder::model()->find('disorder_id=? and subspecialty_id=?',array($id,$firm->serviceSubspecialtyAssignment->subspecialty_id))) {
			echo "<option value=\"$cd->disorder_id\" data-order=\"{$cd->display_order}\">".$cd->disorder->term."</option>";
		}
	}

}
