<?php

/**
 * Various methods to be use by the clinical controller.
 */

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
		$siteElementTypeObjects = array();

		$sql = 'SELECT
					site_element_type.id
				FROM
					site_element_type,
					possible_element_type
				WHERE
					specialty_id = :specialty_id
				AND
					event_type_id = :event_type_id
				AND
					site_element_type.possible_element_type_id = possible_element_type.id
				ORDER BY
					possible_element_type.order
				';

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);
		$command->bindValue('specialty_id', $specialty->id);
		$command->bindParam('event_type_id', $eventTypeId);
		$results = $command->queryAll();

		foreach ($results as $result) {
			$siteElementTypeObjects[] = SiteElementType::Model()->findByPk($result['id']);
		}

		return $siteElementTypeObjects;
	}

	/**
	 * Return all elements that actually exist for this event ID
	 *
	 * @param array   $siteElementTypes  list of SiteElementType objects
	 * @param integer $eventId           event ID to check for
	 *
	 * @return array
	 */
	public function getEventElementTypes($siteElementTypes, $eventId)
	{
		$elements = array();
		foreach ($siteElementTypes as $siteElementType) {
			$elementClassName = $siteElementType->possibleElementType->elementType->class_name;
			$element = $elementClassName::model()->find('event_id = ?', array($eventId));

			if ($element) {
				// Element exists, add it to the array
				$elements[] = array(
					'element' => $element,
					'siteElementType' => $siteElementType
				);
			}
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

			if ($siteElementType->required ||	isset($data[$elementClassName])) {
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
}
