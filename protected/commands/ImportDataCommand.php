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

class ImportDataCommand extends CConsoleCommand {

	const DATA_FOLDER = 'data/import';

	public function getName() {
		return 'Import Data Command.';
	}

	public function getHelp() {
		return "Import data from csv into the database.\n";
	}

	/**
	 * Parse csv files from Google Docs, process them into the right format for MySQL import, and import them
	 */
	public function run($args) {

		// Initialise db
		$connection = Yii::app()->db;
		$command = $connection->createCommand("SET foreign_key_checks = 0;");
		$row_count = $command->execute();

		$path = Yii::app()->basePath . '/' . self::DATA_FOLDER . '/';
		foreach(glob($path."*.map") as $map_path) {
			$table = substr(basename($map_path), 0, -4);
			echo "Importing $table data...";
				
			// Truncate existing data
			$row_count = $connection->createCommand("TRUNCATE $table")->execute();

			// Get mapping info
			$map = file($map_path);
			$import_file_path = $path . trim($map[0]);
			$export_columns = explode(',',trim($map[1]));
				
			$file = file($import_file_path);
			$row_count = 0;
			$block_size = 1000;
			$values = array();
			foreach($file as $index => $line) {
				if (!strlen(trim($line))) {
					continue;
				}
				
				if(!$index) {
					$columns = str_getcsv($line, ',', '"');
				} else {
					$row_count++;
					$output = array();
					$record = str_getcsv($line, ',', '"');
					foreach($export_columns as $column) {
						$column_index = array_search($column, $columns);
						$output[] = $record[$column_index];
					}
					$values[] = $output;
					if(!($row_count % $block_size)) {
						// Insert values in blocks to better handle very large tables
						$this->insertBlock($table, $export_columns, $values);
						$values = array();
					}
				}
			}
			if(!empty($values)) {
				// Insert remaining values
				$this->insertBlock($table, $export_columns, $values);
			}
			echo "imported $row_count records, done.\n";
		}
	}

	/**
	 * Insert a block of records into a table
	 * @param string $table
	 * @param array $columns
	 * @param array $records
	 */
	protected function insertBlock($table, $columns, $records) {
		$db = Yii::app()->db;
		foreach($columns as &$column) {
			$column = $db->quoteColumnName($column);
		}
		$insert = array();
		foreach($records as $record) {
			foreach($record as &$field) {
				if($field != 'NULL') {
					$field = $db->quoteValue($field);
				}
			}
			$insert[] = '('.implode(',', $record).')';
		}
		$query = "INSERT INTO ".$db->quoteTableName($table)." (".implode(',',$columns).") VALUES ".implode(',', $insert);
		//echo "$query\n";
		$db->createCommand($query)->execute();
	}

}
