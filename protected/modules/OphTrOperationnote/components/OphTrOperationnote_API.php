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

class OphTrOperationnote_API extends BaseAPI
{
	/**
	 * Return the list of procedures as a string for use in correspondence for the given patient and episode.
	 * if the $snomed_terms is true, return the snomed_term, otherwise the standard text term.
	 *
	 * @param Patient $patient
	 * @param Episode $episode
	 * @param boolean $snomed_terms
	 * @return string
	 */
	public function getLetterProcedures($patient)
	{
		$return = '';

		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($plist = $this->getElementForLatestEventInEpisode($episode, 'Element_OphTrOperationnote_ProcedureList')) {
				foreach ($plist->procedures as $i => $procedure) {
					if ($i) $return .= ', ';
					$return .= $plist->eye->adjective.' '.$procedure->term;
				}
			}
		}

		return $return;
	}

	public function getLetterProceduresBookingEventID($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($plist = $this->getElementForLatestEventInEpisode($episode, 'Element_OphTrOperationnote_ProcedureList')) {
				return $plist->booking_event_id;
			}
		}
	}

	public function getLastEye($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($plist = $this->getElementForLatestEventInEpisode($episode, 'Element_OphTrOperationnote_ProcedureList')) {
				return $plist->eye_id;
			}
		}
	}

	public function getLetterProceduresSNOMED($patient)
	{
		$return = '';

		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($plist = $this->getElementForLatestEventInEpisode($episode, 'Element_OphTrOperationnote_ProcedureList')) {
				foreach ($plist->procedures as $i => $procedure) {
					if ($i) $return .= ', ';
					$return .= $plist->eye->adjective.' '.$procedure->snomed_term;
				}
			}
		}

		return $return;
	}

	public function getOpnoteWithCataractElementInCurrentEpisode($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			$event_type = EventType::model()->find('class_name=?',array('OphTrOperationnote'));

			$criteria = new CDbCriteria;
			$criteria->compare('episode_id',$episode->id);
			$criteria->compare('event_type_id',$event_type->id);

			return Element_OphTrOperationnote_Cataract::model()
				->with('event')
				->find($criteria);
		}
	}
}
