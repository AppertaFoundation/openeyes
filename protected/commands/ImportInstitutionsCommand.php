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

class ImportInstitutionsCommand extends CConsoleCommand {
	
	public function getName() {
		return '';
	}
	
	public function getHelp() {
		return "";
	}

	public function run($args) {
		$fp = fopen("/tmp/ETR.csv","r");

		while($data = fgetcsv($fp)) {
			$data[1] = $this->reformat($data[1]);
			$data[4] = $this->reformat($data[4]);
			$data[5] = $this->reformat($data[5]);
			$data[6] = $this->reformat($data[6]);
			$data[7] = $this->reformat($data[7]);
			$data[8] = $this->reformat($data[8]);

			if (!$institution = Institution::model()->find('code=?',array($data[0]))) {
				$institution = new Institution;
				$institution->code = $data[0];
			}

			$institution->name = $data[1];
			$institution->save();

			if (!$address = Address::model()->find("parent_class='Institution' and parent_id=?",array($institution->id))) {
				$address = new Address;
				$address->parent_class = 'Institution';
				$address->parent_id = $institution->id;
			}

			$address->address1 = $data[4];
			$address->address2 = $data[5];
			if ($data[6]) {
				$address->address2 .= ', '.$data[6];
			}
			$address->city = $data[7];
			$address->county = $data[8];
			$address->postcode = $data[9];
			$address->country_id = 1;
			$address->save();

			echo ".";
		}

		fclose($fp);

		echo "\n";
	}

	public function reformat($data) {
		$data = ucwords(strtolower($data));
		$data = preg_replace('/ Nhs/',' NHS',$data);

		return trim($data);
	}
}
?>
