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

abstract class Resource extends DataObject
{
	/**
	 * Prefix to use for FHIR IDs if this is not the only resource that can map to its FHIR type
	 */
	static protected $fhir_prefix = null;

	/**
	 * Get prefix for FHIR IDs or null if none
	 *
	 * @return string|null
	 */
	static public function getFhirPrefix()
	{
		return static::$fhir_prefix;
	}

	/**
	 * Get the OpenEyes FHIR profile this resource conforms to
	 *
	 * @return string
	 */
	static public function getOeFhirProfile()
	{
		$url = 'http://openeyes.org.uk/fhir/' . \Yii::app()->version->coreVersion . '/profile/' . static::getFhirType();

		if (static::$fhir_prefix) {
			$class = new \ReflectionClass(get_called_class());
			$url .= "/{$class->getShortName()}";
		}

		return $url;
	}

	static public function fromFhir($fhirObject)
	{
		$resourceType = static::getFhirType();

		if ($fhirObject->resourceType != $resourceType) {
			throw new InvalidStructure("Expecting a resource of type '{$resourceType}', got '{$fhirObject->resourceType}'");
		}

		return parent::fromFhir($fhirObject);
	}

	protected $id = null;
	protected $last_modified = null;

	/**
	 * Get the internal ID of this resource (null if not an internal resource)
	 *
	 * @return int|null
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get version ID of this resource (null if not an internal resource)
	 *
	 * @return int|null
	 */
	public function getVersionId()
	{
		return $this->last_modified;
	}

	/**
	 * Get last modified timestamp of this resource (null if not an internal resource)
	 *
	 * @return int|null
	 */
	public function getLastModified()
	{
		return $this->last_modified;
	}

	public function toFhir()
	{
		$fhirObject = parent::toFhir();
		$fhirObject->resourceType = static::getFhirType();
		return $fhirObject;
	}
}
