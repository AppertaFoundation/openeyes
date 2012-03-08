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

class ImportSampleDataCommand extends CConsoleCommand {
	
	const DATA_FOLDER = 'data';

	public function getName() {
		return 'Import Sample Data Command.';
	}
	
	public function getHelp() {
		return "Imports sample data into the database.\n";
	}

	public function run($args) {
		$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::DATA_FOLDER . DIRECTORY_SEPARATOR;
		$connection = Yii::app()->db;
		$connection->createCommand("SET foreign_key_checks = 0;")->execute();
		foreach(glob($path."*.csv") as $file_path) {
			$table = substr(basename($file_path), 0, -4);
			echo "Clearing $table data.\n";
			$connection->createCommand("TRUNCATE TABLE $table")->execute();
			echo "Importing $table data...";
			$fh = fopen($file_path,"r");
			$columns = implode(',',fgetcsv($fh));
			fclose($fh);
			$query = "LOAD DATA LOCAL INFILE '$file_path' INTO TABLE $table
				FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
				LINES TERMINATED BY '\\n'
				IGNORE 1 LINES
				($columns);";
			$row_count = $connection->createCommand($query)->execute();
			echo "$row_count records, done.\n";
		}
		$connection->createCommand("SET foreign_key_checks = 1;")->execute();
	}
	
}
