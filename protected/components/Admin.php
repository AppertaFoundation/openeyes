<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


class Admin
{
	/**
	 * @var BaseActiveRecord
	 */
	protected $model;
	protected $modelName;
	protected $listTemplate = '//admin/generic/list';

	protected $listFields = array();

	protected $controller;

	/**
	 * @return BaseActiveRecord
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * @param BaseActiveRecord $model
	 */
	public function setModel(BaseActiveRecord $model)
	{
		$this->model = $model;
		if(!$this->modelName) {
			$this->modelName = get_class($model);
		}
	}

	/**
	 * @return string
	 */
	public function getModelName()
	{
		return $this->modelName;
	}

	/**
	 * @param string $modelName
	 */
	public function setModelName($modelName)
	{
		$this->modelName = $modelName;
	}

	/**
	 * @return ModelSearch
	 */
	public function getSearch()
	{
		return $this->search;
	}

	/**
	 * @param ModelSearch $search
	 */
	public function setSearch($search)
	{
		$this->search = $search;
	}

	/**
	 * @return string
	 */
	public function getListTemplate()
	{
		return $this->listTemplate;
	}

	/**
	 * @param string $listTemplate
	 */
	public function setListTemplate($listTemplate)
	{
		$this->listTemplate = $listTemplate;
	}

	/**
	 * @return array
	 */
	public function getListFields()
	{
		return $this->listFields;
	}

	/**
	 * @param array $listFields
	 */
	public function setListFields($listFields)
	{
		$this->listFields = $listFields;
	}

	public function __construct(BaseActiveRecord $model, BaseAdminController $controller)
	{
		$this->setModel($model);
		$this->controller = $controller;
		$this->search = new ModelSearch($this->model);

	}

	public function listModel()
	{
		if(!$this->model){
			throw new CHttpException(500, 'Nothing to list');
		}

		$this->audit('list');
		$this->render($this->listTemplate, array('admin' => $this));
	}

	public function searchAll()
	{
		$searchArray = array('type' => 'compare', 'compare_to' => array());
		$searchFirst = '';
		foreach($this->listFields as $field){
			if($searchFirst === ''){
				$searchFirst = $field;
			} else {
				$searchArray['compare_to'][] = $field;
			}
		}
		$this->search->addSearchItem($searchFirst, $searchArray);
	}

	protected function render($template, $data = array())
	{
		$this->controller->render($template, $data);
	}

	protected function audit($type)
	{
		Audit::add('admin-'.$this->modelName, $type);
	}
}