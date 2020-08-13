<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class MedicationRouteTest extends ActiveRecordTestCase
{
    /**
     * @var MedicationRoute
     */
    protected $model;
    public $fixtures = array(
        'drugroutes' => 'MedicationRoute',
    );

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->model = new MedicationRoute();
    }

    /**
     * @covers MedicationRoute
     */
    public function testModel()
    {
        $this->assertEquals('MedicationRoute', get_class(MedicationRoute::model()), 'Class name should match model.');
    }

    /**
     * @covers MedicationRoute
     */
    public function testTableName()
    {
        $this->assertEquals('medication_route', $this->model->tableName());
    }

    /**
     * @covers MedicationRoute
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->drugroutes('drugroute1')->validate());
        $this->assertEmpty($this->drugroutes('drugroute2')->errors);
    }
}
