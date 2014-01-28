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

/*
 *
How to consolidate

1) make sure you have the latest structure and data files up to the release you are consolidating to, for instance release 1.4.0
	run "./yiic migrate" will update to the latest migration in this release.

2) Run "./yiic initialisedbmigration"
This takes the current db status and data and prepares migration file/data structure that will recreate the database status as it is at that current moment

3) delete all tables and data in the database.

4) delete all previous migration files and old data within the data folder, make sure the new consolidation data and migration files are not removed.

5) run the "./yiic migration" command. It executes the new consolidation migration and restores the database structure and data to the consolidation.

Example : migrated to 130913_000000

For modules consolidation generation there is no command available as automation is more difficult.
But an helper has been created and it is available in the DevTools project https://github.com/openeyes/DevTools

 */

class InitialDbMigrationCommand extends CConsoleCommand
{
	public $migrationPath = 'application.migrations';
	public $oeMigration = null;
	private $dbSchema;

	public function run($args = null) {
		Yii::app()->cache->flush();

		$className = 'consolidation';
		$this->oeMigration = $this->getOeMigration();
		$migrPath = $this->oeMigration->getMigrationPath() ;
		$tables = $this->getDbSchema()->getTables();
		$initialDbMigrationResult = new InitialDbMigrationResult();

		if(!is_writable($migrPath))
			throw new InitialDbMigrationCommandException('Migration folder is not writable/accessible');

		if(!is_array($tables) || count($tables)==0)
			throw new InitialDbMigrationCommandException('No tables to export in the current database');
		//dont export migration table
		unset($tables['tbl_migration']);

		$template = $this->getTemplate();
		$initialDbMigrationResult->fileName = $this->getMigrationFileName($className);

		$migrateCreateTables = $this->getUpCreateTablesStatements($tables);
		$lastMigration = $this->getLatestMigration();
		$content=strtr($this->getTemplate(), array(
			'{LastMigration}' => $lastMigration,
			'{ClassName}'=> $initialDbMigrationResult->fileName,
			'{ClassCreateTables}'=> $migrateCreateTables,
		));

		$fileFullPath = $migrPath . DIRECTORY_SEPARATOR . $initialDbMigrationResult->fileName . '.php';
		$writeFile = file_put_contents($fileFullPath, $content);

		//table structure migration file is generated, lets trigger the data export now
		$initialDbMigrationResult->tables =
			$this->oeMigration->exportData($initialDbMigrationResult->fileName, $tables)->tables;

		if( $writeFile !== false  && is_file($fileFullPath) ){
			$initialDbMigrationResult->result = true;
			//echo "New migration created successfully :" . $writeFile .".\n JSON Result: " . json_encode($initialDbMigrationResult ) . "\n";
		}

		return $initialDbMigrationResult;
	}

	private function getUpCreateTablesStatements($tables){
		$addForeignKeys = '';
		$result = "public function createTables()\n\t\t{\n";
		$result .= '			$this->execute("SET foreign_key_checks = 0");' . "\n";
		foreach ($tables as $table) {
			if( !is_subclass_of($table,'CDbTableSchema'))
				throw new InitialDbMigrationCommandException('Table is not of type CDbTableSchema, instead : ' . get_class( $table));
			//exclude migrations table
			if($table->name == 'tbl_migration'){
				//var_dump($tables);die();
				unset($tables[$table->name]);
				continue;
			}

			$createTable = Yii::app()->db->createCommand('SHOW CREATE TABLE ' . $table->name . ' ;')->queryRow(true);

			if(!isset($createTable["Create Table"]))
				throw new InitialDbMigrationCommandException('Show Create Table errors. $createTable array was : ' . var_export($createTable, true));
			$createTableStm = $createTable["Create Table"];
			$createTableStm = str_replace(array('ENGINE' , "\n"), array("\nENGINE", "\n\t\t\t\t" ), $createTableStm);
			$result .= '			$this->execute("' . $createTableStm .  "\"\n\t\t\t);\n\n";

		}
		$result .= "\t\t\t" . '$this->initialiseData($this->getMigrationPath());' . "\n";
		$result .= '			$this->execute("SET foreign_key_checks = 1");' . "\n";
		$result .= "\t}\n\n";
		return $result;
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

	class {ClassName} extends OEMigration
	{

		public function up(){
			// Check for existing migrations
			$existing_migrations = $this->getDbConnection()->createCommand("SELECT count(version) FROM `tbl_migration`")->queryScalar();
			if ($existing_migrations == 1) {
				$this->createTables();
			} else {
				// Database has existing migrations, so check that last migration step to be consolidated was applied
				$previous_migration = $this->getDbConnection()->createCommand("SELECT * FROM `tbl_migration` WHERE version = '{LastMigration}'")->execute();
				if ($previous_migration) {
					// Previous migration was applied, safe to consolidate
					echo "Consolidating old migration data";
					$this->execute("DELETE FROM `tbl_migration` WHERE version < '{ClassName}'");
				} else {
					// Database is not migrated up to the consolidation point, cannot migrate
					echo "Previous migrations missing or incomplete, migration not possible\n";
					return false;
				}
			}
		}

		{ClassCreateTables}

		public function down()
		{
			echo "{ClassName} does not support migration down.\n";
			return false;
		}


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

	private function getOeMigration(){
		if($this->oeMigration === null ){
			return new OEMigration();
		}
		return $this->oeMigration;
	}

	/**
	 * @return mixed - either the name of the lastest migration or false
	 */
	private function getLatestMigration(){
		//select version from tbl_migration order by 1 desc limit 1
		return $existing_migrations = Yii::app()->db
			->createCommand("SELECT version FROM tbl_migration ORDER BY 1 DESC LIMIT 1")->queryScalar();
	}
}
