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

class CancelledOperationTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'users' => 'User',
		'operations' => 'CancelledOperation',
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('element_operation_id' => 1), 1, array('operation1')),
			array(array('cancelled_date' => date('Y-m-d')), 2, array('operation1','operation2')),
			array(array('created_user_id' => 1), 1, array('operation1')),
			array(array('created_user_id' => 2), 1, array('operation2')),
			array(array('created_user_id' => 3), 0, array()),
		);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new CancelledOperation;
	}
	
	public function testModel()
	{
		$this->assertEquals('CancelledOperation', get_class(CancelledOperation::model()), 'Class name should match model.');
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'element_operation_id' => 'Element Operation',
			'cancelled_date' => 'Cancelled Date',
			'created_user_id' => 'User',
			'cancelled_reason_id' => 'Cancelled Reason',
			'cancellation_comment' => 'Cancellation comment',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$operation = new CancelledOperation;
		$operation->setAttributes($searchTerms);
		$results = $operation->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->operations($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}
}
