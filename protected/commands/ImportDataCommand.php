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
			
			// Convert csv file into format suitable for MySQL import
			$table = substr(basename($map_path), 0, -4);
			$map = file($map_path);
			$import_file_path = $path . trim($map[0]);
			$export_columns = explode(',',trim($map[1]));
			$tmp_path = $path . 'tmp';
			if(!file_exists($tmp_path)) {
				mkdir($path . 'tmp');
			}
			$file = file($import_file_path);
			$file_output = fopen($tmp_path . '/' . $table . '.csv', 'w+');
			foreach($file as $index => $line) {
				if(!$index) {
					$columns = str_getcsv($line, ',', '"');
					fputcsv($file_output, $export_columns, ',', '"');
				} else {
					$output = array(); 
					$record = str_getcsv($line, ',', '"');
					foreach($export_columns as $column) {
						$column_index = array_search($column, $columns);
						// FIXME: This is probably bad
						$output[] = $record[$column_index];
					}
					fputcsv($file_output, $output, ',', '"');
				}
			}
			fclose($file_output);
			
			// Import file into database
			$columns_string = implode(',', $export_columns);
			$query = "
			LOAD DATA LOCAL INFILE '$tmp_path/$table.csv' INTO TABLE $table
			FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
			LINES TERMINATED BY '\\n'
			IGNORE 1 LINES
			($columns_string);
			";
			
			// Truncate table
			$command = $connection->createCommand("TRUNCATE $table");
			$row_count = $command->execute();
				
			// Import records
			$command = $connection->createCommand($query);
			$row_count = $command->execute();
			
			// Remove import file
			unlink("$tmp_path/$table.csv");
			
		}
	}
}
