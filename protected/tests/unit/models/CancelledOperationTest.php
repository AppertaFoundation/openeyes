<?php
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
			array(array('user_id' => 1), 1, array('operation1')),
			array(array('user_id' => 2), 1, array('operation2')),
			array(array('user_id' => 3), 0, array()),
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
