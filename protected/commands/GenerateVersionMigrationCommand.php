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

class GenerateVersionMigrationCommand extends CConsoleCommand {
	public function run($args) {
		$exclude = array('audit', 'audit_action', 'audit_ipaddr', 'audit_model', 'audit_module', 'audit_server', 'audit_type', 'audit_useragent', 'authassignment', 'authitem', 'authitemchild', 'eye', 'gender', 'import_source', 'pas_assignment', 'pas_patient_merged', 'report', 'report_dataset', 'report_dataset_element', 'report_dataset_element_field', 'report_dataset_element_join', 'report_dataset_related_entity', 'report_dataset_related_entity_table', 'report_dataset_related_entity_table_relation', 'report_dataset_related_entity_type', 'report_graph', 'report_graph_item', 'report_input', 'report_input_data_type', 'report_input_option', 'report_item', 'report_item_data_type', 'report_item_list_item', 'report_item_list_item_conditional', 'report_item_pair_field', 'report_query_type', 'report_validation_rule', 'report_validation_rule_type', 'tbl_audit_trail', 'tbl_migration', 'user_session');

		if (!empty($args)) {
			if (!file_exists("modules/{$args[0]}/models")) {
				die("Path modules/{$args[0]}/models does not exist.\n");
			}

			$module = $args[0];

			$args[0] = $this->getPointlesslyShortenedTableSegmentForModule($module);
		}

		if ($module) {
			$r = `echo yes |./yiic migrate --migrationPath=application.modules.$module.migrations create table_versioning`;
			preg_match('/\/migrations\/(.*?)\.php/',$r,$m);
			$fp = fopen("modules/$module/migrations/{$m[1]}.php","w");
			$migration = $m[1];
		} else {
			$r = `echo yes |./yiic migrate create table_versioning`;
			preg_match('/\/migrations\/(.*?)\.php)/',$r,$m);
			$fp = fopen("migrations/{$m[1]}.php","w");
			$migration = $m[1];
		}

		fwrite($fp,'<?php

class '.$migration.' extends CDbMigration
{
	public function up()
	{
');

		$i = 0;
		foreach (Yii::app()->db->getSchema()->getTables() as $table) {
			if ($this->matches($table, $args, $exclude)) {
				$create = $this->createArchiveTable($table);

				if ($i >0) fwrite($fp,"\n");

				fwrite($fp,"\t\t\$this->execute(\"\n$create\n\t\t\");\n\n");

				fwrite($fp,"\t\t\$this->alterColumn('{$table->name}_version','id','int(10) unsigned NOT NULL');\n");
				fwrite($fp,"\t\t\$this->dropPrimaryKey('id','{$table->name}_version');\n\n");

				fwrite($fp,"\t\t\$this->createIndex('{$table->name}_aid_fk','{$table->name}_version','id');\n");
				fwrite($fp,"\t\t\$this->addForeignKey('{$table->name}_aid_fk','{$table->name}_version','id','$table->name','id');\n\n");

				fwrite($fp,"\t\t\$this->addColumn('{$table->name}_version','version_date',\"datetime not null default '1900-01-01 00:00:00'\");\n\n");

				fwrite($fp,"\t\t\$this->addColumn('{$table->name}_version','version_id','int(10) unsigned NOT NULL');\n");
				fwrite($fp,"\t\t\$this->addPrimaryKey('version_id','{$table->name}_version','version_id');\n");
				fwrite($fp,"\t\t\$this->alterColumn('{$table->name}_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');\n");

				$i++;
			}
		}

		fwrite($fp,"\t}\n\n\tpublic function down()\n\t{\n");

		foreach (Yii::app()->db->getSchema()->getTables() as $table) {
			if ($this->matches($table, $args, $exclude)) {
				fwrite($fp,"\t\t\$this->dropTable('{$table->name}_version');\n");
			}
		}

		fwrite($fp,"\t}\n}\n");

		fclose($fp);
	}

	public function getPointlesslyShortenedTableSegmentForModule($module)
	{
		$dh = opendir("modules/$module/models");

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				$a = file_get_contents("modules/$module/models/$file");

				if (preg_match('/public function tableName\(\)[\s\t\r\n]+{[\s\r\n\t]+return \'(.*?)\'/s',$a,$m)) {
					if (preg_match('/^et_(.*?)_/',$m[1],$n)) {
						return $n[1];
					}

					if (preg_match('/^(.*?)_/',$m[1],$n)) {
						return $n[1];
					}
				}
			} 
		}

		closedir($dh);

		die("Unable to determine table segment for module $module\n");
	}

	public function matches($table, $args, $exclude)
	{
		if (empty($args)) {
			return !in_array($table->name,$exclude) && !preg_match('/^et_/',$table->name) && !preg_match('/^oph/',$table->name);
		} else {
			return preg_match('/^et_'.strtolower($args[0]).'_/',$table->name) || preg_match('/^'.strtolower($args[0]).'_/',$table->name);
		}
	}

	public function createArchiveTable($table)
	{
		$a = Yii::app()->db->createCommand("show create table $table->name;")->queryRow();

		$create = $a['Create Table'];

		$create = preg_replace('/CREATE TABLE `(.*?)`/',"CREATE TABLE `{$table->name}_version`",$create);

		preg_match_all('/  KEY `(.*?)`/',$create,$m);

		foreach ($m[1] as $key) {
			$_key = $key;

			if (strlen($_key) <= 60) {
				$_key = 'acv_'.$_key;
			} else {
				$_key[0] = 'a';
				$_key[1] = 'c';
				$_key[2] = 'v';
				$_key[3] = '_';
			}

			$create = preg_replace("/KEY `{$key}`/","KEY `$_key`",$create);
		}

		preg_match_all('/CONSTRAINT `(.*?)`/',$create,$m);

		foreach ($m[1] as $key) {
			$_key = $key;

			if (strlen($_key) <= 60) {
				$_key = 'acv_'.$_key;
			} else {
				$_key[0] = 'a';
				$_key[1] = 'c';
				$_key[2] = 'v';
				$_key[3] = '_';
			}

			$create = preg_replace("/CONSTRAINT `{$key}`/","CONSTRAINT `$_key`",$create);
		}

		$create = preg_replace('/AUTO_INCREMENT=[0-9]+/','AUTO_INCREMENT=1',$create);

		return $create;
	}
}
