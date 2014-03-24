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

/**
 * Module-aware fixture manager
 */
class DbFixtureManager extends CDbFixtureManager
{
	protected $fixtures = array();

	public function init()
	{
		$paths = array($this->basePath ?: Yii::getPathOfAlias("application.tests.fixtures"));
		foreach(array_keys(Yii::app()->modules) as $module) {
			$path = Yii::getPathOfAlias("{$module}.tests.fixtures");
			if (is_dir($path)) $paths[] = $path;
		}

		$schema = $this->getDbConnection()->getSchema();
		$suffix_len = strlen($this->initScriptSuffix);

		foreach ($paths as $path) {
			$dir = opendir($path);

			while (($file = readdir($dir))) {
				if (!preg_match("/\\.php$/", $file) || substr($file, -$suffix_len) == $this->initScriptSuffix) {
					continue;
				}

				$table = substr($file, 0, -4);
				if($schema->getTable($table)) $this->fixtures[$table] = "{$path}/{$file}";
			}

			closedir($dir);
		}

		parent::init();
	}

	/**
	 * @return array
	 */
	public function getFixtures()
	{
		return $this->fixtures;
	}

	/**
	 * @param string $tableName
	 */
	public function resetTable($tableName)
	{
		parent::resetTable($tableName);

		$version_table = "{$tableName}_version";
		if ($this->dbConnection->schema->getTable($version_table)) {
			$this->truncateTable($version_table);
		}
	}

	/**
	 * @param string $table_name
	 * @return array|false
	 */
	public function loadFixture($table_name)
	{
		if (!isset($this->fixtures[$table_name])) return false;

		$rows = array();
		$schema = $this->getDbConnection()->getSchema();
		$builder = $schema->getCommandBuilder();
		$table = $schema->getTable($table_name);
		$pk = $table->primaryKey;

		foreach(require($this->fixtures[$table_name]) as $alias => $row) {
			$builder->createInsertCommand($table, $row)->execute();
			if(!is_null($table->sequenceName) && is_string($pk)) {
				$row[$pk] = $builder->getLastInsertID($table);
			}
			$rows[$alias] = $row;
		}
		return $rows;
	}
}
