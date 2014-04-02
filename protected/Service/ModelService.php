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

namespace Service;

/**
 * Services that provide access to the OE data model
 */
abstract class ModelService extends InternalService
{
	/**
	 * Default set of operations for a model service, override if required
	 */
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE);

	/**
	 * The primary model class for this service
	 *
	 * @var string
	 */
	static protected $primary_model;

	static public function load(array $params = array())
	{
		$class = static::$primary_model;
		return new static($class::model());
	}

	protected $model;

	/**
	 * @param BaseActiveRecord $model
	 */
	public function __construct(\BaseActiveRecord $model)
	{
		$this->model = $model;
	}

	/**
	 * @param int $id
	 * @return int
	 */
	public function getLastModified($id)
	{
		return strtotime($this->readModel($id)->last_modified_date);
	}

	/**
	 * @param int $id
	 * @return Resource
	 */
	public function read($id)
	{
		return $this->modelToResource($this->readModel($id));
	}

	/**
	 * @param int $id
	 * @param Resource $resource
	 */
	public function update($id, Resource $resource)
	{
		if (!$this->supportsOperation(self::OP_UPDATE)) {
			parent::update($id, $resource);
		}

		if (!($model = $this->model->findByPk($id))) {
			throw new NotFound(static::getServiceName() . " with ID '$id' not found");
		}
		$this->resourceToModel($resource, $model);
	}

	/**
	 * @param Resource $resource
	 * @return int
	 */
	public function create(Resource $resource)
	{
		if (!$this->supportsOperation(self::OP_CREATE)) {
			parent::create($resource);
		}

		$class = static::$primary_model;
		$model = new $class;
		$this->resourceToModel($resource, $model);
		return $model->id;
	}

	/**
	 * @param int $id
	 * @return BaseActiveRecord
	 */
	protected function readModel($id)
	{
		if (!$this->supportsOperation(self::OP_READ)) {
			throw new ProcessingNotSupported("Read operation not supported");
		}

		if (!($model = $this->model->findByPk($id))) {
			throw new NotFound(static::getServiceName() . " with ID '$id' not found");
		}

		return $model;
	}

	/**
	 * @param BaseActiveRecord $model
	 * @return Resource
	 */
	protected function modelToResource($model)
	{
		$class = static::getResourceClass();
		return new $class(array('id' => $model->id, 'last_modified' => strtotime($model->last_modified_date)));
	}

	/**
	 * @param Resource $resource
	 * @param BaseActiveRecord $model
	 */
	protected function resourceToModel($resource, $model)
	{
		throw new ProcessingNotSupported("Can't write resources of type '" . get_class($resource) . "' to model layer");
	}

	/**
	 * Get an instance of the model class to fill in with search details
	 *
	 * @return BaseActiveRecord
	 */
	protected function getSearchModel()
	{
		$class = static::$primary_model;
		return new $class(null);
	}

	/**
	 * Get a list of resources from an AR data provider
	 *
	 * @param CActiveDataProvider $dataProvider
	 * @return Resource[]
	 */
	protected function getResourcesFromDataProvider(\CActiveDataProvider $provider)
	{
		$class = static::getResourceClass();
		$resources = array();
		foreach ($provider->getData() as $model) {
			$resources[] = $this->modelToResource($model);
		}
		return $resources;
	}

	/*
	 * Save model object and throw a service layer exception on failure
	 *
	 * @param BaseActiveRecord $model
	 */
	protected function saveModel(\BaseActiveRecord $model)
	{
		if (!$model->save()) {
			throw new ValidationFailure("Validation failure on " . get_class($model), $model->errors);
		}
	}
}
