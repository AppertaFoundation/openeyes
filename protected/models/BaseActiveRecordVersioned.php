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
	private $enable_archive = true;
	private $fetch_from_archive = false;
	public $unique_id = null;
	public $deleted_at = null;

	/* Disable archiving on save() */

	public function noArchive()
	{
		$this->enable_archive = false;

		return $this;
	}

	/* Re-enable archiving on save() */

	public function withArchive()
	{
		$this->enable_archive = true;

		return $this;
	}

	/* Fetch from archive */

	public function fromArchive()
	{
		$this->fetch_from_archive = true;

		return $this;
	}

	/* Disable fetch from archive */

	public function notFromArchive()
	{
		$this->fetch_from_archive = false;

		return $this;
	}

	public function getTableSchema()
	{
		if ($this->fetch_from_archive) {
			return $this->getDbConnection()->getSchema()->getTable($this->tableName().'_archive');
		}

		return parent::getTableSchema();
	}

	public function isArchived()
	{
		return $this->unique_id;
	}

	public function getPreviousVersion()
	{
		$condition = 'id = :id';
		$params = array(':id' => $this->id);

		if ($this->isArchived()) {
			$condition .= ' and unique_id < :unique_id';
			$params[':unique_id'] = $this->unique_id;
		}

		return $this->model()->fromArchive()->find(array(
			'condition' => $condition,
			'params' => $params,
			'order' => 'unique_id desc',
		));
	}

	/* Return all previous versions ordered by most recent */

	public function getPreviousVersions()
	{
		$condition = 'id = :id';
		$params = array(':id' => $this->id);

		if ($this->isArchived()) {
			$condition .= ' and unique_id = :unique_id';
			$params[':unique_id'] = $this->unique_id;
		}

		return $this->model()->fromArchive()->findAll(array(
			'condition' => $condition,
			'params' => $params,
			'order' => 'unique_id desc',
		));
	}

	public function getArchiveTableSchema()
	{
		return Yii::app()->db->getSchema()->getTable($this->tableName().'_archive');
	}

	public function getCommandBuilder()
	{
		return new OECommandBuilder($this->getDbConnection()->getSchema());
	}

	public function updateByPk($pk,$attributes,$condition='',$params=array())
	{
		$table = $this->getTableSchema();

		$transaction = Yii::app()->db->getCurrentTransaction() === null ? Yii::app()->db->beginTransaction() : false;

		try {
			if (!$this->enable_archive || $this->archiveToTableByPk($pk,$condition,$params)) {
				$result = parent::updateByPk($pk,$attributes,$condition,$params);

				if ($transaction && $result) {
					$transaction->commit();
				}

				return $result;
			}
		} catch (Exception $e) {
			if ($transaction) {
				$transaction->rollback();
			}
			throw $e;
		}

		if ($transaction) {
			$transaction->rollback();
		}

		return false;
	}

	public function updateAll($attributes,$condition='',$params=array())
	{
		$transaction = Yii::app()->db->getCurrentTransaction() === null ? Yii::app()->db->beginTransaction() : false;

		try {
			if (!$this->enable_archive || $this->archiveAllToTable($condition,$params)) {
				$result = parent::updateAll($attributes,$condition,$params);

				if ($transaction && $result) {
					$transaction->commit();
				}

				return $result;
			}
		} catch (Exception $e) {
			if ($transaction) {
				$transaction->rollback();
			}
			throw $e;
		}

		if ($transaction) {
			$transaction->rollback();
		}

		return false;
	}

	public function archiveToTableByPk($pk, $condition, $params=array())
	{
		$builder = $this->getCommandBuilder();
		$table = $this->getTableSchema();
		$table_archive = $this->getArchiveTableSchema();

		$criteria = $builder->createPkCriteria($table,$pk,$condition,$params);

		$command = $builder->createInsertFromTableCommand($table_archive,$table,$criteria);

		return $command->execute();
	}

	public function archiveAllToTable($condition,$params)
	{
		foreach (Yii::app()->db->createCommand()
			->select("*")
			->from($this->tableName())
			->where($condition, $params)
			->queryAll() as $row) {

			if (!$this->archiveToTableByPk($row['id'], "id = :id", array(":id" => $row['id']))) {
				return false;
			}
		}

		return true;
	}

	public function save($runValidation=true, $attributes=null, $allow_overriding=false)
	{
		if ($this->isArchived()) {
			throw new Exception("save() should not be called on archived model instances.");
		}

		return parent::save($runValidation, $attributes, $allow_overriding);
	}
}
