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
		if($medication_id == 'adherence')
		{
			$this->renderPartial(
				'adherence_form',
				array(
					"patient" => $this->fetchModel('Patient', $patient_id),
				),
				false, true
			);
		}
		else {
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
	}

	/**
	 * Searches across MedicationDrug and Drug models for the given term. If the term only matches
	 * on an alias, the alias will be included in the returned label for that entry.
	 *
	 * Distinguishes between the data types to ensure relationship defined correctly.
	 */
	public function actionFindDrug()
	{
		$return = array();

		if (isset($_GET['term']) && $term = strtolower($_GET['term'])) {
			$criteria = new CDbCriteria();
			$criteria->compare('LOWER(name)', $term, true, 'OR');
			$criteria->compare('LOWER(aliases)', $term, true, 'OR');

			foreach (MedicationDrug::model()->findAll($criteria) as $md) {
				$label = $md->name;
				if (strpos(strtolower($md->name), $term) === false) {
					$label .= " (" . $md->aliases . ")";
				}
				$return[] = array(
						'name' => $md->name,
						'label' => $label,
						'value' => $md->id,
						'type' => 'md',
				);
			}

			foreach (Drug::model()->active()->findAll($criteria) as $drug) {
				$label = $drug->tallmanlabel;
				if (strpos(strtolower($drug->name), $term) === false) {
					$label .= " (" . $drug->aliases . ")";
				}
				$return[] = array(
					'name' => $drug->tallmanlabel,
					'label' => $label,
					'value' => $drug->id,
					'type' => 'd'
				);
			}
		}

		echo json_encode($return);
	}

	public function actionDrugDefaults($drug_id)
	{
		echo json_encode($this->fetchModel('Drug', $drug_id)->getDefaults());
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
		if(@$_POST['MedicationAdherence']){
			$patient = $this->fetchModel('Patient', @$_POST['patient_id']);

			$medication_adherence = MedicationAdherence::model()->find('patient_id=:patient_id', array(':patient_id'=>$patient->id ));
			if(!$medication_adherence) {
				$medication_adherence= new MedicationAdherence();
				$medication_adherence->patient_id=$patient->id;
			}
			$medication_adherence->medication_adherence_level_id = $_POST['MedicationAdherence']['level'];
			$medication_adherence->comments = $_POST['MedicationAdherence']['comments'];

			if ($medication_adherence->save()) {
				$this->renderPartial('lists', array("patient" => $patient));
			} else {
				header('HTTP/1.1 422');
				echo json_encode($medication_adherence->errors);
			}
		}
		else
		{
			$patient = $this->fetchModel('Patient', @$_POST['patient_id']);
			$medication = $this->fetchModel('Medication', @$_POST['medication_id'], true);

			$medication->patient_id = $patient->id;

			if (!@$_POST['dose']) $_POST['dose'] = null;
			if (!@$_POST['end_date']) $_POST['end_date'] = null;
			$medication->attributes = $_POST;

			if ($medication->save()) {
				$this->renderPartial('lists', array("patient" => $patient));
			} else {
				header('HTTP/1.1 422');
				echo json_encode($medication->errors);
			}
		}
	}

	public function actionStop()
	{
		$patient = $this->fetchModel('Patient', @$_POST['patient_id']);
		$medication = $this->fetchModel('Medication', @$_POST['medication_id']);

		if ($patient->id != $medication->patient_id) throw new Exception("Patient ID mismatch");

		$medication->end_date = @$_POST['end_date'];
		$medication->stop_reason_id = @$_POST['stop_reason_id'] ?: null;
		$medication->save();

		$this->renderPartial('lists', array("patient" => $patient));
	}

	public function actionDelete()
	{
		$patient = $this->fetchModel('Patient', @$_POST['patient_id']);
		$medication = $this->fetchModel('Medication', @$_POST['medication_id']);

		if ($patient->id != $medication->patient_id) throw new Exception("Patient ID mismatch");

		$medication->delete();

		$this->renderPartial('lists', array("patient" => $patient));
	}
}
