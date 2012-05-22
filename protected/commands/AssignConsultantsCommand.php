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

class AssignConsultantsCommand extends CConsoleCommand {
	
	public function getName() {
		return '';
	}
	
	public function getHelp() {
		return "";
	}

	public function run($args) {
		$sites = array();

		foreach (Site::model()->findAll() as $site) {
			$sites[] = $site->id;
		}

		echo "\nWARNING: This command takes contacts that have no site/institution assignment and assigns them *AT RANDOM* to Moorfields sites.\n\n";
		echo "Obviously this should never be run on the live database.\n\n";

		echo "Continue? [y/N] ";

		$s = fgets(STDIN);

		if (strtolower($s[0]) != 'y') exit;

		foreach (Contact::model()->findAll() as $contact) {
			if ($contact->parent_class == 'Consultant') {
				if (!InstitutionConsultantAssignment::model()->find('consultant_id=?',array($contact->parent_id)) &&
						!SiteConsultantAssignment::model()->find('consultant_id=?',array($contact->parent_id))) {

					$ica = new SiteConsultantAssignment;
					$ica->consultant_id = $contact->parent_id;
					$ica->site_id = $sites[rand(0,count($sites)-1)];
					$ica->save();

					echo ".";
					fflush(STDOUT);
				}
			}
		}

		echo "\n";
	}
}
?>
