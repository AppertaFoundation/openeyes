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

		// Process patients in batches of 100 to avoid excessive queries on PAS
		$patient_ids = array();
		$count = 0;
		foreach($patients as $patient) {
			$patient_ids[sprintf('%07d',$patient['hos_num'])] = $patient['id'];
			$count++;
			if($count % 100 == 0) {
				echo "Block $count\n";
				$hos_nums = array_keys($patient_ids);
				$hos_num_string = "'" . implode("','", $hos_nums) . "'";
				$patient_nos = Yii::app()->db_pas->createCommand()
				->select('CONCAT(num_id_type,number_id) AS hos_num, rm_patient_no')
				->from('SILVER.NUMBER_IDS')
				->where("CONCAT(num_id_type,number_id) IN ($hos_num_string)")
				->queryAll();
				foreach($patient_nos as $patient_no) {
					$assignment = new PasAssignment();
					$assignment->external_id = $patient_no['rm_patient_no'];
					$assignment->external_type = 'PAS_Patient';
					$assignment->internal_id = $patient_ids[$patient_no['hos_num']];
					$assignment->internal_type = 'Patient';
					$assignement->save();
				}
				$patient_ids = array();
			}
		}

		echo "Created $count patient assignments\n";
		echo "Done.\n";
	}

}
