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

	public $fixtures = array(
		'event_group' => 'EventGroup'
	);

	public function setUp(){
		parent::setUp();
		$this->oeMigration = new OEMigration();
		$this->fixturePath = Yii::getPathOfAlias( 'application.tests.fixtures' );
	}

	public function testInitialiseData()
	{
		$eventGroup = new EventGroup();
		$eventGroupResultSet = $eventGroup->findAll();

		$this->compareFixtureWithResultSet($this->event_group, $eventGroupResultSet);




		/*//load info fixture data
		$this->oeMigration->initialiseData();

		$this->assertInstanceOf('InitialDbMigrationResult' , $initDbMigrationResult, 'Not and instance of InitialDbMigrationResult' );
		$this->assertTrue($initDbMigrationResult->result === true);
		$this->assertRegExp($this->fileNameRegEx , $initDbMigrationResult->fileName );
		$thisMigrationFile = $this->initialDbMigrationCommand->getMigrationPath()
			. DIRECTORY_SEPARATOR . $initDbMigrationResult->fileName . '.php';
		$this->assertFileExists($thisMigrationFile);
		include $thisMigrationFile;
		$this->assertTrue(class_exists($initDbMigrationResult->fileName));
		$thisMigrationClassMethods = get_class_methods($initDbMigrationResult->fileName );
		$this->assertContains('up', $thisMigrationClassMethods);
		$this->assertContains('down', $thisMigrationClassMethods);
		$this->assertContains('safeUp', $thisMigrationClassMethods);
		$this->assertContains('safeDown', $thisMigrationClassMethods);*/
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

		foreach($fixture as $k => $v){
			$this->assertTrue(isset($resultArr[$v['id']]));
			$this->assertTrue(isset($resultArr[$v['id']] ['name'] ));
			$this->assertTrue(isset($resultArr[$v['id']] ['code']));
			$this->assertEquals($v['id'], $resultArr[$v['id']]['id']);
			$this->assertEquals($v['name'], $resultArr[$v['id']]['name']);
			$this->assertEquals($v['code'], $resultArr[$v['id']]['code']);
		}
	}

	public function tearDown(){
		unset($this->oeMigration);
	}

}

