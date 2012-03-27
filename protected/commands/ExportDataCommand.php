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

class ExportDataCommand extends CConsoleCommand {
	
	const DATA_FOLDER = 'data/export';

	public function getName() {
		return 'Export Data Command.';
	}
	
	public function getHelp() {
		return "Export data from database to CSV files.\n";
	}

	public function run($args) {
		
		// Initialise db
		$connection = Yii::app()->db;
		$tables = $connection->createCommand("SHOW TABLES")->queryColumn();
		
		$path = Yii::app()->basePath . '/' . self::DATA_FOLDER . '/';
		foreach($tables as $table) {
			echo "Exporting $table...";
			$columns = $connection->createCommand("SHOW COLUMNS FROM `$table`")->queryColumn();
			$data = $connection->createCommand("SELECT * from `$table`")->queryAll();
			if($data) {
				$file_output = fopen($path . $table . '.csv', 'w+');
				fputcsv($file_output, $columns, ',', '"');
				$records = 0;
				foreach($data as $record) {
					fputcsv($file_output, $record, ',', '"');
					$records++;
				}
				fclose($file_output);
				echo "$records records, done.\n";
			} else {
				echo "skipping (empty)\n";
			}
		}
	}
}
