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

class InitialDbMigrationCommand extends MigrateCommand
{
	public $migrationPath = 'application.migrations';
	private $dbSchema;

	public function run($args = null) {
		$className = 'consolidation';
		$migrPath = $this->getMigrationPath() ;
		$tables = $this->getDbSchema()->getTables();
		$initialDbMigrationResult = new InitialDbMigrationResult();

		if(!is_writable($migrPath))
			throw new InitialDbMigrationCommandException('Migration folder is not writable/accessible');

		if(!is_array($tables) || count($tables)==0)
			throw new InitialDbMigrationCommandException('No tables to export in the current database');

		$template = $this->getTemplate();
		$initialDbMigrationResult->fileName = $this->getMigrationFileName($className);

		$migrateUp = $this->getUpCreateTablesStatements($tables);
		$migrateDown = $this->getDownDropTablesStatements($tables);
		$content=strtr($this->getTemplate(), array(
			'{ClassName}'=> $initialDbMigrationResult->fileName,
			'{ClassUp}'=> $migrateUp,
			'{ClassDown}'=> $migrateDown,
		));

		$fileFullPath = $migrPath . DIRECTORY_SEPARATOR . $initialDbMigrationResult->fileName . '.php';
		$writeFile = file_put_contents($fileFullPath, $content);
		if( $writeFile !== false  && is_file($fileFullPath) ){
			$initialDbMigrationResult->result = true;
			echo "New migration created successfully :" . $writeFile .".\n JSON Result: " . json_encode($initialDbMigrationResult ) . "\n";
		}

		return $initialDbMigrationResult;
	}

	private function getUpCreateTablesStatements($tables){
		$addForeignKeys = '';
		$result = "public function up()\n\t\t{\n";
		$result .= '			$this->execute("SET foreign_key_checks = 0");' . "\n";
		foreach ($tables as $table) {
			if( !is_subclass_of($table,'CDbTableSchema'))
				throw new InitialDbMigrationCommandException('Table is not of type CDbTableSchema, instead : ' . get_class( $table));

			$createTable = Yii::app()->db->createCommand('SHOW CREATE TABLE ' . $table->name . ' ;')->queryRow(true);

			if(!isset($createTable["Create Table"]))
				throw new InitialDbMigrationCommandException('Show Create Table errors. $createTable array was : ' . var_export($createTable, true));

			$result .= '			$this->execute("' . $createTable["Create Table"] .  '");' . "\n\n";

		}
		$result .= '			$this->execute("SET foreign_key_checks = 1");' . "\n";
		$result .= "\t}\n\n";
		return $result;
	}

	private function getDownDropTablesStatements($tables){
		$result = "public function down()\n\t\t{\n";
		foreach ($tables as $table) {
			// Add foreign key(s)
			$dropForeignKeys = '';
			foreach ($table->foreignKeys as $col => $fk) {
				// Foreign key naming convention: fk_table_foreignTable_col (max 64 characters)
				$fkName = substr('fk_' . $table->name . '_' . $fk[0] . '_' . $col, 0 , 64);
				$dropForeignKeys .= '			$this->dropForeignKey(' . "'$fkName', '$table->name');\n";
			}
			$result .= $dropForeignKeys;
			$result .= "\n\t\t\t" . '$this->dropTable(\'' . $table->name . '\');' . "\n";
		}
		$result .= "\t\t}\n";
		return $result;
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

	public function getDbSchema(){
		if(!isset($this->dbSchema)){
			$this->dbSchema = Yii::app()->db->schema;
		}
		return $this->dbSchema;
	}

	public function setDbSchema(CDbSchema $schema){
		if(is_null($schema))
			$schema =  Yii::app()->db->schema;
		$this->dbSchema = $schema;
	}

	public function getTemplate(){
		return <<<'EOD'
<?php

	class {ClassName} extends CDbMigration
	{

		{ClassUp}

		{ClassDown}


		// Use safeUp/safeDown to do migration with transaction
		public function safeUp()
		{
			$this->up();
		}

		public function safeDown()
		{
			$this->down();
		}

	}
EOD;
	}

	private function getMigrationFileName($name){
		return $name='m'.gmdate('ymd_His').'_'.$name ;
	}
}