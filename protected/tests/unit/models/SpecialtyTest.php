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
class SpecialtyTest extends CDbTestCase
{
    public $fixtures = array(
        'specialties' => 'Specialty',
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->model = new Specialty();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Specialty::model
     *
     * @todo   Implement testModel().
     */
    public function testModel()
    {
        $this->assertEquals('Specialty', get_class(Specialty::model()), 'Class name should match model.');
    }

    /**
     * @covers Specialty::tableName
     *
     * @todo   Implement testTableName().
     */
    public function testTableName()
    {
        $this->assertEquals('specialty', $this->model->tableName());
    }

    /**
     * @covers Specialty::rules
     *
     * @todo   Implement testRules().
     */
    public function testRules()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Specialty::relations
     *
     * @todo   Implement testRelations().
     */
    public function testRelations()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Specialty::attributeLabels
     *
     * @todo   Implement testAttributeLabels().
     */
    public function testAttributeLabels()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Specialty::search
     *
     * @todo   Implement testSearch().
     */
    public function testSearch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Specialty::getMedical
     *
     * @todo   Implement testGetMedical().
     */
    public function testGetMedical()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Specialty::getSpecialtyOptions
     *
     * @todo   Implement testGetSpecialtyOptions().
     */
    public function testGetSpecialtyOptions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
