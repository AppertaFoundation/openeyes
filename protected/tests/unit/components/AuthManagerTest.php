<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class AuthManagerTest extends PHPUnit_Framework_TestCase
{
    private AuthManager $authManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->authManager = new AuthManager();
        $this->authManager->registerRuleset('core', $this);
    }

    /**
     * @covers AuthManager
     */
    public function testUnknownRuleset()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Unknown ruleset \'foo\' for business rule \'foo.bar\'');
        $this->authManager->executeBizRule('foo.bar', array(), null);
    }

    /**
     * @covers AuthManager
     */
    public function testUndefinedCoreRule()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Undefined business rule: \'foo\'');
        $this->authManager->executeBizRule('foo', array(), null);
    }

    /**
     * @covers AuthManager
     */
    public function testUndefinedModuleRule()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Undefined business rule: \'foo.bar\'');
        $this->authManager->registerRuleset('foo', $this);
        $this->authManager->executeBizRule('foo.bar', array(), null);
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testCoreRule()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule0', array(), null));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testModuleRule()
    {
        $this->authManager->registerRuleset('foo', $this);
        $this->assertTrue($this->authManager->executeBizRule('foo.rule0', array(), null));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testUserIdRemoved()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule0', array('userId' => 1), null));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testNotEnoughArgs()
    {
        $this->expectException('ArgumentCountError');
        if (phpversion() > '7.1') {
            $this->authManager->executeBizRule('rule1', array(), null);
        } else {
            $this->markTestSkipped("Test requires PHP7.1 or higher");
        }
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testDataScalar()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule1', array(), 'foo'));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testDataArray()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule2', array(), array('foo', 'bar')));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testDataAndParam()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule2', array('bar'), 'foo'));
    }

    public function rule0()
    {
        return func_num_args() == 0;
    }

    /**
     * @param $foo
     * @return bool
     */
    public function rule1($foo)
    {
        return $foo === 'foo';
    }

    /**
     * @param $foo
     * @param $bar
     * @return bool
     */
    public function rule2($foo, $bar)
    {
        return $foo === 'foo' && $bar === 'bar';
    }
}
