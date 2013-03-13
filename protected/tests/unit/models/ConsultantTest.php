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

class ConsultantTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'consultants' => 'Consultant',
		'contacts' => 'Contact'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('contact_id' => 1), 1, array('consultant1')),
			array(array('contact_id' => 2), 1, array('consultant2')),
			array(array('contact_id' => 3), 1, array('consultant3')),
			array(array('contact_id' => 4), 0, array()),
			array(array('obj_prof' => 'prof1'), 2, array('consultant1', 'consultant2')),
			array(array('obj_prof' => 'prof2'), 1, array('consultant3')),
			array(array('nat_id' => 'uk'), 1, array('consultant1')),
			array(array('nat_id' => 'us'), 1, array('consultant2')),
			array(array('nat_id' => 'can'), 1, array('consultant3')),
		);
	}
	public function setUp()
	{
		parent::setUp();
		$this->model = new Consultant;
	}

	public function testModel()
	{
		$this->assertEquals('Consultant', get_class(Consultant::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'obj_prof' => 'Obj Prof',
			'nat_id' => 'Nat',
			'contact_id' => 'Contact',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$consultant = new Consultant;
		$consultant->setAttributes($searchTerms);
		$results = $consultant->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->consultants($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}
}
