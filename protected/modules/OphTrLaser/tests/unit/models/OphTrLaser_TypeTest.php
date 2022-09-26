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
class OphTrLaser_TypeTest extends ActiveRecordTestCase
{
    /**
     * @var OphTrLaser_Site_Laser
     */
    protected $model;
    public $fixtures = array(
        'ophtrlaser_type' => 'OphTrLaser_Type',
    );

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->getFixtureManager()->basePath = Yii::getPathOfAlias('application.modules.ophtrlaser.tests.fixtures');
        parent::setUp();
        $this->model = new OphTrLaser_Type();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        $this->getFixtureManager()->basePath = Yii::getPathOfAlias('application.modules.ophtrlaser.tests.fixtures');
        parent::tearDown();
    }

    /**
     * @covers OphTrLaser_Site_Laser::model
     */
    public function testModel()
    {
        $this->assertEquals('OphTrLaser_Type', get_class($this->model), 'Class name should match model.');
    }

    /**
     * @covers OphTrLaser_Type::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('ophtrlaser_type', $this->model->tableName());
    }

    /**
     * @covers OphTrLaser_Type::defaultScope
     */
    public function testFindActiveLasers()
    {
        $types = $this->ophtrlaser_type('type1')->findAll();

        $this->assertEquals(5, count($types));
        foreach ($types as $type) {
            $this->assertInternalType('string', $type->name);
            $this->assertInternalType('int', (int) $type->id);
            $this->assertGreaterThan(0, strlen($type->name));
        }
    }
}
