<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace services;

abstract class ResourceReference implements FhirCompatible
{
	/**
	 * @param \StdClass $fhir_object
	 * @return ResourceReference
	 */
	static public function fromFhir($fhir_object)
	{
		$url = $fhir_object->reference;

		if (!preg_match('|^(.*)/(.+)$|', $url, $m)) {
			throw new ProcessingNotSupported("Unsupported FHIR resource reference: {$url}");
		}

		if (!($ref = \Yii::app()->service->fhirIdToReference($m[1], $m[2]))) {
			throw new InvalidValue("Invalid local resource reference: {$url}");
		}

		return $ref;
	}

	/**
	 * @return Resource
	 */
	abstract public function resolve();
}
