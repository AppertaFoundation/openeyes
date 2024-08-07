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
class MedicationSetTest extends ActiveRecordTestCase
{
    /**
     * @var MedicationSet
     */
    protected $model;
    public $fixtures = array(
        'drugsets' => 'MedicationSet',
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
        parent::setUp();
        $this->model = new MedicationSet();
    }

    /**
     * @covers MedicationSet
     */
    public function testModel()
    {
        $this->assertEquals('MedicationSet', get_class(MedicationSet::model()), 'Class name should match model.');
    }

    /**
     * @covers MedicationSet
     */
    public function testTableName()
    {
        $this->assertEquals('medication_set', $this->model->tableName());
    }

    /**
     * @covers MedicationSet
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->drugsets('drugset1')->validate());
        $this->assertEmpty($this->drugsets('drugset1')->errors);
    }

    /**
     * @covers MedicationSet
     */
    public function testSearch()
    {
        $this->model->setAttributes($this->drugsets('drugset1')->getAttributes());
        $results = $this->model->search();
        $data = $results->getData();

        $expectedKeys = array('drugset1');
        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->drugsets($key);
            }
        }
        $this->assertEquals(1, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }
}
