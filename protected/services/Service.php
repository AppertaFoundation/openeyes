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

abstract class Service
{
	/**
	 * @return Service
	 */
	static public function load(array $params = array())
	{
		return new static($params);
	}

	/**
	 * Get the last modified date for a resource without fetching it
	 *
	 * @param scalar $id
	 * @return int
	 */
	abstract public function getLastModified($id);

	/**
	 * Fetch a single resource by ID
	 *
	 * @param scalar $id
	 * @return Resource
	 */
	abstract public function read($id);

	/**
	 * Update a resource by ID
	 *
	 * @param scalar $id
	 * @param Resource $resource
	 */
	abstract public function update($id, Resource $resource);

	/**
	 * Delete a resource by ID
	 *
	 * @param scalar $id
	 */
	abstract public function delete($id);

	/**
	 * Create a new resource and return its ID
	 *
	 * @param Resource $resource
	 * @return scalar
	 */
	abstract public function create(Resource $resource);

	/**
	 * Search for resources according to the parameters passed
	 *
	 * @param array $params
	 * @return Resource[]
	 */
	abstract public function search(array $params);
}
