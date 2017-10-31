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
class DrugFrequencyTest extends CDbTestCase
{
    /**
     * @var DrugFrequency
     */
    protected $model;
    public $fixtures = array(
        'drugfrequencies' => 'DrugFrequency',
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->model = new DrugFrequency();
    }

    /**
     * @covers DrugForm::model
     */
    public function testModel()
    {
        $this->assertEquals('DrugFrequency', get_class(DrugFrequency::model()), 'Class name should match model.');
    }

    /**
     * @covers DrugForm::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('drug_frequency', $this->model->tableName());
    }

    /**
     * @covers DrugForm::rules
     */
    public function testRules()
    {
        $this->assertTrue($this->drugfrequencies('drugfrequency1')->validate());
        $this->assertEmpty($this->drugfrequencies('drugfrequency2')->errors);
    }

    /**
     * @covers DrugFrequency::search
     */
    public function testSearch()
    {
        $this->model->setAttributes($this->drugfrequencies('drugfrequency1')->getAttributes());
        $results = $this->model->search();
        $data = $results->getData();

        $expectedKeys = array('drugfrequency1');
        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->drugfrequencies($key);
            }
        }
        $this->assertEquals(1, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }
}
