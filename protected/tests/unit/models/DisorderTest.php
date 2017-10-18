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
class DisorderTest extends CDbTestCase
{
    public $fixtures = array(
        'disorders' => 'Disorder',
    );

    /**
     * @var Disorder
     */
    protected $model;

    public function dataProvider_Search()
    {
        return array(
            array(array('term' => 'Myopia'), 1, array('disorder1')),
            array(array('term' => 'foobar'), 0, array()),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->model = new Disorder();
    }

    /**
     * @covers Disorder::model
     */
    public function testModel()
    {
        $this->assertEquals('Disorder', get_class(Disorder::model()), 'Class name should match model.');
    }

    /**
     * @covers Disorder::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('disorder', $this->model->tableName());
    }

    /**
     * @covers Disorder::rules
     */
    public function testRules()
    {
        $this->assertTrue($this->disorders('disorder1')->validate());
        $this->assertEmpty($this->disorders('disorder1')->errors);
    }

    /**
     * @covers Disorder::getDisorderOptions
     *
     * @todo   Implement testGetDisorderOptions().
     */
    public function testGetDisorderOptions()
    {
        $expected = array('Myopia');

        $result = $this->disorders('disorder1')->GetDisorderOptions('Myopia');

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProvider_Search
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $this->model->setAttributes($searchTerms);
        $results = $this->model->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->disorders($key);
            }
        }

        $this->assertEquals($numResults, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }
}
