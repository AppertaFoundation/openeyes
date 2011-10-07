<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class GenerateSiteElementTypeDataCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'Generate Site Element Type Data Command';
	}
	public function getHelp()
	{
		return 'site_element_type needs to contain all entries which are possible. this command generates those based on other tables';
	}

	public function run($args)
	{
		$this->addNew($args);
		// $this->removeOld($args);
	}

	public function addNew($args)
	{
		$specialties = Specialty::Model()->findAll();
		$possibleElementTypes = PossibleElementType::Model()->with('eventType')->findAll();

		foreach ($specialties as $specialty) {
			echo $specialty->name . "\n";
			foreach ($possibleElementTypes as $pet) {
				// if there's not an existing record, where first_in_episode is false, make one
				if (!$existingSiteElementType = SiteElementType::Model()->findAllByAttributes(array('possible_element_type_id' => $pet->id, 'specialty_id' => $specialty->id, 'first_in_episode' => false))) {
					echo "\tCreating non-first-in-episode site_element_type entry for: possible_element_type id: " . $pet->id . " specialty: " . $specialty->name . " (event type is: " . $pet->eventType->name . ")\n";
					$newSiteElementType = new SiteElementType;
					$newSiteElementType->possible_element_type_id = $pet->id;
					$newSiteElementType->specialty_id = $specialty->id;
					$newSiteElementType->view_number = 1;
					$newSiteElementType->required = 0;
					$newSiteElementType->first_in_episode = 0;
					$newSiteElementType->save();
				}

				// if it's possible for the first in episode to be different:
				// echo "FIEP: " . $pet->eventType->name ."=".$pet->eventType->first_in_episode_possible."\n";
				if ($pet->eventType->first_in_episode_possible == 1) {
					// if there's not an existing record, where first_in_episode is true, make one
					if (!$existingSiteElementType = SiteElementType::Model()->findAllByAttributes(array('possible_element_type_id' => $pet->id, 'specialty_id' => $specialty->id, 'first_in_episode' => true))) {
					echo "\tCreating first-in-episode site_element_type entry for: possible_element_type id: " . $pet->id . " specialty: " . $specialty->name . " (event type is: " . $pet->eventType->name . ")\n";
						$newSiteElementType = new SiteElementType;
						$newSiteElementType->possible_element_type_id = $pet->id;
						$newSiteElementType->specialty_id = $specialty->id;
						$newSiteElementType->view_number = 1;
						$newSiteElementType->required = 0;
						$newSiteElementType->first_in_episode = 1;
						$newSiteElementType->save();
					}
				}
			}
		}
		return true;
	}
}
