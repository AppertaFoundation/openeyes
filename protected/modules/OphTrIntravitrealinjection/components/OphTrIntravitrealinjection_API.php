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

class OphTrIntravitrealinjection_API extends BaseAPI
{
	private $previous_treatments = array();
	private $legacy_api;

	/**
	 * cache and return a legacy injection api instance
	 *
	 * @return mixed
	 */
	protected function getLegacyAPI()
	{
		if (!$this->legacy_api) {
			$this->legacy_api = Yii::app()->moduleAPI->get('OphLeIntravitrealinjection');
		}
		return $this->legacy_api;
	}

	/**
	 * caching method for previous injections store
	 *
	 * @param Patient $patient
	 * @param Episode $episode
	 *
	 * @return Element_OphTrIntravitrealinjection_Treatment[]
	 */
	protected function previousInjectionsForPatientEpisode($patient, $episode)
	{
		if (!isset($this->previous_treatments[$patient->id])) {
			$this->previous_treatments[$patient->id] = array();
		}

		if (!isset($this->previous_treatments[$patient->id][$episode->id])) {
			$events = $this->getEventsInEpisode($patient, $episode);
			$previous = array();
			foreach ($events as $event) {
				if ($treat = Element_OphTrIntravitrealinjection_Treatment::model()->find('event_id = :event_id', array(':event_id' => $event->id))) {
					$previous[] = $treat;
				}
			}
			$this->previous_treatments[$patient->id][$episode->id] = $previous;
		}
		return $this->previous_treatments[$patient->id][$episode->id];
	}


	/**
	 * return only previous injections given a starting event id
	 */
	public function previousInjectionsByEvent($event_id, $side, $drug)
	{
		$event = Event::model()->find('id = :id', array(':id' => $event_id));
		$episode = $event->episode;
		$patient = $event->episode->patient;
		$injections = $this-> previousInjections($patient, $episode, $side, $drug);

		//remove this event and events in the future
		$previousInjections = array();
		foreach($injections as $injection){
			if($event_id > $injection['event_id']){
				$previousInjections[]=$injection;
			}
		}

		return $previousInjections;

	}

	/**
	 * return the set of treatment elements from previous injection events in descending order
	 *
	 * @param Patient $patient
	 * @param Episode $episode
	 * @param string $side
	 * @param Drug $drug
	 * @throws Exception
	 * @return array {$side . '_drug_id' => integer, $side . '_number' => integer, 'date' => datetime}[] - array of treatment elements for the eye and optional drug
	 */
	public function previousInjections($patient, $episode, $side, $drug = null)
	{
		$res = array();

		$previous = $this->previousInjectionsForPatientEpisode($patient, $episode);

		switch ($side) {
			case 'left':
				$eye_ids = array(SplitEventTypeElement::LEFT, SplitEventTypeElement::BOTH);
				break;
			case 'right':
				$eye_ids = array(SplitEventTypeElement::RIGHT, SplitEventTypeElement::BOTH);
				break;
			default:
				throw new Exception('invalid side value provided: ' . $side);
				break;
		}

		foreach ($previous as $prev) {
			if (in_array($prev->eye_id,$eye_ids)) {
				if ($drug == null || $prev->{$side . '_drug_id'} == $drug->id) {
					$res[] = array(
							$side . '_drug_id' => $prev->{$side . '_drug_id'},
							$side . '_drug' => $prev->{$side . '_drug'}->name,
							$side . '_number' => $prev->{$side . '_number'},
							'date' => $prev->created_date,
							'event_id' => $prev->event_id,
					);
				}
			}
		}

		// NOTE: we assume that all legacy injections would be from before any injections in
		// this module. Should this prove not to be the case, we would need to sort the result
		// data structure by date
		if ($legacy_api = $this->getLegacyAPI()) {
			foreach ($legacy_api->previousInjections($patient, $episode, $side, $drug) as $legacy) {
				$res[] = $legacy;
			}
		}

		return $res;
	}

