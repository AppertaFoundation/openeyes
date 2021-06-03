<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class SsoDefaultRightsTest
 *
 * @covers SsoDefaultRights
 */
class SsoDefaultRightsTest extends ActiveRecordTestCase
{

    public function getModel()
    {
        return SsoDefaultRights::model();
    }

    /**
     * @var SsoDefaultRights
     */
    public $model;
    public $fixtures = array(
        'sso_default_user_rights' => 'SsoDefaultRights',
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->model = SsoDefaultRights::model();
    }

    public function testSaveDefaultRights_noFirms_noRoles()
    {

        $attributes = array(
            'id' => 1,
            'default_enabled' => 1,
            'global_firm_rights' => 1,
            'is_consultant' => 1,
            'is_surgeon' => 1,
        );

        $this->model->saveDefaultRights($attributes);

        $actual = $this->model->findByPk(1);
        $this->assertEquals(1, $actual['default_enabled']);
        $this->assertEquals(1, $actual['global_firm_rights']);
        $this->assertEquals(1, $actual['is_consultant']);
        $this->assertEquals(1, $actual['is_surgeon']);
    }

    public function testSaveDefaultRights_noFirms_setRoles()
    {
        $attributes = array(
            'id' => 1,
            'default_enabled' => 1,
            'global_firm_rights' => 1,
            'is_consultant' => 1,
            'is_surgeon' => 0,
            'sso_default_roles' => array(0 => 'User', 1 => 'Edit'),
        );

        $this->model->saveDefaultRights($attributes);

        $actual = $this->model->findByPk(1);
        $this->assertEquals(1, $actual['default_enabled']);
        $this->assertEquals(1, $actual['global_firm_rights']);
        $this->assertEquals(1, $actual['is_consultant']);
        $this->assertEquals(0, $actual['is_surgeon']);
        $this->assertCount(2, $actual['sso_default_roles']);

        $role_ids = SsoDefaultRoles::model()->findAllByAttributes(['sso_user_id' => 1]);
        $this->assertEquals('User', $role_ids[0]['roles']);
        $this->assertEquals('Edit', $role_ids[1]['roles']);
    }

    public function testSaveDefaultRights_setFirms_noRoles()
    {
        $attributes = array(
            'id' => 1,
            'default_enabled' => 1,
            'global_firm_rights' => 0,
            'is_consultant' => 0,
            'is_surgeon' => 1,
            'sso_default_firms' => array(0 => '1', 1 => '3', 2 => '6'),
        );

        $this->model->saveDefaultRights($attributes);

        $actual = $this->model->findByPk(1);
        $this->assertEquals(1, $actual['default_enabled']);
        $this->assertEquals(0, $actual['global_firm_rights']);
        $this->assertEquals(0, $actual['is_consultant']);
        $this->assertEquals(1, $actual['is_surgeon']);
        $this->assertCount(3, $actual['sso_default_firms']);

        $firm_ids = SsoDefaultFirms::model()->findAllByAttributes(['sso_user_id' => 1]);
        $this->assertEquals(1, $firm_ids[0]['firm_id']);
        $this->assertEquals(3, $firm_ids[1]['firm_id']);
        $this->assertEquals(6, $firm_ids[2]['firm_id']);
    }

    public function testSaveDefaultRights_throwException()
    {
        $attributes = array(
            'id' => 1,
            'default_enabled' => 1,
            'global_firm_rights' => 0,
            'is_consultant' => 0,
            'is_surgeon' => 0,
        );

        $this->expectException(FirmSaveException::class);
        $this->model->saveDefaultRights($attributes);
    }
}
