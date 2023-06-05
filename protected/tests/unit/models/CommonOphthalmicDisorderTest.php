<?php

/**
 * OpenEyes.
 *
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@apperta.org>
 * @copyright Copyright (c) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CommonOphthalmicDisorderTest extends ActiveRecordTestCase
{
    use MocksSession;
    use \WithFaker;

    private CommonOphthalmicDisorder $model;

    public $fixtures = array(
        'firms' => 'Firm',
        'specialties' => 'Specialty',
        'subspecialties' => 'Subspecialty',
        'serviceSubspecialtyAssignments' => 'ServiceSubspecialtyAssignment',
        'actual_disorders' => 'Disorder',
        'findings' => 'Finding',
        'disorders' => 'CommonOphthalmicDisorder'
    );

    public function getModel()
    {
        return CommonOphthalmicDisorder::model();
    }

    public function dataProvider_Search()
    {
        return array(
            array(array('disorder_id' => 1), 1, array('commonOphthalmicDisorder1')),
            array(array('disorder_id' => 2), 1, array('commonOphthalmicDisorder2')),
            array(array('disorder_id' => 3), 1, array('commonOphthalmicDisorder3')),
            array(array('disorder_id' => 6), 0, array()),
            array(array('subspecialty_id' => 1), 2, array('commonOphthalmicDisorder1', 'commonOphthalmicDisorder2')),
        );
    }

    public function dataProvider_List()
    {
        return array(
            array(1, array('commonOphthalmicDisorder1', 'commonOphthalmicDisorder2'))
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new CommonOphthalmicDisorder();
        $this->mockCurrentContext();
        $user = User::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $this->mockCurrentUser($user);
    }

    /**
     * @covers CommonOphthalmicDisorder
     */
    public function testModel()
    {
        $this->assertEquals('CommonOphthalmicDisorder', get_class(CommonOphthalmicDisorder::model()), 'Class name should match model.');
    }

    /**
     * @covers CommonOphthalmicDisorder
     */
    public function testTableName()
    {

        $this->assertEquals('common_ophthalmic_disorder', $this->model->tableName());
    }

    /**
     * @covers CommonOphthalmicDisorder
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->disorders('commonOphthalmicDisorder1')->validate());
        $this->assertEmpty($this->disorders('commonOphthalmicDisorder1')->errors);
    }

    /**
     * @covers CommonOphthalmicDisorder
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'disorder_id' => 'Disorder',
            'subspecialty_id' => 'Subspecialty',
            'finding_id' => 'Finding',
            'alternate_disorder_id' => 'Alternate Disorder',
            'group_id' => 'Group',
        );

        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @covers CommonOphthalmicDisorder
     * @throws CException
     */
    public function testGetList_MissingFirm_ThrowsException()
    {
        $this->expectException('Exception');
        $this->model->getList(null);
    }

    public function dataProvider_getList()
    {
        return array(
            array('firm2', false, 2),
            array('firm2', true, 4)
        );
    }

    public function dataProvider_getListSecondaryTo()
    {
        return array(
            array('firm2', 4),
        );
    }

    /**
     * @covers CommonOphthalmicDisorder
     * @dataProvider dataProvider_getList
     * @param $firmkey
     * @param $get_findings
     * @param $result_count
     * @throws CException
     */
    public function testGetList($firmkey, $get_findings, $result_count)
    {
        $res = CommonOphthalmicDisorder::getList($this->firms($firmkey), $get_findings);
        $this->assertCount($result_count, $res);
    }

    /**
     * @covers CommonOphthalmicDisorder
     * @dataProvider dataProvider_Search
     * @param $searchTerms
     * @param $numResults
     * @param $expectedKeys
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $disorder = new CommonOphthalmicDisorder('search');
        $disorder->setAttributes($searchTerms);
        $results = $disorder->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->disorders($key);
            }
        }

        $this->assertEquals($numResults, $results->totalItemCount);
        $this->assertEquals($expectedResults, $data);
    }

    /**
     * @covers CommonOphthalmicDisorder
     * @dataProvider dataProvider_getListSecondaryTo
     * @param $firmkey
     * @param $result_count
     * @throws CException
     */
    public function testgetListWithSecondaryTo($firmkey, $result_count)
    {
        $res = CommonOphthalmicDisorder::getListByGroupWithSecondaryTo($this->firms($firmkey));
        $this->assertCount($result_count, $res);
    }
}
