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

class ContactTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'contacts' => 'Contact',
		'addresses' => 'Address'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('nick_name' => 'Aylward'), 1, array('contact1')),
			array(array('nick_name' => 'Collin'), 1, array('contact2')),
			array(array('nick_name' => 'Allan'), 1, array('contact3')),
			array(array('nick_name' => 'Blah'), 0, array()),
		);
	}
	public function setUp()
	{
		parent::setUp();
		$this->model = new Contact;
	}

	public function testModel()
	{
		$this->assertEquals('Contact', get_class(Contact::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'nick_name' => 'Nick Name',
 			'primary_phone' => 'Primary Phone Number',
 			'title' => 'Title',
 			'first_name' => 'First Name',
 			'last_name' => 'Last Name',
 			'qualifications' => 'Qualifications',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$contact = new Contact;
		$contact->setAttributes($searchTerms);
		$results = $contact->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->contacts($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}
}
