<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class UserController extends BaseController
{
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('autoComplete'),
				'roles' => array('OprnViewClinical'),
			),
		);
	}

	public function actionAutoComplete($term)
	{
		$crit = new CDbCriteria;
		$crit->compare('first_name', $term, true);
		$crit->compare('last_name', $term, true, 'OR');
		$crit->compare('active', true);
		$crit->order = 'last_name,first_name';

		$results = array();
		foreach (User::model()->findAll($crit) as $user) {
			$results[] = array(
				'id' => $user->id,
				'value' => $user->getReversedFullName(),
			);
		}

		print json_encode($results);
	}
}
