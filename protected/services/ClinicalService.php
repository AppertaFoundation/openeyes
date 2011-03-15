<?php

class ClinicalService
{
	/**
	 * Returns an array of siteElementType objects for a given element type id.
	 *
	 * @param int $eventTypeId
	 * @param object $firm
	 *
	 * @return array Object
	 */
	public function getSiteElementTypeObjects($eventTypeId, $firm) {
		$specialty = $firm->serviceSpecialtyAssignment->specialty;

		// Get an array of all element types for this specialty and event type
		$criteria = new CDbCriteria;
		$criteria->join = 'LEFT JOIN possible_element_type possibleElementType
			ON t.possible_element_type_id = possibleElementType.id';
		$criteria->addCondition('t.specialty_id = :specialty_id');
		$criteria->addCondition('possibleElementType.event_type_id = :event_type_id');
		$criteria->order = 'possibleElementType.order';
		$criteria->params = array(
			':specialty_id' => $specialty->id,
			':event_type_id' => $eventTypeId
		);

		return SiteElementType::model()->findAll($criteria);
	}

	/**
	 * Returns the episode for a patient and specialty if there is one.
	 *
	 * @param integer $specialtyId      id of the specialty
	 * @param integer $patientId        id of the patient
	 *
	 * @return object $episode if found, null otherwise
	 */
	public static function getEpisodeBySpecialtyAndPatient($specialtyId, $patientId)
	{
		$criteria = new CDbCriteria;
		$criteria->join = 'LEFT JOIN firm ON t.firm_id = firm.id 
			LEFT JOIN service_specialty_assignment serviceSpecialtyAssignment ON 
				serviceSpecialtyAssignment.id = firm.service_specialty_assignment_id 
			LEFT JOIN patient ON t.patient_id = patient.id';
		$criteria->addCondition('serviceSpecialtyAssignment.specialty_id = :specialty_id');
		$criteria->addCondition('patient.id = :patient_id');
		$criteria->addCondition('t.end_date IS NULL');
		$criteria->params = array(
			':specialty_id' => $specialtyId,
			':patient_id' => $patientId
		);

		return Episode::model()->find($criteria);
	}

	/**
	 * Return all elements that actually exist for this event ID
	 *
	 * @param array   $siteElementTypes  list of SiteElementType objects
	 * @param integer $eventId           event ID to check for
	 * @param boolean $createElement     whether to create an element if none exists
	 *
	 * @return array
	 */
	public function getEventElementTypes($siteElementTypes, $eventId, $createElement = false)
	{
		$elements = array();
		foreach ($siteElementTypes as $siteElementType) {
			$elementClassName = $siteElementType->possibleElementType->elementType->class_name;
			$element = $elementClassName::model()->find('event_id = ?', array($eventId));

			if ($createElement) {
				$preExisting = isset($element);
				if (!$element) {
					$element = new $elementClassName;
				}
			}

			$data = array(
				'element' => $element,
				// need to reload it fresh to prevent eager-loading of
				// possibleElementType and elementType from above
				// @todo: figure out if there's a way around that?
				'siteElementType' => SiteElementType::model()->findByPk($siteElementType->id)
			);
			if ($createElement) {
				$data['preExisting'] = $preExisting;
			}
			$elements[] = $data;
		}

		return $elements;
	}

	/**
	 * Loop through all site element types. If it's a required site element type,
	 * validate it. If it's not, check for its presence then validate if present.
	 *
	 * @param array $siteElementTypes   list of site element type objects
	 * @param array $data               $_POST data to be validated
	 *
	 * return array
	 *		valid => boolean
	 *		elements => element data array
	 */
	public function validateElements($siteElementTypes, $data)
	{
		$valid = true;
		$elements = array();

		foreach ($siteElementTypes as $siteElementType) {
			$elementClassName = $siteElementType->possibleElementType->elementType->class_name;

			if ($siteElementType->required || isset($data[$elementClassName])) {
				$element = new $elementClassName;
				$element->attributes = $data[$elementClassName];

				if (!$element->validate()) {
					$valid = false;
				} else {
					$elements[] = $element;
				}
			}
		}

		return array(
			'valid' => $valid,
			'elements' => $elements
		);
	}

	/**
	 * Update elements based on $_POST data
	 *
	 * @param array   $elements   array of SiteElementTypes
	 * @param array   $data       $_POST data to update
	 * @param integer $eventId    id of the associated event
	 *
	 * @return boolean $success   true if all elements suceeded, false otherwise
	 */
	public function updateElements($elements, $data, $eventId)
	{
		$success = true;

		foreach ($elements as $element) {
			$elementClassName = get_class($element['element']);

			if ($data[$elementClassName]) {
				// The user has entered information for this element
				// Check if it's a pre-existing element
				if (empty($element['preExisting'])) {
					// It's not pre-existing so give it an event id
					$element['element']->event_id = $eventId;
				}

				// @todo - is there a risk they could change the event id here?
				$element['element']->attributes = $data[$elementClassName];
			}

			try {
				$element['element']->save();
			} catch (Exception $e) {
				$success = false;
			}
		}

		return $success;
	}
}
