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

class OEMigration extends CDbMigration {

	/**
	 * Initialise tables with default data
	 * Filenames must to be in the format "nn_tablename.csv", where nn is the processing order
	 */
	protected function initialiseData($migrations_path, $update_pk = null) {
		$data_path = $migrations_path.'/data/'.get_class($this).'/';
		foreach(glob($data_path."*.csv") as $file_path) {
			$table = substr(substr(basename($file_path), 0, -4), 3);
			echo "Importing $table data...\n";
			$fh = fopen($file_path, 'r');
			$columns = fgetcsv($fh);
			$lookup_columns = array();
			foreach($columns as &$column) {
				if(strpos($column, '=>') !== false) {
					$column_parts = explode('=>',$column);
					$column = trim($column_parts[0]);
					$lookup_parts = explode('.',$column_parts[1]);
					$model = trim($lookup_parts[0]);
					$field = trim($lookup_parts[1]);
					$lookup_columns[$column] = array('model' => $model, 'field' => $field);
				}
			}
			$row_count = 0;
			$values = array();
			while(($record = fgetcsv($fh)) !== false) {
				$row_count++;
				$data = array_combine($columns, $record);

				// Process lookup columns
				foreach($lookup_columns as $lookup_column => $lookup) {
					$model = $lookup['model'];
					$field = $lookup['field'];
					$lookup_value = $data[$lookup_column];
					$lookup_record = BaseActiveRecord::model($model)->findByAttributes(array($field => $lookup_value));
					$data[$lookup_column] = $lookup_record->id;
				}

				// Process NULLs
				foreach($data as &$value) {
					if($value == 'NULL') {
						$value = null;
					}
				}

				if($update_pk) {
					$pk = $data[$update_pk];
					$existing = $this->getDbConnection()->createCommand()
					->select($update_pk)
					->from($table)
					->where($update_pk.' = ?')
					->queryScalar(array($pk));
					if($existing) {
						$this->update($table, $data, $update_pk . '= :pk', array(':pk' => $pk));
					} else {
						$this->insert($table, $data);
					}
				} else {
					$this->insert($table, $data);
				}
			}
			fclose($fh);
			echo "$row_count records, done.\n";
		}
	}

}