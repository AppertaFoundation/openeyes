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

class MedicationController extends BaseController
{
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('OprnEditMedication')),
		);
	}

	/**
	 * @param int $patient_id
	 * @param int $medication_id
	 */
	public function actionForm($patient_id, $medication_id = null)
	{
		$this->renderPartial(
			'form',
			array(
				"patient" => $this->fetchModel('Patient', $patient_id),
				"medication" => $this->fetchModel('Medication', $medication_id, true),
				"firm" => Firm::model()->findByPk($this->selectedFirmId),
			),
			false, true
		);
	}

	public function actionFindDrug()
	{
		$return = array();

		if (isset($_GET['term']) && $term = $_GET['term']) {
			$criteria = new CDbCriteria();
			$criteria->compare('name', $term, true, 'OR');
			$criteria->compare('aliases', $term, true, 'OR');

			foreach (Drug::model()->active()->findAll($criteria) as $drug) {
				$return[] = array(
					'label' => $drug->tallmanlabel,
					'value' => $drug->tallman,
					'id' => $drug->id,
				);
			}
		}

		echo CJSON::encode($return);
	}

	public function actionDrugDefaults($drug_id)
	{
		$drug = $this->fetchModel('Drug', $drug_id);
		echo json_encode(array('route_id' => $drug->default_route_id, 'frequency_id' => $drug->default_frequency_id));
	}

	public function actionDrugRouteOptions($route_id)
	{
		$this->renderPartial(
			'route_option',
			array(
				'medication' => new Medication,
				'route' => $this->fetchModel('DrugRoute', $route_id)
			)
		);
	}

	public function actionSave()
	{
		$patient = $this->fetchModel('Patient', @$_POST['patient_id']);
		$medication = $this->fetchModel('Medication', @$_POST['medication_id'], true);

		$medication->patient_id = $patient->id;
		$medication->attributes = $_POST;

		if ($medication->save()) {
			$this->renderPartial('list', array("patient" => $patient, "current" => true));
			$this->renderPartial('list', array("patient" => $patient, "current" => false));
		} else {
			header('HTTP/1.1 422');
			echo json_encode($medication->errors);
		}
	}
}
