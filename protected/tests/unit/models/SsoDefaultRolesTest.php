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
 * Class SsoDefaultRolesTest
 *
 * @covers SsoDefaultRoles
 */
class SsoDefaultRolesTest extends ActiveRecordTestCase
{

    public $model;
    public $fixtures = array(
        'ssoroles' => 'SsoDefaultRoles',
    );

    public function getModel()
    {
        return SsoDefaultRoles::model();
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new SsoDefaultRoles();
    }

    public function testModel()
    {
        $this->assertEquals('SsoDefaultRoles', get_class(SsoDefaultRoles::model()), 'Class name should match model.');
    }

    public function testTableName()
    {
        $this->assertEquals('sso_default_user_roles', $this->model->tableName());
    }

    public function testPrimaryKey()
    {
        $this->assertEquals('roles', $this->model->primaryKey());
    }
}
