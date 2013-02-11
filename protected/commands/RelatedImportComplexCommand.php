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

class RelatedImportComplexCommand extends CConsoleCommand {

	const DATA_FOLDER = 'data/import';

	public function getName() {
		return 'Related Import Complex Data Command.';
	}

	public function getHelp() {
		return <<<EOH
Import data from csv that is related to data already defined in the database, and related to each other in separate tables:
	[import_name].cpxmap
	a file that contains the name of files that contain data to be imported - specifies order of import, so dependent data should appear later
	in the file
	[import_name]_[table_name].csv
	data to be imported into [table_name]. First line is column headers which match the column names in the table. With the following special cases:
		a) imp_id - a unique identifier for that row of data to use when mapping related data to that row
		b) [[column_name]=[[table_name].[table_column]] this column will be defined by the related table lookup specified by [table_name].[table_column]
		c) [[column_name]=[[table_name].imp_id] this column will be defined by the related table row value.

EOH;
	}
	
	private $column_value_map = array();
	private $imp_id_map = array();
	
	/**
	 * Parse csv files from Google Docs, process them into the right format for MySQL import, and import them
	 */
	public function run($args) {

		// Initialise db
		$connection = Yii::app()->db;
		$row_count = 0;

		$path = Yii::app()->basePath . '/' . self::DATA_FOLDER . '/';
		foreach(glob($path."*.cpxmap") as $map_path) {
			$imp_name = substr(basename($map_path), 0, -7);
			echo "Performing $imp_name import ...\n";
				
			// Get mapping info
			$map = file($map_path);
			foreach ($map as $map_idx => $tbl_file) {
				// get the table name
				$tbl_file = rtrim($tbl_file);
				if (preg_match("/^".$imp_name."_([^\.]+)\.csv/",$tbl_file, $match) ) {
					$table = $match[1];
					echo "Processing file $map_idx, $tbl_file ...\n";
				}
				else {
					echo "ERROR: bad name for file\n";
					continue;
				}
				
				$file = file($path . $tbl_file);
				$columns = array();
				$row_count = 0;
				$values = array();
				// iterate through data rows of the table file
				foreach ($file as $index => $line) {
					if (!$index) {
						$columns = str_getcsv($line, ',', '"');		
					}
					else {
						if (!strlen(trim($line))) {
							// skip empty line
							continue;
						}
						
						if (!count($columns)) {
							echo "ERROR: columns must be defined in first row of $tbl_file\n";
							break;
						}
						
						$record = str_getcsv($line, ',', '"');
						$data = array();
						foreach($columns as $i => $col) {
							$data[$i] = $record[$i];
						}
						$this->insert($table, $columns, $data);
						$row_count++;
					
					}
				}
				echo "imported $row_count records, done.\n";
			}
		}
	}

	protected function insert($table, $raw_columns, $raw_data) {
		$db = Yii::app()->db;
		$insert_cols = array();
		$data = array();
		
		$imp_id = null;
		// iterate through columns and map data where necessary
		foreach ($raw_columns as $i => $column) {
			if ($column == 'imp_id') {
				$imp_id = $raw_data[$i];
			} elseif (preg_match('/^\[(.+)=(([^.]+)\.(.+))\]$/',$column, $matches)) {
				$insert_cols[] = $db->quoteColumnName($matches[1]);
				if ($matches[4] == 'imp_id') {
					// we are mapping to a row already done in this import, need to get the value or error if not available
					if (!array_key_exists($matches[3], $this->imp_id_map)) {
						echo "ERROR: haven't set import ids for $matches[3] - is your import order correct?\n";
						exit;
					}
					
					if (!(isset($this->imp_id_map[$matches[3]][$raw_data[$i]]) ) ) {
						echo "ERROR: cannot find import id $raw_data[$i] for $matches[2]\n";
						exit;
					}
					
					$data[] = $this->imp_id_map[$matches[3]][$raw_data[$i]];
				
				} else {
					// the value should already exist in the db and we go look for it
					$data[] = $this->getTableVal($matches[2], $raw_data[$i]);
				}
			} else {
				$insert_cols[] = $db->quoteColumnName($column);
				$data[] = $raw_data[$i];
			}
		}
		
		// escape data values
		foreach ($data as &$field) {
			if ($field != 'NULL') {
				$field = $db->quoteValue($field);
			}
		}
		
		// create the query
		$insert = '('.implode(',', $data).')';
		
		$query = "INSERT INTO ".$db->quoteTableName($table)." (".implode(',',$insert_cols).") VALUES ".$insert;
		//echo $query . "\n";
		
		$db->createCommand($query)->execute();
		if ($imp_id) {
			$this->imp_id_map[$table][$imp_id] = $db->getLastInsertID();
		}
	}
	
	// TODO: handle NULL results appropriately
	protected function _storeTableVal($col_spec, $value) {
		// split the col spec on dot, do select and store
		list($table, $column) = explode(".", $col_spec);
		$db = Yii::app()->db;
		$query = "SELECT id FROM " . $db->quoteTableName($table) . " WHERE " . $db->quoteColumnName($column) . " = "  . $db->quoteValue($value);
		$res =  $db->createCommand($query)->query();
		foreach ($res as $row) {
			// we'll grab the last if there are multiple. 
			$this->column_value_map[$col_spec][$value] = $row['id'];
		}
	}
	
	protected function getTableVal($col_spec, $value) {
		if (!isset($this->column_value_map[$col_spec]) || !isset($this->column_value_map[$col_spec][$value])) {
			$this->_storeTableVal($col_spec, $value);
		}
		
		return $this->column_value_map[$col_spec][$value];
	}
	
}
