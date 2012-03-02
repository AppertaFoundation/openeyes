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

		/*
		the concept of first_in_episode has gone away. this will possibly need refactoring.

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
			}
		}
		*/
		return true;
	}
}
