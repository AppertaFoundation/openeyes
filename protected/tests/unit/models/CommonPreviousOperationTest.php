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
class CommonPreviousOperationTest extends ActiveRecordTestCase
{
    /**
     * @var CommonPreviousOperation
     */
    public $model;
    public $fixtures = array(
        'commonpreviousops' => 'CommonPreviousOperation',
    );

    public function getModel()
    {
        return CommonPreviousOperation::model();
    }

    public function dataProvider_Search()
    {
        return array(
            array(array('id' => 1, 'name' => 'commonpreviousop 1'), 1, array('commonpreviousop1')),
            array(array('id' => 2, 'name' => 'commonpreviousop 2'), 1, array('commonpreviousop2')),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new CommonPreviousOperation();
    }

    /**
     * @covers CommonPreviousOperation
     */
    public function testModel()
    {
        $this->assertEquals('CommonPreviousOperation', get_class(CommonPreviousOperation::model()), 'Class name should match model.');
    }

    /**
     * @covers CommonPreviousOperation
     */
    public function testTableName()
    {
        $this->assertEquals('common_previous_operation', $this->model->tableName());
    }

    /**
     * @covers CommonPreviousOperation
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->commonpreviousops('commonpreviousop1')->validate());
        $this->assertEmpty($this->commonpreviousops('commonpreviousop1')->errors);
    }

    /**
     * @covers CommonPreviousOperation
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'name' => 'Name',
        );

        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @covers       CommonPreviousOperation
     * @dataProvider dataProvider_Search
     * @param $searchTerms
     * @param $numResults
     * @param $expectedKeys
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $commonpreviousop = new CommonPreviousOperation();
        $commonpreviousop->setAttributes($searchTerms);
        $commonpreviousopresults = $commonpreviousop->search();
        $commonpreviousopdata = $commonpreviousopresults->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->commonpreviousops($key);
            }
        }

        $this->assertEquals($numResults, $commonpreviousopresults->getItemCount());
        $this->assertEquals($expectedResults, $commonpreviousopdata);
    }
}
