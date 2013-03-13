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

class CommonSystemicDisorderTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'disorders' => 'CommonSystemicDisorder'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('disorder_id' => 5), 1, array('commonSystemicDisorder1')),
			array(array('disorder_id' => 6), 1, array('commonSystemicDisorder2')),
			array(array('disorder_id' => 7), 1, array('commonSystemicDisorder3')),
			array(array('disorder_id' => 1), 0, array()),
		);
	}

	public function setUp()
	{
		parent::setUp();
		$this->model = new CommonSystemicDisorder;
	}

	public function testModel()
	{
		$this->assertEquals('CommonSystemicDisorder', get_class(CommonSystemicDisorder::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'disorder_id' => 'Disorder',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$disorder = new CommonSystemicDisorder;
		$disorder->setAttributes($searchTerms);
		$results = $disorder->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->disorders($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Results list should match.');
	}

	public function testGetList_ReturnsCorrectResults()
	{
		$expected = array();
		foreach ($this->disorders as $data) {
			$disorder = Disorder::model()->findByPk($data['disorder_id']);
			$expected[$disorder->id] = $disorder->term;
		}

		$this->assertEquals($expected, $this->model->getList(), 'List results should match.');
	}
}