	/**
	 * get the most recent treatment element that has data for the given eye side.
	 *
	 * @param $patient
	 * @param $episode
	 * @param $side
	 * @return Element_OphTrIntravitrealinjection_Treatment
	 */
	protected function getPreviousTreatmentForSide($patient, $episode, $side)
	{
		$checker = ($side == 'left') ? 'hasLeft' : 'hasRight';
		$treatment = $this->getElementForLatestEventInEpisode($episode, 'Element_OphTrIntravitrealinjection_Treatment');
		if ($treatment && $treatment->$checker()) {
			return $treatment;
		}
	}

	/**
	 * get the drug name for the patient, episode and side from the most recent injection event, if it exists.
	 *
	 * @param $patient
	 * @param $episode
	 * @param $side
	 * @return mixed
	 */
	public function getLetterTreatmentDrugForSide($patient, $episode, $side)
	{
		if ($injection = $this->getPreviousTreatmentForSide($patient, $episode, $side)) {
			return $injection->{$side . '_drug'}->name;
		}
	}

	/**
	 * get the most recent drug for the left side in the current subspecialty episode for the patient
	 *
	 * @param $patient
	 * @return mixed
	 */
	public function getLetterTreatmentDrugLeft($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			return $this->getLetterTreatmentDrugForSide($patient, $episode, 'left');
		}
	}

	/**
	 * get the most recent drug for the right side in the current subspecialty episode for the patient
	 *
	 * @param $patient
	 * @return mixed
	 */
	public function getLetterTreatmentDrugRight($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			return $this->getLetterTreatmentDrugForSide($patient, $episode, 'right');
		}
	}

	/**
	 * get the most recent drug for both sides in the current subspecialty episode for the patient
	 *
	 * @param $patient
	 * @return string
	 */
	public function getLetterTreatmentDrugBoth($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			$res = '';
			$right = $this->getLetterTreatmentDrugForSide($patient, $episode, 'right');
			$left = $this->getLetterTreatmentDrugForSide($patient, $episode, 'left');
			if ($right) {
				$res = $right . ' injection to the right eye';
				if ($left) {
					$res .= ', and ' . $left . ' injection to the left eye';
				}

			}
			elseif ($left) {
				$res = $left . ' injection on the left eye';
			}
			return $res;
		}
	}

	/**
	 * get the most recent treatment number for the patient, episode and side
	 *
	 * @param $patient
	 * @param $episode
	 * @param $side
	 * @return mixed
	 */
	public function getLetterTreatmentNumberForSide($patient, $episode, $side)
	{
		if ($injection = $this->getPreviousTreatmentForSide($patient, $episode, $side)) {
			return $injection->{$side . '_number'};
		}
	}

	/**
	 * get the most recent treatment number for the left side in the current subspecialty episode for the patient
	 *
	 * @param $patient
	 * @return mixed
	 */
	public function getLetterTreatmentNumberLeft($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			return $this->getLetterTreatmentNumberForSide($patient, $episode, 'left');
		}
	}

	/**
	 * get the most recent treatment number for the right side in the current subspecialty episode for the patient
	 *
	 * @param $patient
	 * @return mixed
	 */
	public function getLetterTreatmentNumberRight($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			return $this->getLetterTreatmentNumberForSide($patient, $episode, 'right');
		}
	}

	/**
	 * get the most recent treatment number for both eyes in the current subspecialty episode for the patient
	 *
	 * @param Patient $patient
	 */
	public function getLetterTreatmentNumberBoth($patient)
	{
		$right = $this->getLetterTreatmentNumberRight($patient);
		$left = $this->getLetterTreatmentNumberLeft($patient);
		$res = '';
		if ($right) {
			$res = $right . ' on the right eye';
			if ($left) {
				$res .= ', and ' . $left . ' on the left eye';
			}
		}
		elseif ($left) {
			$res = $left . ' on the left eye';
		}
		return $res;
	}

	/**
	 * get the text string describing the post injection drops needed for the last injection event in the episode
	 *
	 * @param $patient
	 * @return string
	 */
	public function getLetterPostInjectionDrops($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($el = $this->getElementForLatestEventInEpisode($episode, 'Element_OphTrIntravitrealinjection_PostInjectionExamination')) {
				$drops = array();
				if ($el->hasRight()) {
					$drops[] = $el->right_drops->name . " to the right eye";
				}
				if ($el->hasLeft()) {
					$drops[] = $el->left_drops->name . " to the left eye";
				}
				return implode(", and ", $drops);
			}
		}
	}
}
