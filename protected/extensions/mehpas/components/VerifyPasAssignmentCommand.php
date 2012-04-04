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

class VerifyPasAssignmentCommand extends CConsoleCommand {

	public function getName() {
		return 'VerifyPasAssignment';
	}

	public function getHelp() {
		return "Checks the records in the assignment table still match have a matching PAS patient\n";
	}

	public function run($args) {
		$pas_service = new PasService();
		if ($pas_service->available) {
			$this->verifyPatientPasAssignment();
		} else {
			echo "PAS is unavailable or module is disabled";
			return false;
		}
	}

	protected function verifyPatientPasAssignment() {

		// Checks all the patient assignments are still valid in PAS
		$patients = Yii::app()->db->createCommand()
		->select('external_id')
		->from('pas_assignment')
		->where("pas_assignment.internal_type = 'Patient'")
		->queryAll();

		echo "There are ".count($patients)." patient assignments, processing...\n";

		$count = 0;
		foreach($patients as $patient) {
			$count++;
			if($count % 100 == 0) {
				echo ".";
			}
			$rm_patient_no = $patient['external_id'];
			$pas_patient = PAS_Patient::model()->findAll('rm_patient_no = :rm_patient_no', array(
					':rm_patient_no' => $rm_patient_no,
			));

			if(count($pas_patient) == 1) {
				// Found a single match
				Yii::log("Found match in PAS for rm_patient_no $rm_patient_no", 'trace');
			} else if(count($patient_no) > 1) {
				// Found more than one match
				echo "Found more than one match in PAS for rm_patient_no $rm_patient_no\n";
			} else {
				// No match
				echo "Cannot find match in PAS for rm_patient_no $rm_patient_no\n";
			}

		}

		echo "\nDone.\n";
	}

}
