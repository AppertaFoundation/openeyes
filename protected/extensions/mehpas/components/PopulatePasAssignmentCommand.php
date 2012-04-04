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
 * @todo This command is currently disabled until the referral code is fixed
 */

class PopulatePasAssignmentCommand extends CConsoleCommand {

	public function getName() {
		return 'PopulatePasAssignment';
	}

	public function getHelp() {
		return "Adds an assignment record for every patient currently in OpenEyes\n";
	}

	public function run($args) {
		$pas_service = new PasService();
		if ($pas_service->available) {
			$this->populatePatientPasAssignment();
		} else {
			echo "PAS is unavailable or module is disabled";
			return false;
		}
	}

	protected function populatePatientPasAssignment() {

		// Find all patients that don't have an assignment
		$patients = Yii::app()->db->createCommand()
		->select('patient.id, patient.hos_num')
		->from('patient')
		->leftJoin('pas_assignment', "pas_assignment.internal_id = patient.id AND pas_assignment.internal_type = 'Patient'")
		->where('pas_assignment.id IS NULL')
		->queryAll();

		echo "There are ".count($patients)." without an assignment, processing...\n";

		$updated = 0;
		foreach($patients as $patient) {

			// Find rm_patient_no
			$hos_num = sprintf('%07d',$patient['hos_num']);
			$number_id = substr($hos_num, -6);
			$num_id_type = substr($hos_num, 0, 1);
			$patient_no = PAS_PatientNumber::model()->findAll('num_id_type = :num_id_type AND number_id = :number_id', array(
					':num_id_type' => $num_id_type,
					':number_id' => $number_id,
			));
				
			if(count($patient_no) == 1) {
				// Found a single match
				Yii::log("Found match in PAS for hos_num $hos_num, creating assignment and updating patient", 'trace');
				$assignment = new PasAssignment();
				$assignment->external_id = $patient_no[0]->RM_PATIENT_NO;
				$assignment->external_type = 'PAS_Patient';
				$assignment->internal_id = $patient['id'];
				$assignment->internal_type = 'Patient';
				$assignment->save();
				$updated++;
			} else if(count($patient_no) > 1) {
				// Found more than one match
				echo "Found more than one match in PAS for hos_num $hos_num, cannot create assignment\n";
			} else {
				// No match
				echo "Cannot find match in PAS for hos_num $hos_num, cannot create assignment\n";
			}

		}

		echo "Created $updated patient assignments\n";
		echo "Done.\n";
	}

}
