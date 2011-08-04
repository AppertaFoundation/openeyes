<?php
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
