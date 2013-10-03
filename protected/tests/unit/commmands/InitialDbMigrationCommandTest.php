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
class InitialDbMigrationCommandTest extends CTestCase
{
	protected $initialDbMigrationCommand;

	/*public $fixtures = array(
		'users' => 'User',
		'contact' => 'Contact',
		'site' => 'Site',
		'firm' => 'Firm',
	);*/

	public function setUp(){
		$this->initialDbMigrationCommand = new InitialDbMigrationCommand('initialdbmigration', null);
		$this->fileNameRegEx = '|^m\d{6}_\d{6}_[a-z]*$|i';
	}

	public function testRunSuccessful()
	{
		$initDbMigrationResult = $this->initialDbMigrationCommand->run();
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
		$this->assertContains('safeDown', $thisMigrationClassMethods);
	}

	/**
	 *  InitialDbMigrationCommandException
	 */
	public function testRunMigrationFolderNotAccessible(){
		$this->setExpectedException('InitialDbMigrationCommandException','Migration folder is not writable/accessible');
		$this->initialDbMigrationCommand->setMigrationPath('/root');
		$this->initialDbMigrationCommand->run();
		$this->initialDbMigrationCommand->setMigrationPath();
	}

	public function testRunMigrationNoTables(){
		$mockSchema  = $this->getMockBuilder('CMysqlSchema')
			->disableOriginalConstructor()
			->getMock();

		$mockSchema->expects( $this->any() )->method('getTableNames')->will($this->returnValue(array()));
		$mockSchema->expects( $this->any() )->method('loadTable')->will($this->returnValue(null));
		$this->initialDbMigrationCommand->setDbSchema($mockSchema);

		$this->setExpectedException('InitialDbMigrationCommandException','No tables to export in the current database');
		$this->initialDbMigrationCommand->run();
	}
	/**
	 * @description test the getTemplate returns an object that can be stringyfied
	 * into a representation of a migration file to be dymamically filled
	 */
	public function testGetTemplate(){
		$expected = <<<'EOD'
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
		$this->assertEquals($expected, $this->initialDbMigrationCommand->getTemplate(), 'Template was not returned correctly');
	}

	public function testGetMigrationPath(){
		$path = $this->initialDbMigrationCommand->getMigrationPath();
		$this->assertStringEndsWith('migrations', $path );
		$this->initialDbMigrationCommand->setMigrationPath('system');
		$wrongSavePath = $this->initialDbMigrationCommand->getMigrationPath();
		$this->assertNotEquals( $path , $wrongSavePath );
	}

	public function tearDown(){
		unset($this->initialDbMigrationCommand);
	}

}

