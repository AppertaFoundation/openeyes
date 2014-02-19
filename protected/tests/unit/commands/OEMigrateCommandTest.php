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
class OEMigrateCommandTest extends CDbTestCase
{
	private $oeMigrationCommand;

	public function setUp(){
		parent::setUp();
		$commandRunnerMock = $this->getMock('CConsoleCommandRunner');
		$this->oeMigrationCommand = new OEMigrateCommand('migrate', $commandRunnerMock);
	}

	public function testGetCliArg(){
		$cliArg = $this->oeMigrationCommand->getCliArg(array());
		$this->assertFalse($cliArg);
		$cliArg = $this->oeMigrationCommand->getCliArg('injectedArg', array(0 => 'some.php', 1 => 'injectedArg'));
		$this->assertTrue($cliArg);
		$this->assertInternalType('boolean' , $cliArg);
		$cliArg = $this->oeMigrationCommand->getCliArg('injectedArg', array(0 => 'some.php', 1 => 'injectedArg=true'));
		$this->assertInternalType('string', $cliArg);
		$this->assertEquals('true', $cliArg);
	}

	/*
	 * public function testActionUp(){
		$this->assertNull($this->oeMigrationCommand->args);
		$this->oeMigrationCommand->actionUp(array(0 => 'commandArg'));
		$this->assertNotNull($this->oeMigrationCommand->args);
	}
	*/

	public function tearDown(){
		unset($this->oeMigrationCommand);
	}
}
