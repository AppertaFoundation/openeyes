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

//Currently failing as BaseElement doesn't have a db table. Suspect that this file needs replacing
class OEMigrationTest extends CDbTestCase
{
	protected $oeMigration;
	protected $fixturePath;
	protected $Consolidation;

	public $fixtures = array(
		'event_type' => 'EventType',
	);

	public function setUp(){
		parent::setUp();
		$this->oeMigration = new OEMigration();
		$this->fixturePath = Yii::getPathOfAlias( 'application.tests.fixtures' );
	}

	public function testInitialiseData()
	{
		$eventTypeResultSet = EventType::model()->findAll('id >= 1000');

		Yii::app()->db->createCommand("delete from episode_summary")->query();
		Yii::app()->db->createCommand("delete from episode_summary_item")->query();
		Yii::app()->db->createCommand("delete from event_type where id >= 1000")->query();

		$this->oeMigration->initialiseData($this->fixturePath,	null, 'oeMigrationData');
		$this->compareFixtureWithResultSet($this->event_type, $eventTypeResultSet);

		EventType::model()->deleteAll('id >= 1000');
	}

	public function testGetMigrationPath(){
		$path = $this->oeMigration->getMigrationPath();
		$this->assertStringEndsWith('migrations', $path );
		$this->oeMigration->setMigrationPath('system');
		$wrongSavePath = $this->oeMigration->getMigrationPath();
		$this->assertNotEquals( $path , $wrongSavePath );
	}

	public function testExportDataCannotWriteFile(){
		$tables = array();
		$this->oeMigration->setMigrationPath('system.config');
		$thisPath = $this->oeMigration->getMigrationPath();
		$thisConsolidation = 'm'.gmdate('ymd_His').'_consolidation';
		$this->setExpectedException('OEMigrationException','Migration folder is not writable/accessible: ' . $thisPath);
		$result = $this->oeMigration->exportData($thisConsolidation , $tables);
	}

	public function testExportDataCannotExportNoTables(){
		$tables = array();
		$thisConsolidation = 'm'.gmdate('ymd_His').'_consolidation';
		$this->setExpectedException('OEMigrationException','No tables to export in the current database');
		$result = $this->oeMigration->exportData($thisConsolidation , $tables);
	}

	public function testExportDataNotCdbTableSchema(){
		$tables = array('just an array, not' => 'CDbTableSchema');
		$thisConsolidation = 'm'.gmdate('ymd_His').'_consolidation';
		$this->setExpectedException('OEMigrationException','Not a CDbTableSchema child class');
		$result = $this->oeMigration->exportData($thisConsolidation , $tables);
	}

	public function testExportData(){
		$tables = Yii::app()->db->schema->getTables();
		$this->Consolidation = 'm'.gmdate('ymd_His').'_consolidation';
		$result = $this->oeMigration->exportData($this->Consolidation, $tables);
		$this->assertInstanceOf('OEMigrationResult', $result);
		$this->assertTrue($result->result);
		$this->assertGreaterThan(0 , count($result->tables ));
		foreach($result->tables as $tableName => $tableTotalRows){
			$this->assertInternalType('string' ,$tableName );
			$this->assertGreaterThan(0 , strlen($tableName ));
			$this->assertInternalType('int' , $tableTotalRows );
		}
	}

	/**
	 * @param $fixture
	 * @param $resultSet
	 * @description generic function to compare fixture data with database result set
	 * @return void
	 */

	private function compareFixtureWithResultSet($fixture, $resultSet){
		$resultArr = array();

		foreach($resultSet as $t)
		{
			$resultArr[$t->id] = $t->attributes;
		}

		$this->assertCount(count($fixture), $resultArr);

		while($thisFixture = array_shift($fixture) ){
			$thisRecord = @array_shift($resultArr);

			//remove ids to compare results
			unset($thisFixture['id']); unset($thisRecord['id']);

			$this->assertCount(0 , array_diff( $thisFixture, $thisRecord ) , 'Somehow the fixture and db record are different, fixture: ' . var_export($thisFixture, true) .
				' this record: ' .	var_export( $thisRecord, true)	);
		}
	}

	public function tearDown(){
		unset($this->oeMigration);

		if ($this->Consolidation) {
			$this->eraseDirectory(getcwd()."/../migrations/data/$this->Consolidation");
			$this->Consolidation = null;
		}
	}

	public function eraseDirectory($path)
	{
		if ($dh = opendir($path)) {
			while ($file = readdir($dh)) {
				if (!preg_match('/^\.\.?$/',$file)) {
					if (is_file($path."/".$file)) {
						unlink($path."/".$file);
					} else {
						$this->eraseDirectory("$path/$file");
					}
				}
			}

			closedir($dh);
			rmdir($path);
		}
	}
}
