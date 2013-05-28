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

class BookingTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'users' => 'User',
		'firms' => 'Firm',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'eventTypes' => 'EventType',
		'events' => 'Event',
		'procedures' => 'Procedure',
		'services' => 'Service',
		'elements' => 'ElementOperation',
		'operationProcedures' => 'OperationProcedureAssignment',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'sessions' => 'Session',
		'operations' => 'ElementOperation',
		'bookings' => 'Booking',
		'theatres' => 'Theatre',
		'sites' => 'Site',
		'wards' => 'Ward'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('element_operation_id' => 1), 1, array('0')),
		);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new Booking;
	}
	
	public function testModel()
	{
		$this->assertEquals('Booking', get_class(Booking::model()), 'Class name should match model.');
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'element_operation_id' => 'Element Operation',
			'session_id' => 'Session',
			'display_order' => 'Display Order',
			'ward_id' => 'Ward',
			'id' => 'ID',
			'element_operation_id' => 'Element Operation',
			'session_id' => 'Session',
			'display_order' => 'Display Order',
			'ward_id' => 'Ward',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$booking = new Booking;
		$searchTerms['ward_id'] = null; // ignore for search purposes
		$booking->setAttributes($searchTerms);
		$results = $booking->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->bookings($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}
}
