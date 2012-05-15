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

class ClinicalService
{
	/**
	 * Loop through an array of elements. If it's a required site element type,
	 * validate it. If it's not, check for its presence then validate if present.
	 * If validation passes for all the chosen elements then save them.
	 *
	 * @param array $elements		list of element objects
	 * @param array $data		array of data from $_POST to be saved
	 *
	 * return eventId => int || false
	 */
	public function createElements($elements, $data, $firm, $patientId, $userId, $eventTypeId)
	{
		$valid = true;

		$elementsToProcess = array();

		// Go through the array of elements to see which the user is attempting to
		// create, which are required and whether they pass validation.
		foreach ($elements as $element) {
			$elementClassName = get_class($element);

			if ($element->required || isset($data[$elementClassName])) {
				if (isset($data[$elementClassName])) {
					$element->attributes = Helper::convertNHS2MySQL($data[$elementClassName]);
				}

				if (!$element->validate()) {
					$valid = false;
				} else {
					$elementsToProcess[] = $element;
				}
			}
		}

		if (!$valid) {
			return false;
		}

		/**
		 * Create the event. First check to see if there is currently an episode for this
		 * subspecialty for this patient. If so, add the new event to it. If not, create an
		 * episode and add it to that.
		 */
		$episode = $this->getOrCreateEpisode($firm, $patientId);
		$event = $this->createEvent($episode, $userId, $eventTypeId);

		// Create elements for the event
		foreach ($elementsToProcess as $element) {
			$element->event_id = $event->id;

			// No need to validate as it has already been validated and the event id was just generated.
			if (!$element->save(false)) {
				throw new Exception('Unable to save element ' . get_class($element) . '.');
			}
		}

		return $event->id;
	}

	/**
	 * Update elements based on arrays passed over from $_POST data
	 *
	 * @param array		$elements		array of SiteElementTypes
	 * @param array		$data			$_POST data to update
	 * @param object $event				the associated event
	 *
	 * @return boolean $success		true if all elements suceeded, false otherwise
	 */
	public function updateElements($elements, $data, $event)
	{
		$success = true;
		$toDelete = array();
		$toSave = array();

		foreach ($elements as $element) {
			$elementClassName = get_class($element);
			$needsValidation = false;

			if (isset($data[$elementClassName])) {
				$element->attributes = Helper::convertNHS2MySQL($data[$elementClassName]);

				$toSave[] = $element;

				$needsValidation = true;
			} elseif ($element->required) {
				// The form has failed to provide an array of data for a required element.
				// This isn't supposed to happen - a required element should at least have the
				// $data[$elementClassName] present, even if there's nothing in it.
				$success = false;
			} elseif ($element->event_id) {
				// This element already exists, isn't required and has had its data deleted.
				// Therefore it needs to be deleted.
				$toDelete[] = $element;
			}

			if ($needsValidation) {
				if (!$element->validate()) {
					$success = false;
				}
			}
		}

		if (!$success) {
			// An element failed validation or a required element didn't have an
			// array of data provided for it.
			return false;
		}

		foreach ($toSave as $element) {
			if (!isset($element->event_id)) {
				$element->event_id = $event->id;
			}

			if (!$element->save()) {
				OELog::log("Unable to save element: $element->id ($elementClassName): ".print_r($element->getErrors(),true));
				throw new SystemException('Unable to save element: '.print_r($element->getErrors(),true));
			}
		}

		foreach ($toDelete as $element) {
			$element->delete();
		}

		return true;
	}

	/**
	 * Find the episode for this event or, if there isn't one, create one.
	 *
	 * @param object $firm
	 * @param int $patientId
	 * @return object
	 */
	public function getOrCreateEpisode($firm, $patientId)
	{
		$subspecialtyId = $firm->serviceSubspecialtyAssignment->subspecialty->id;
		$episode = Episode::model()->getBySubspecialtyAndPatient($subspecialtyId, $patientId);

		if (!$episode) {
			$episode = new Episode();
			$episode->patient_id = $patientId;
			$episode->firm_id = $firm->id;
			$episode->start_date = date("Y-m-d H:i:s");

			if (!$episode->save()) {
				OELog::log("Unable to create new episode for patient_id=$episode->patient_id, firm_id=$episode->firm_id, start_date='$episode->start_date'");
				throw new Exception('Unable to create create episode.');
			}

			OELog::log("New episode created for patient_id=$episode->patient_id, firm_id=$episode->firm_id, start_date='$episode->start_date'");

			Yii::app()->event->dispatch('episode_after_create', array('episode' => $episode));
				
		}

		return $episode;
	}

	/**
	 * Create a new event for an episode.
	 *
	 * @param object $episode
	 * @param int $userId
	 * @param int $eventTypeId
	 * @return object
	 */
	public function createEvent($episode, $userId, $eventTypeId)
	{
		$event = new Event();
		$event->episode_id = $episode->id;
		$event->event_type_id = $eventTypeId;
		$event->datetime = date("Y-m-d H:i:s");
		if (!$event->save()) {
			OELog::log("Failed to creat new event for episode_id=$episode->id, event_type_id=$eventTypeId, datetime='$event->datetime'");
			throw new Exception('Unable to save event.');
		}

		OELog::log("Created new event for episode_id=$episode->id, event_type_id=$eventTypeId, datetime='$event->datetime'");

		if (!$event->addIssue('Operation requires scheduling')) {
			OELog::log("Failed to mark new event as requiring scheduling in the event_issue table: episode_id=$episode->id, event_type_id=$eventTypeId, datetime='$event->datetime'");
			throw new Exception('Unable to store event_issue');
		}

		return $event;
	}

	/**
	 * Get all the elements for a the current module's event type
	 *
	 * @param $event_type_id
	 * @return array
	 */
	public function getDefaultElements($action, $event=false, $event_type_id=false) {
		$etc = new BaseEventTypeController(1);
		$etc->event = $event;
		return $etc->getDefaultElements($action, $event_type_id);
	}

	/**
	 * Get the optional elements for the current module's event type
	 * This will be overriden by the module
	 *
	 * @param $event_type_id
	 * @return array
	 */
	public function getOptionalElements($action, $event=false) {
		return array();
	}
}
