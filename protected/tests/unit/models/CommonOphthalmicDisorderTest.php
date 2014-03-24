<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class CommonOphthalmicDisorderTest extends CDbTestCase
{

	public $fixtures = array(
		'firms' => 'Firm',
		'serviceSubspecialtyAssignments' => 'ServiceSubspecialtyAssignment',
		'specialties' => 'Specialty',
		'disorders' => 'CommonOphthalmicDisorder'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('disorder_id' => 1), 1, array('commonOphthalmicDisorder1')),
			array(array('disorder_id' => 2), 1, array('commonOphthalmicDisorder2')),
			array(array('disorder_id' => 3), 1, array('commonOphthalmicDisorder3')),
			array(array('disorder_id' => 4), 0, array()),
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
	protected function setUp()
	{
		parent::setUp();
		$this->model = new CommonOphthalmicDisorder;
	}

	/**
	 * @covers CommonOphthalmicDisorder::model
	 */
	public function testModel()
	{
		$this->assertEquals('CommonOphthalmicDisorder', get_class(CommonOphthalmicDisorder::model()), 'Class name should match model.');
	}

	/**
	 * @covers CommonOphthalmicDisorder::tableName
	 */
	public function testTableName()
	{

		$this->assertEquals('common_ophthalmic_disorder', $this->model->tableName());
	}

	/**
	 * @covers CommonOphthalmicDisorder::rules
	 */
	public function testRules()
	{
		$this->assertTrue($this->disorders('commonOphthalmicDisorder1')->validate());
		$this->assertEmpty($this->disorders('commonOphthalmicDisorder1')->errors);
	}

	/**
	 * @covers CommonOphthalmicDisorder::attributeLabels
	 */
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'disorder_id' => 'Disorder',
			'subspecialty_id' => 'Subspecialty',
		);

		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	public function testGetList_MissingFirm_ThrowsException()
	{
		$this->setExpectedException('CException', 'Firm is required.');
		$this->model->getList(null);
	}

	/**
	 * @covers CommonOphthalmicDisorder::getList
	 * @todo   Implement testGetList().
	 */
	public function testGetList()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$disorder = new CommonOphthalmicDisorder;
		$disorder->setAttributes($searchTerms);
		$results = $disorder->search();
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
