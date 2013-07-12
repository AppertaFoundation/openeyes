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

class AddressTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'patients' => 'Patient',
		'addresses' => 'Address'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('address1' => 'flat 1'), 1, array('address1')),
			array(array('address1' => 'FLAT 1'), 1, array('address1')), /* case insensitivity test */
			array(array('address2' => 'bleakley'), 3, array('address1', 'address2', 'address3')),
			array(array('city' => 'flitchley'), 3, array('address1', 'address2', 'address3')),
			array(array('postcode' => 'ec1v'), 3, array('address1', 'address2', 'address3')),
			array(array('county' => 'london'), 3, array('address1', 'address2', 'address3')),
			array(array('email' => 'bleakley1'), 1, array('address1')),
			array(array('email' => 'foobar'), 0, array()),
		);
	}

	public function setUp()
	{
		parent::setUp();
		$this->model = new Address;
	}

	public function testModel()
	{
		$this->assertEquals('Address', get_class(Address::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'address1' => 'Address1',
			'address2' => 'Address2',
			'city' => 'City',
			'postcode' => 'Postcode',
			'county' => 'County',
			'country_id' => 'Country',
			'email' => 'Email',
		);

		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$address = new Address;
		$address->setAttributes($searchTerms);
		$results = $address->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->addresses($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}
}
