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

class OEMigration extends CDbMigration
{
	private $migrationPath;

	/**
	 * Initialise tables with default data
	 * Filenames must to be in the format "nn_tablename.csv", where nn is the processing order
	 */
	public function initialiseData($migrations_path, $update_pk = null, $data_directory = null)
	{
		if (!$data_directory) {
			$data_directory = get_class($this);
		}
		$data_path = $migrations_path.'/data/'.$data_directory.'/';
		foreach (glob($data_path."*.csv") as $file_path) {
			$table = substr(substr(basename($file_path), 0, -4), 3);
			echo "Importing $table data...\n";
			$fh = fopen($file_path, 'r');
			$columns = fgetcsv($fh);
			$lookup_columns = array();
			foreach ($columns as &$column) {
				if (strpos($column, '=>') !== false) {
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
			while (($record = fgetcsv($fh)) !== false) {
				$row_count++;
				$data = array_combine($columns, $record);

				// Process lookup columns
				foreach ($lookup_columns as $lookup_column => $lookup) {
					$model = $lookup['model'];
					$field = $lookup['field'];
					$lookup_value = $data[$lookup_column];
					$lookup_record = BaseActiveRecord::model($model)->findByAttributes(array($field => $lookup_value));
					$data[$lookup_column] = $lookup_record->id;
				}

				// Process NULLs
				foreach ($data as &$value) {
					if ($value == 'NULL') {
						$value = null;
					}
				}

				if ($update_pk) {
					$pk = $data[$update_pk];
					$existing = $this->getDbConnection()->createCommand()
					->select($update_pk)
					->from($table)
					->where($update_pk.' = ?')
					->queryScalar(array($pk));
					if ($existing) {
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

	public function exportData($migrationName , $tables){
		if(!is_writable($this->getMigrationPath()))
			throw new OEMigrationException('Migration folder is not writable/accessible: ' . $this->getMigrationPath());

		if(!is_array($tables) || count($tables)==0)
			throw new OEMigrationException('No tables to export in the current database');

		$migrationResult = new OEMigrationResult();
		$migrationResult->tables = array();
		foreach($tables as $table){
			$migrationResult->tables[@$table->name] =  $this->exportTable($migrationName, $table) ;
		}
		$migrationResult->result = true;
		return $migrationResult;
	}

	public function getMigrationPath(){
		if(!isset($this->migrationPath)){
			$this->migrationPath = 'application.migrations';
		}
		return Yii::getPathOfAlias( $this->migrationPath );
	}

	public function setMigrationPath($path = null){
		if(is_null($path))
			$path =  'application.migrations';
		$this->migrationPath = $path;
	}

	/**
	 * @param $migrationName - name of the migration, a folder with name will be created under data
	 * @param $table - name of the table being exported
	 * @return int - return totRows
	 * @throws OEMigrationException
	 */
	private function exportTable($migrationName, $table){
		if(!is_subclass_of($table, 'CDbTableSchema' ) )
			throw new OEMigrationException('Not a CDbTableSchema child class');

		$dataPath = $this->getMigrationPath(). DIRECTORY_SEPARATOR . 'data';
		//create data folder if does not exist
		if(!file_exists( $dataPath )){
			$dataDirCreated = mkdir($dataPath);
			if(!$dataDirCreated)
				throw new OEMigrationException('Data folder could not be created');
		}
		$dataMigPath = $dataPath. DIRECTORY_SEPARATOR . $migrationName;
		//create data migration folder if does not exist
		if(!file_exists( $dataMigPath )){
			$dataMigDirCreated = mkdir($dataMigPath );
			if(!$dataMigDirCreated)
				throw new OEMigrationException('Data migration folder could not be created');
		}

		$columns = implode(',' ,  $table->getColumnNames());

		$rowsQuery = $this->getDbConnection()->createCommand()
			->select($columns)->from($table->name)->queryAll();

		$data = array();
		$data[] = $table->getColumnNames();
		$data= array_merge($data , $rowsQuery);

		$file = fopen($dataMigPath . DIRECTORY_SEPARATOR . '01_' . $table->name . '.csv',  'w');
		//i dont like manual file opening with no exceptions - might need refactoring later
		foreach($data as $row){
			fputcsv($file , $row);
		}

		fclose($file);
		return  count($rowsQuery);
	}

}
