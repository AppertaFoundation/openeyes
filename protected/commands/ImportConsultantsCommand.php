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

class ImportConsultantsCommand extends CConsoleCommand {
	
	public function getName() {
		return '';
	}
	
	public function getHelp() {
		return "";
	}

	public function run($args) {
		$fp = fopen("/tmp/econcur.csv","r");

		$people = array();

		while($data = fgetcsv($fp)) {
			// Ignore consultants not in the same specialty category as Bill (opthalmologists)
			if ($data[5] != 130) continue;

			if ($data[7] != 'RP6') {
				if (!$consultant = Consultant::model()->find('practitioner_code=?',array($data[1]))) {
					$consultant = new Consultant;
					$consultant->gmc_number = $data[0];
					$consultant->practitioner_code = $data[1];
					$consultant->gender = $data[4];
					$consultant->save();
				}

				if (!$contact = $consultant->contact) {
					$contact = new Contact;
					$contact->parent_class = 'Consultant';
					$contact->parent_id = $consultant->id;
				}

				$contact->first_name = $data[3];
				$contact->last_name = $data[2];
				$contact->save();

				if (!$institution = Institution::model()->find('code=?',array($data[7]))) {
					echo "\nInstitution missing: {$data[7]}\n";
					continue;
				}

				if (!InstitutionConsultantAssignment::model()->find('institution_id=? and consultant_id=?',array($institution->id,$consultant->id))) {
					$ica = new InstitutionConsultantAssignment;
					$ica->institution_id = $institution->id;
					$ica->consultant_id = $consultant->id;
					$ica->save();
				}

				echo ".";
			}
		}

		echo "\n";
	}
}
?>
