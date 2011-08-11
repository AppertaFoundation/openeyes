<?php
class CancelledBookingTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'users' => 'User',
		'bookings' => 'CancelledBooking',
		'reasons' => 'CancellationReason'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('element_operation_id' => 1), 1, array('booking1')),
			array(array('cancelled_date' => date('Y-m-d', strtotime('-30 days'))), 1, array('booking2')),
			array(array('user_id' => 1), 1, array('booking1')),
			array(array('user_id' => 2), 1, array('booking2')),
			array(array('theatre_id' => 3), 0, array()),
		);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new CancelledBooking;
	}
	
	public function testModel()
	{
		$this->assertEquals('CancelledBooking', get_class(CancelledBooking::model()), 'Class name should match model.');
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'element_operation_id' => 'Element Operation',
			'date' => 'Date',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'theatre_id' => 'Theatre',
			'cancelled_date' => 'Cancelled Date',
			'user_id' => 'User',
			'cancelled_reason_id' => 'Cancelled Reason',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$booking = new CancelledBooking;
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
