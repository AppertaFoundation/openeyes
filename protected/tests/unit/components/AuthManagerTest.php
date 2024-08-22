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

 /**
 * @group sample-data
 */
class AuthManagerTest extends OEDbTestCase
{
    use WithFaker;
    use WithTransactions;

    private AuthManager $authManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->authManager = Yii::app()->getComponent('authManager');
        $this->authManager->registerRuleset('core', $this);
    }

    public function tearDown(): void
    {
        Yii::app()->setComponent('authManager', null);
        parent::tearDown();
    }

    /**
     * @covers AuthManager
     *
     * @test
     */
    public function biz_rule_unknown_rule_set()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown ruleset \'foo\' for business rule \'foo.bar\'');
        $this->authManager->executeBizRule('foo.bar', [], null);
    }

    /**
     * @covers AuthManager
     *
     * @test
     */
    public function biz_rule_undefined_core_rule()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Undefined business rule: \'foo\'');
        $this->authManager->executeBizRule('foo', [], null);
    }

    /**
     * @covers AuthManager
     *
     * @test
     */
    public function biz_rule_undefined_module_rule()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Undefined business rule: \'foo.bar\'');
        $this->authManager->registerRuleset('foo', $this);
        $this->authManager->executeBizRule('foo.bar', [], null);
    }

    /**
     * @covers AuthManager
     * @throws Exception
     *
     * @test
     */
    public function biz_rule_core_rule()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule0', [], null));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     *
     * @test
     */
    public function biz_rule_module_rule()
    {
        $this->authManager->registerRuleset('foo', $this);
        $this->assertTrue($this->authManager->executeBizRule('foo.rule0', [], null));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     *
     * @test
     */
    public function biz_rule_user_id_removed()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule0', array('userId' => 1), null));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     *
     * @test
     */
    public function biz_rule_not_enough_args()
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
     *
     * @test
     */
    public function biz_rule_data_scalar()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule1', [], 'foo'));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     *
     * @test
     */
    public function biz_rule_data_array()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule3', [], ['foo', 'bar']));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     *
     * @test
     */
    public function biz_rule_data_and_param()
    {
        $this->assertTrue($this->authManager->executeBizRule('rule2', ['bar'], 'foo'));
    }

    /**
     * @covers AuthManager
     * @throws Exception
     *
     * @test
     */
    public function biz_rule_param_only()
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

    /**
     * @covers AuthManager
     * @group user-roles
     *
     * @test
     */
    public function has_role_returns_true_when_user_has_role()
    {
        $roles = $this->faker->randomElements($this->authManager->getRoles(), 2);

        $user = User::factory()->withAuthItems([$roles[0]->name])->create();

        $this->assertTrue($this->authManager->hasRole($user->id, $roles[0]->name));
        $this->assertFalse($this->authManager->hasRole($user->id, $roles[1]->name));
    }

    /**
     * @covers AuthManager
     * @group user-roles
     *
     * @test
     */
    public function get_assignable_roles_only_inludes_admin_role_when_user_has_admin_role()
    {
        $all_roles_but_admin = array_filter($this->authManager->getRoles(), function ($role) {
            return $role->name !== AuthManager::ADMIN_ROLE_NAME;
        });

        $non_admin_user = User::factory()->withAuthItems([$this->faker->randomElement($all_roles_but_admin)->name])->create();
        $this->assertNotContains(AuthManager::ADMIN_ROLE_NAME, $this->getRoleNames($this->authManager->getAssignableRoles($non_admin_user->id)));

        $admin_user = User::factory()->withAuthItems([AuthManager::ADMIN_ROLE_NAME])->create();
        $this->assertContains(AuthManager::ADMIN_ROLE_NAME, $this->getRoleNames($this->authManager->getAssignableRoles($admin_user->id)));
    }

    protected function getRoleNames($roles)
    {
        return array_map(function ($role) {
            return $role->name;
        }, $roles);
    }

    /**
     * @covers AuthManager
     * @group user-roles
     *
     * @test
     */
    public function has_role_throws_exception_when_user_id_not_provided_empty_string()
    {
        $role = $this->faker->randomElement($this->authManager->getRoles());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot check if user has role of "' . $role->name . '" when no user supplied.');

        $this->authManager->hasRole("", $role->name);
    }

    /**
     * @covers AuthManager
     * @group user-roles
     *
     * @test
     */
    public function has_role_throws_exception_when_user_id_not_provided_null()
    {
        $role = $this->faker->randomElement($this->authManager->getRoles());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot check if user has role of "' . $role->name . '" when no user supplied.');

        $this->authManager->hasRole(null, $role->name);
    }

    /**
     * @covers AuthManager
     * @group user-roles
     *
     * @test
     */
    public function has_role_throws_exception_when_role_not_provided_empty_string()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot check if user has role when no target role supplied.');

        $this->authManager->hasRole(User::factory()->create()->id, "");
    }

    /**
     * @covers AuthManager
     * @group user-roles
     *
     * @test
     */
    public function has_role_throws_exception_when_role_not_provided_null()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot check if user has role when no target role supplied.');

        $this->authManager->hasRole(User::factory()->create()->id, null);
    }
}
