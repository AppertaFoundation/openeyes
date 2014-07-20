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
class CountryTest extends CDbTestCase
{
	public $model;
	public $fixtures = array(
		'countries' => 'Country'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('code' => 'US'), 1, array('us')),
			array(array('code' => 'UK'), 1, array('uk')),
			array(array('code' => 'IRE'), 0, array()),
			array(array('name' => 'United'), 2, array('uk', 'us')),
			array(array('name' => 'Can'), 1, array('can')),
			array(array('name' => 'Ireland'), 0, array()),
		);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->model = new Country;
	}

	/**
	 * @covers Country::model
	 */
	public function testModel()
	{
		$this->assertEquals('Country', get_class(Country::model()), 'Class name should match model.');
	}

	/**
	 * @covers Country::tableName
	 */
	public function testTableName()
	{
		$this->assertEquals('country', $this->model->tableName());
	}

	/**
	 * @covers Country::rules
	 */
	public function testRules()
	{
		$this->assertTrue($this->countries('us')->validate());
		$this->assertEmpty($this->countries('us')->errors);
	}

	/**
	 * @covers Country::attributeLabels
	 */
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'code' => 'Code',
			'name' => 'Name',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$country = new Country;
		$country->setAttributes($searchTerms);
		$results = $country->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->countries($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}
}
