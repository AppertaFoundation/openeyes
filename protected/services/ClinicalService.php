<?php

class ClinicalService
{
	/**
	 * Loop through an array of elements. If it's a required site element type,
	 * validate it. If it's not, check for its presence then validate if present.
	 * If validation passes for all the chosen elements then save them.
	 *
	 * @param array $elements   list of element objects
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
					$element->attributes = $data[$elementClassName];
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
		 * specialty for this patient. If so, add the new event to it. If not, create an
		 * episode and add it to that.
		 */
		$episode = $this->getOrCreateEpisode($firm, $patientId);
		$event = $this->createEvent($episode, $userId, $eventTypeId);

		// Create elements for the event
		foreach ($elementsToProcess as $element) {
			$element->event_id = $event->id;
			// @todo - for some reason Yii likes to try and update here instead of create.
			//	Find out why.
			$element->setIsNewRecord(true);

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
	 * @param array   $elements		array of SiteElementTypes
	 * @param array   $data			$_POST data to update
	 * @param object $event    		the associated event
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
				$element->attributes = $data[$elementClassName];

				$toSave[] = $element;

				$needsValidation = true;
			} elseif ($element->required) {
				// The form has failed to provide an array of data for a required element.
				// This isn't supposed to happen - a required element should at least have the
				// $data[$elementClassName] present, even if there's nothing in it.

				// @todo - this behaviour is ill defined. As the form for this element hasn't been
				// displayed, presumably it won't be again, so running validate() to display
				// the errors is pointless?
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
				// @todo - another example of Yii getting confused about save vs update
				$element->setIsNewRecord(true);
				$element->event_id = $event->id;
			}

			$element->save();
		}

		foreach ($toDelete as $element) {
			$element->delete();
		}

		return true;
	}

	/**
	 * Get all the elements for a combination of event type and specialty. If the elements
	 * already exist (i.e. they belong to an event) they are loaded from the db.
	 *
	 * @param object $eventType
	 * @param object $firm
	 * @param int $patientId
	 * @param object $userId
	 * @param object $event
	 * @return array
	 */
	public function getElements($eventType, $firm, $patientId, $userId, $event = null)
	{
		if (isset($event)) {
			$eventType = $event->eventType;
			$firm = $event->episode->firm;
			$patientId = $event->episode->patient_id;
			$criteria = $this->getCriteria($eventType, $firm, $patientId, $event);
		} else {
			$criteria = $this->getCriteria($eventType, $firm, $patientId);
		}

		$siteElementTypeObjects = SiteElementType::model()->findAll($criteria);

		$elements = array();

		// Loop through the site_element_type objects and create an element object for each one.
		foreach ($siteElementTypeObjects as $siteElementTypeObject) {
			$elementClassName = $siteElementTypeObject->possibleElementType->elementType->class_name;

			if ($event) {
				// This may already exist
				$element = $elementClassName::model()->find('event_id = ?', array($event->id));
			}

			if (isset($element)) {
				// Element already exists
				$element->firm = $firm;
				$element->patientId = $patientId;
				$element->userId = $userId;
				$element->viewNumber = $siteElementTypeObject->view_number;
				$element->required = $siteElementTypeObject->required;
			} else {
				// This is a new element
				$element = new $elementClassName(
					$firm,
					$patientId,
					$userId,
					$siteElementTypeObject->view_number,
					$siteElementTypeObject->required
				);
				$element->setDefaultOptions();
			}

			$elements[] = $element;

			unset($element);
		}

		return $elements;
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
		$specialtyId = $firm->serviceSpecialtyAssignment->specialty->id;
		$episode = Episode::model()->getBySpecialtyAndPatient($specialtyId, $patientId);

		if (!$episode) {
			$episode = new Episode();
			$episode->patient_id = $patientId;
			$episode->firm_id = $firm->id;
			// @todo - this might not be DB independent
			$episode->start_date = date("Y-m-d H:i:s");

			if (!$episode->save()) {
				throw new Exception('Unable to create create episode.');
			}
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
		$event->user_id = $userId;
		$event->event_type_id = $eventTypeId;
		$event->datetime = date("Y-m-d H:i:s");
		if (!$event->save()) {
			throw new Exception('Unable to save event.');
		}

		return $event;
	}

	/**
	 * Generates the criteria to get the list of site_element_type records
	 * from the DB.
	 *
	 * @param $eventType
	 * @param $firm
	 * @param $patientId
	 * @return object
	 */
	public function getCriteria($eventType, $firm, $patientId, $event = null)
	{
		$specialtyId = $firm->serviceSpecialtyAssignment->specialty_id;
		$episode = Episode::model()->getBySpecialtyAndPatient($specialtyId, $patientId);

		$criteria = new CDbCriteria;
		$criteria->join = 'LEFT JOIN possible_element_type possibleElementType
			ON t.possible_element_type_id = possibleElementType.id';
		$criteria->addCondition('t.specialty_id = :specialty_id');
		$criteria->addCondition('possibleElementType.event_type_id = :event_type_id');
		$criteria->order = 'possibleElementType.display_order';
		$criteria->params = array(
			':specialty_id' => $specialtyId,
			':event_type_id' => $eventType->id
		);

		// If this event_type has first_in_episode_possible set to 1 we have to add an extra criterion
		if ($eventType->first_in_episode_possible) {
			if (!isset($episode) || !$episode->hasEventOfType($eventType->id, $event)) {
				// It's the first of this event type in an episode or a new episode, get site_element_types
				// that have first_in_episode set to true
				$criteria->addCondition('first_in_episode = 1');
			} else {
				// It's not the first in episode for this event type, get site_element_types that have
				// first_in_episode set to false
				$criteria->addCondition('first_in_episode = 0');
			}
		}

		return $criteria;
	}
}
