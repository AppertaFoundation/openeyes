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
	 * @return array Object
	 */
	public static function getSiteElementTypeObjects($eventTypeId, $firm) {
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
		$command->bindParam('specialty_id', $specialty->id);
		$command->bindParam('event_type_id', $eventTypeId);
		$results = $command->queryAll();

		foreach ($results as $result) {
			$siteElementTypeObjects[] = SiteElementType::Model()->findByPk($result['id']);
		}

		return $siteElementTypeObjects;
	}
}
