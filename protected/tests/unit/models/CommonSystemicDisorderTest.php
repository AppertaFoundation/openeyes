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

class CommonSystemicDisorderTest extends ActiveRecordTestCase
{
    use MocksSession;

    public $fixtures = array(
        'Disorder',
        'specialties' => 'Specialty',
        'commonsystemicdisorder' => CommonSystemicDisorder::class,
    );

    private $model;

    public function getModel()
    {
        return CommonSystemicDisorder::model();
    }

    public function dataProvider_Search()
    {
        return array(
            array(array('disorder_id' => 5), 1, array('commonSystemicDisorder1')),
            array(array('disorder_id' => 6), 1, array('commonSystemicDisorder2')),
            array(array('disorder_id' => 7), 1, array('commonSystemicDisorder3')),
            array(array('disorder_id' => 1), 0, array()),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new CommonSystemicDisorder();
    }

    /**
     * @covers CommonSystemicDisorder
     */
    public function testModel()
    {
        $this->assertEquals('CommonSystemicDisorder', get_class(CommonSystemicDisorder::model()), 'Class name should match model.');
    }

    /**
     * @covers CommonSystemicDisorder
     */
    public function testTableName()
    {
        $this->assertEquals('common_systemic_disorder', $this->model->tableName());
    }

    /**
     * @covers CommonSystemicDisorder
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->commonsystemicdisorder('commonSystemicDisorder1')->validate());
        $this->assertEmpty($this->commonsystemicdisorder('commonSystemicDisorder1')->errors);
    }

    /**
     * @covers CommonSystemicDisorder
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'disorder_id' => 'Disorder',
        );

        $this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
    }

    /**
     * @covers CommonSystemicDisorder
     */
    public function testGetList()
    {
        $expected = array();
        $this->mockCurrentContext();
        foreach ($this->commonsystemicdisorder as $data) {
            $disorder = Disorder::model()->findByPk($data['disorder_id']);
            $expected[$disorder->id] = $disorder->term;
        }

        $firm = $this->getMockBuilder('Firm')->disableOriginalConstructor()->getMock();
        $this->assertEquals($expected, $this->model->getList($firm), 'List results should match.');
    }

    /**
     * @covers CommonSystemicDisorder
     * @dataProvider dataProvider_Search
     * @param $searchTerms
     * @param $numResults
     * @param $expectedKeys
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $commonsystemicdisorder = new CommonSystemicDisorder();
        $commonsystemicdisorder->setAttributes($searchTerms);
        $results = $commonsystemicdisorder->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->commonsystemicdisorder($key);
            }
        }

        $this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
        $this->assertEquals($expectedResults, $data, 'Results list should match.');
    }
}
