<?php
class TheatreWardAssignmentTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'theatres' => 'Theatre',
		'wards' => 'Ward',
		'assignments' => 'TheatreWardAssignment'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('theatre_id' => 1), 1, array('twa1')),
			array(array('ward_id' => 2), 1, array('twa1')),
			array(array('theatre_id' => 2), 1, array('twa2')),
			array(array('ward_id' => 3), 1, array('twa2')),
			array(array('theatre_id' => 3), 0, array()),
			array(array('ward_id' => 1), 0, array()),
			array(array('ward_id' => 4), 0, array()),
			array(array('ward_id' => 5), 0, array()),
		);
	}
	public function setUp()
	{
		parent::setUp();
		$this->model = new TheatreWardAssignment;
	}

	public function testModel()
	{
		$this->assertEquals('TheatreWardAssignment', get_class(TheatreWardAssignment::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'theatre_id' => 'Theatre',
			'ward_id' => 'Ward',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$assignment = new TheatreWardAssignment;
		$assignment->setAttributes($searchTerms);
		$results = $assignment->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->assignments($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}
}
