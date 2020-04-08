<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class AllergicDrugEntriesBehavior
 *
 * This class provides common functionality for elements that
 * save drug entries that interfere with the Patient's allergies.
 *
 * @property BaseEventTypeElement $owner
 */

class AllergicDrugEntriesBehavior extends CBehavior
{
	/**
	 * @param $target			Audit entry target
	 * @param string $action	Audit entry action
	 * @throws Exception
	 *
	 * Search for interfering allergies and log an audit entry if found
	 */

	public function auditAllergicDrugEntries($target, $action = "allergy-override")
	{
		$patient = $this->owner->event->getPatient();
		$patient_allergies = $patient->allergies;
		$patient_allergies_from_drugs = [];
		$allergic_drugs = [];

		$drug_allergies_assignments = $this->getDrugAllergiesAssignments();

		foreach ($patient_allergies as $allergy) {
			foreach ($drug_allergies_assignments as $drug_allergy_assignment) {
				if ($allergy->id === $drug_allergy_assignment->allergy->id) {
					$patient_allergies_from_drugs[$allergy->id] = $allergy->name;
					$allergic_drugs[$drug_allergy_assignment->medication->id] = $drug_allergy_assignment->medication->preferred_term;
				}
			}
		}

		if (isset($allergic_drugs) && sizeof($allergic_drugs) !== 0 &&
			isset($patient_allergies_from_drugs) && sizeof($allergic_drugs) != 0) {
			Audit::add(
				$target, $action, 'Allergies: ' .
				implode(' , ', $patient_allergies_from_drugs) . ' Drugs: ' . implode(' , ', $allergic_drugs),
				null,
				array('patient_id' => $patient->id)
			);
		}
	}

	/**
	 * @return stdClass[]	returns an array of objects where each object
	 * 						has an "allergy" property and a "medication" property
	 */

	private function getDrugAllergiesAssignments()
	{
		$allergies = array();
		/* TODO Would be nice to create an interface to force owner classes to implement getEntries */
		foreach ($this->owner->getEntries() as $item) {
			/** @var EventMedicationUse $item */
			$item_allergies = $item->medication->allergies;
			foreach ($item_allergies as $allergy) {
				$obj = new stdClass();
				$obj->allergy = clone $allergy;
				$obj->medication = clone $item->medication;
				$allergies[] = $obj;
			}
		}

		return $allergies;
	}
}