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
 * Class SsoDefaultFirmsTest
 *
 * @covers SsoDefaultFirms
 */
class SsoDefaultFirmsTest extends ActiveRecordTestCase
{

    public $model;
    public $fixtures = array(
      'ssofirms' => 'SsoDefaultFirms',
    );

    public function getModel()
    {
        return SsoDefaultFirms::model();
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new SsoDefaultFirms();
    }

    /**
     * @covers SsoDefaultFirms::model
     */
    public function testModel()
    {
        $this->assertEquals('SsoDefaultFirms', get_class(SsoDefaultFirms::model()), 'Class name should match model.');
    }

    /**
     * @covers SsoDefaultFirms::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('sso_default_user_firms', $this->model->tableName());
    }

    /**
     * @covers SsoDefaultFirms::primaryKey
     */
    public function testPrimaryKey()
    {
        $this->assertEquals('firm_id', $this->model->primaryKey());
    }
}
