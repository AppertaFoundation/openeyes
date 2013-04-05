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

class CancellationReasonTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'reasons' => 'CancellationReason'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('text' => 'new'), 1, array('reason1')),
			array(array('parent_id' => 2), 1, array('reason3')),
			array(array('list_no' => 1), 2, array('reason2', 'reason3')),
			array(array('parent_id' => 1), 0, array()),
			array(array('text' => 'random'), 0, array()),
		);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new CancellationReason;
	}
	
	public function testModel()
	{
		$this->assertEquals('CancellationReason', get_class(CancellationReason::model()), 'Class name should match model.');
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'text' => 'Text',
			'parent_id' => 'Parent',
			'list_no' => 'List No',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$reason = new CancellationReason;
		$reason->setAttributes($searchTerms);
		$results = $reason->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->reasons($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}
	
	public function testGetReasonsByListNumber_NoListNumber_ReturnsCorrectData()
	{
		$reason = $this->reasons['reason1'];
		$expected = array($reason['id'] => $reason['text']);
		
		$this->assertEquals($expected, $this->model->getReasonsByListNumber(), 'Data returned should match.');
	}
	
	public function dataProvider_ReasonLists()
	{	
		return array(
			array(1, array('reason2', 'reason3')),
			array(2, array('reason1')),
			array(3, array()),
		);
	}
	
	/**
	 * @dataProvider dataProvider_ReasonLists
	 */
	public function testGetReasonByListNumber_ValidListNumbers_ReturnsCorrectData($listNo, $reasonArray)
	{
		$expected = array();
		foreach ($reasonArray as $reasonKey) {
			$reason = $this->reasons[$reasonKey];
			$expected[$reason['id']] = $reason['text'];
		}
		
		$result = $this->model->getReasonsByListNumber($listNo);
		
		$this->assertEquals($expected, $result, 'Data returned should match.');
	}
}
