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
        $this->authManager->executeBizRule('foo.bar', [], null);
    }

    /**
     * @covers AuthManager
     */
    public function testUndefinedCoreRule()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Undefined business rule: \'foo\'');
        $this->authManager->executeBizRule('foo', [], null);
    }

    /**
     * @covers AuthManager
     */
    public function testUndefinedModuleRule()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Undefined business rule: \'foo.bar\'');
        $this->authManager->registerRuleset('foo', $this);
        $this->authManager->executeBizRule('foo.bar', [], null);
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testCoreRule()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule0', [], null));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testModuleRule()
    {
        $this->authManager->registerRuleset('foo', $this);
        $this->assertTrue($this->authManager->executeBizRule('foo.rule0', [], null));
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
            $this->authManager->executeBizRule('rule2', [], null);
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
        $this->assertTrue($this->authManager->executeBizRule('rule1', [], 'foo'));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testDataArray()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule3', [], ['foo', 'bar']));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testDataAndParam()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule2', ['bar'], 'foo'));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     */
    public function testParam()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule4', ['foo', 'bar'], null));
    }

    /**
     * @param $data
     * @return bool
     */
    public function rule0($data)
    {
        return func_num_args() === 1;
    }

    /**
     * @param $data
     * @return bool
     */
    public function rule1($data)
    {
        return $data === 'foo';
    }

    /**
     * @param $data
     * @param $param
     * @return bool
     */
    public function rule2($data, $param)
    {
        return $data === 'foo' && $param === 'bar';
    }

    /**
     * @param $data_array array
     * @return bool
     */
    public function rule3($data_array)
    {
        return count($data_array) === 2 && $data_array[0] === 'foo' && $data_array[1] === 'bar';
    }

    /**
     * @param $data
     * @param $foo_param
     * @param $bar_param
     * @return bool
     */
    public function rule4($data, $foo_param, $bar_param)
    {
        return func_num_args() === 3 && $foo_param === 'foo' && $bar_param === 'bar';
    }
}
