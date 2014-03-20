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

class BaseActiveRecordVersioned extends BaseActiveRecord
{
	private $enable_version = true;
	private $fetch_from_version = false;
	public $version_id = null;

	/* Disable archiving on save() */

	public function noVersion()
	{
		$this->enable_version = false;

		return $this;
	}

	/* Re-enable archiving on save() */

	public function withVersion()
	{
		$this->enable_version = true;

		return $this;
	}

	/* Fetch from version */

	public function fromVersion()
	{
		$this->fetch_from_version = true;

		return $this;
	}

	/* Disable fetch from version */

	public function notFromVersion()
	{
		$this->fetch_from_version = false;

		return $this;
	}

	public function getTableSchema()
	{
		if ($this->fetch_from_version) {
			return $this->getDbConnection()->getSchema()->getTable($this->tableName().'_version');
		}

		return parent::getTableSchema();
	}

	public function getPreviousVersion()
	{
		$condition = 'id = :id';
		$params = array(':id' => $this->id);

		if ($this->version_id) {
			$condition .= ' and version_id < :version_id';
			$params[':version_id'] = $this->version_id;
		}

		return $this->model()->fromVersion()->find(array(
			'condition' => $condition,
			'params' => $params,
			'order' => 'version_id desc',
		));
	}

	/* Return all previous versions ordered by most recent */

	public function getPreviousVersions()
	{
		$condition = 'id = :id';
		$params = array(':id' => $this->id);

		if ($this->version_id) {
			$condition .= ' and version_id = :version_id';
			$params[':version_id'] = $this->version_id;
		}

		return $this->model()->fromVersion()->findAll(array(
			'condition' => $condition,
			'params' => $params,
			'order' => 'version_id desc',
		));
	}

	public function getVersionTableSchema()
	{
		return Yii::app()->db->getSchema()->getTable($this->tableName().'_version');
	}

	public function getCommandBuilder()
	{
		return new OECommandBuilder($this->getDbConnection()->getSchema());
	}

	public function updateByPk($pk,$attributes,$condition='',$params=array())
	{
		$transaction = $this->dbConnection->beginInternalTransaction();

		try {
			if ($this->enable_version) $this->versionToTableByPk($pk, $condition, $params);

			$result = parent::updateByPk($pk,$attributes,$condition,$params);

			// No big deal if $result is 0, it just means the row was unchanged so no new version row is required
			$result ? $transaction->commit() : $transaction->rollback();

			return $result;
		} catch (Exception $e) {
			$transaction->rollback();
			throw $e;
		}
	}

	public function updateAll($attributes,$condition='',$params=array())
	{
		$transaction = $this->dbConnection->beginInternalTransaction();

		try {
			if ($this->enable_version) $this->versionAllToTable($condition, $params);

			$result = parent::updateAll($attributes,$condition,$params);
			$result ? $transaction->commit() : $transaction->rollback();

			return $result;
		} catch (Exception $e) {
			$transaction->rollback();
			throw $e;
		}
	}

	public function versionToTableByPk($pk, $condition, $params=array())
	{
		$builder = $this->getCommandBuilder();
		$table = $this->getTableSchema();
		$table_version = $this->getVersionTableSchema();

		$criteria = $builder->createPkCriteria($table,$pk,$condition,$params);

		$builder->createInsertFromTableCommand($table_version,$table,$criteria)->execute();
	}

	public function versionAllToTable($condition, $params=array())
	{
		$builder = $this->getCommandBuilder();
		$table = $this->getTableSchema();
		$table_version = $this->getVersionTableSchema();

		$criteria = $builder->createCriteria($condition, $params);

		$builder->createInsertFromTableCommand($table_version,$table,$criteria)->execute();
	}

	public function save($runValidation=true, $attributes=null, $allow_overriding=false)
	{
		if ($this->version_id) {
			throw new Exception("save() should not be called on versiond model instances.");
		}

		return parent::save($runValidation, $attributes, $allow_overriding);
	}

	public function resetScope($resetDefault=true)
	{
		$this->enable_version = true;
		$this->fetch_from_version = false;

		return parent::resetScope($resetDefault);
	}
}
