<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class OphInDnaextraction_API extends BaseAPI
{
	public function getEventsByPatient($patient)
	{
		$events = array();
		$episodes = $patient->episodes;
		foreach ($episodes as $episode) {
			foreach ($this->getEventsInEpisode($patient, $episode) as $key => $event)
				$events[] = $event;
		}
		return $events;
	}

	public function getVolume($event_id)
	{
		if (!$element = Element_OphInDnaextraction_DnaExtraction::model()->find('event_id = ?', array($event_id))) {
			throw new CHttpException(403, 'Invalid event id.');
		}
		return intVal($element->volume);
	}
}
