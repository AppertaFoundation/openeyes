<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class VerifyVersionTablesCommand extends CConsoleCommand {
	public function run($args)
	{
		$this->nuke_cache();

		if (isset($args[0])) {
			if ($args[0] == 'all') {
				$this->scanModels("models");

				foreach (Yii::app()->modules as $module => $blah) {
					if (file_exists("modules/$module/models")) {
						$this->scanModels("modules/$module/models");
					}
				}
			} else {
				if (!file_exists("modules/{$args[0]}/models")) {
					die("Path not found: modules/{$args[0]}/models\n");
				}
				$this->scanModels("modules/{$args[0]}/models");
			}
		} else {
			$this->scanModels("models");

			$dh = opendir("modules");

			while ($file = readdir($dh)) {
				if (!preg_match('/^\.\.?$/',$file) && file_exists("modules/$file/models")) {
					$this->scanModels("modules/$file/models");
				}
			}

			closedir($dh);
		}

		foreach (Yii::app()->db->getSchema()->getTables() as $table) {
			if (preg_match('/_version$/',$table->name)) {
				if (!$this->tableExists(preg_replace('/_version$/','',$table->name))) {
					echo "Warning: orphaned version table: {$table->name}\n";
				}
			}
		}
	}

	public function scanModels($path)
	{
		$dh = opendir($path);

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				if (is_dir($path."/".$file)) {
					$this->scanModels($path."/".$file);
				} else {
					$this->scanFile($path."/".$file);
				}
			}
		}

		closedir($dh);
	}

	public function scanFile($path)
	{
		$data = file_get_contents($path);

		if (preg_match('/extends (.*?)[\s\t\r\n]+{/',$data,$m)) {
			if (in_array($m[1],array('BaseActiveRecordVersioned','BaseEventTypeElement','SplitEventTypeElement'))) {
				if (preg_match('/public function tableName\(\)[\r\n\s\t]+{[\r\t\n\s]+return \'(.*?)\'/s',$data,$m)) {
					if ($this->tableExists($m[1])) {
						if (!$this->tableExists($m[1].'_version')) {
							echo "Table {$m[1]}_version doesn't exist.\n";
						} else {
							$this->fieldsMatch($m[1]);
						}
					}
				}
			}
		}
	}

	public function tableExists($table)
	{
		return (boolean)Yii::app()->db->getSchema()->getTable($table);
	}

	public function fieldsMatch($table)
	{
		$_table = Yii::app()->db->getSchema()->getTable($table);
		$_table_version = Yii::app()->db->getSchema()->getTable($table.'_version');

		foreach ($_table->columns as $column => $properties) {
			if (!isset($_table_version->columns[$column])) {
				echo "$_table_version->name doesn't have column $column\n";
			} else if ($_table_version->columns[$column]->dbType != $properties->dbType) {
				echo "$_table_version->name\->$column has the wrong type ({$_table_version->columns[$column]->dbType} should be {$properties->dbType})\n";
			}
		}

		foreach ($_table_version->columns as $column => $properties) {
			if (!in_array($column,array('version_id','version_date'))) {
				if (!isset($_table->columns[$column])) {
					echo "$_table_version->name has extra column $column\n";
				}
			}
		}

		/*foreach ($_table->foreignKeys as $column => $properties) {
			if (!isset($_table_version->foreignKeys[$column])) {
				if ($this->table_soft_deleted_or_not_excluded($properties[0])) {
					echo "$_table_version->name doesn't have a foreign key on column $column\n";
				}
			} else {
				if ($_table_version->foreignKeys[$column][0] != $properties[0]) {
					echo "$_table_version->name foreign key on $column table doesn't match\n";
				}
				if ($_table_version->foreignKeys[$column][1] != $properties[1]) {
					echo "$_table_version->name foreign key on $column column doesn't match\n";
				}
			}
		}

		foreach ($_table_version->foreignKeys as $column => $properties) {
			if ($column != 'id') {
				if (!isset($_table->foreignKeys[$column])) {
					echo "$_table_version->name has extra foriegn key on column $column\n";
				}
			}
		}
		*/

		if (!isset($_table_version->columns['version_date'])) {
			echo "$_table_version->name doesn't have column version_date\n";
		} else if ($_table_version->columns['version_date']->dbType != 'datetime') {
			echo "$_table_version->name\->version_date has the wrong type ({$_table_version->columns['version_date']->dbType} should be datetime)\n";
		}

		if (!isset($_table_version->columns['version_id'])) {
			echo "$_table_version->name doesn't have column version_id\n";
		} else if (!in_array($_table_version->columns['version_id']->dbType,array('int(11)','int(10) unsigned'))) {
			echo "$_table_version->name\->version_id has the wrong type ({$_table_version->columns['version_id']->dbType} should be int(11) or int(10) unsigned)\n";
		}

		/*if ($this->is_soft_deleted($_table_version)) {
			if (!isset($_table_version->foreignKeys['id'])) {
				echo "$_table_version->name doesn't have foreign key on column id\n";
			} else {
				if ($_table_version->foreignKeys['id'][0] != $_table->name) {
					echo "$_table_version->name foreign key on id column table doesn't match\n";
				}
				if ($_table_version->foreignKeys['id'][1] != 'id') {
					echo "$_table_version->name foreign key on id column column doesn't match\n";
				}
			}
		} else {
			if (isset($_table_version->foreignKeys['id'])) {
				echo "$_table_version->name has a foreign key on column id\n";
			}
		}
		*/
	}

	public function nuke_cache()
	{
		$this->wipe_files("runtime/cache/");
	}

	public function wipe_files($dir, $root = true)
	{
		$dh = opendir($dir);

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				if (is_file("$dir/$file")) {
					if ($file != '.gitignore') {
						if (!@unlink("$dir/$file")) {
							echo "Error: unable to remove $dir/$file\n";
							exit;
						}
					}
				} else {
					$this->wipe_files("$dir/$file",false);
				}
			}
		}

		closedir($dh);

		if (!$root) {
			if (!@rmdir($dir)) {
				echo "Error: unable to remove $dir/$file\n";
				exit;
			}
		}
	}

	public function is_soft_deleted($table)
	{
		return (isset($table->columns['deleted']) || isset($table->columns['active']) || isset($table->columns['discontinued']));
	}

	public function table_soft_deleted_or_not_excluded($table_name)
	{
		$exclude = array('patient','practice','subspecialty','specialty','disorder','element_type','episode_status','eye','event_group','event_type','service_subspecialty_assignment','import_source','service','setting_field_type','period','protected_file','language');

		if (in_array($table_name,$exclude)) return false;

		return $this->is_soft_deleted(Yii::app()->db->getSchema()->getTable($table_name));
	}
}
