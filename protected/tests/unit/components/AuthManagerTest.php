<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class AuthManagerTest extends PHPUnit_Framework_TestCase
{
	private $authManager;

	public function setUp()
	{
		$this->authManager = new AuthManager;
		$this->authManager->registerRuleset('core', $this);
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Unknown ruleset 'foo' for business rule 'foo.bar'
	 */
	public function testUnknownRuleset()
	{
		$this->authManager->executeBizRule('foo.bar', array(), null);
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Undefined business rule: 'foo'
	 */
	public function testUndefinedCoreRule()
	{
		$this->authManager->executeBizRule('foo', array(), null);
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Undefined business rule: 'foo.bar'
	 */
	public function testUndefinedModuleRule()
	{
		$this->authManager->registerRuleset('foo', $this);
		$this->authManager->executeBizRule('foo.bar', array(), null);
	}

	public function testCoreRule()
	{
		$this->assertTrue($this->authManager->executeBizRule('rule0', array(), null));
	}

	public function testModuleRule()
	{
		$this->authManager->registerRuleset('foo', $this);
		$this->assertTrue($this->authManager->executeBizRule('foo.rule0', array(), null));
	}

	public function testUserIdRemoved()
	{
		$this->assertTrue($this->authManager->executeBizRule('rule0', array('userId' => 1), null));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNotEnoughArgs()
	{
		$this->authManager->executeBizRule('rule1', array(), null);
	}

	public function testDataScalar()
	{
		$this->assertTrue($this->authManager->executeBizRule('rule1', array(), 'foo'));
	}

	public function testDataArray()
	{
		$this->assertTrue($this->authManager->executeBizRule('rule2', array(), array('foo', 'bar')));
	}

	public function testDataAndParam()
	{
		$this->assertTrue($this->authManager->executeBizRule('rule2', array('bar'), 'foo'));
	}

	public function rule0()
	{
		return (func_num_args() == 0);
	}

	public function rule1($foo)
	{
		return ($foo === 'foo');
	}

	public function rule2($foo, $bar)
	{
		return ($foo === 'foo' && $bar === 'bar');
	}
}
