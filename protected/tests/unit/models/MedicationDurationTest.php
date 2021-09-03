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
class MedicationDurationTest extends ActiveRecordTestCase
{
    /**
     * @var MedicationDuration
     */
    public $model;
    public $fixtures = array(
        'drugduration' => 'MedicationDuration',
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
        $this->model = new MedicationDuration();
    }

    /**
     * @covers MedicationDuration
     */
    public function testModel()
    {
        $this->assertEquals('MedicationDuration', get_class(MedicationDuration::model()), 'Class name should match model.');
    }

    /**
     * @covers MedicationDuration
     */
    public function testTableName()
    {
        $this->assertEquals('medication_duration', $this->model->tableName());
    }

    /**
     * @covers MedicationDuration
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->drugduration('drugduration1')->validate());
        $this->assertEmpty($this->drugduration('drugduration1')->errors);
    }

    /**
     * @covers MedicationDuration
     */
    public function testSearch()
    {
        $this->model->setAttributes($this->drugduration('drugduration1')->getAttributes());
        $results = $this->model->search();
        $data = $results->getData();

        $expectedKeys = array('drugduration1');
        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->drugduration($key);
            }
        }
        $this->assertEquals(1, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }
}
