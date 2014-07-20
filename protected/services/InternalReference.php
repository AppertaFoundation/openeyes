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

/**
 * Reference to an internal resource
 */
class InternalReference extends ResourceReference
{
	// NB these are internal types and IDs, not FHIR/API ones
	private $service_name;
	private $id;

	/**
	 * @params string $service_name
	 * @param scalar $id
	 */
	public function __construct($service_name, $id)
	{
		$this->service_name = $service_name;
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getServiceName()
	{
		return $this->service_name;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getVersionId()
	{
		return $this->getService()->getLastModified($this->id);
	}

	/**
	 * @return int
	 */
	public function getLastModified()
	{
		return $this->getService()->getLastModified($this->id);
	}

	/**
	 * @return Resource
	 */
	public function resolve()
	{
		return $this->getService()->read($this->id);
	}

	/**
	 * @return bool
	 */
	public function delete()
	{
		return $this->getService()->delete($this->id);
	}

	/**
	 * @param StdClass $fhirObject
	 */
	public function fhirUpdate(\StdClass $fhirObject)
	{
		$this->getService()->fhirUpdate($this->id, $fhirObject);
	}

	/**
	 * @return StdClass
	 */
	public function toFhir()
	{
		return (object)array("reference" => \Yii::app()->service->referenceToFhirUrl($this));
	}

	/**
	 * @return Service
	 */
	protected function getService()
	{
		return \Yii::app()->service->{$this->service_name};
	}
}
