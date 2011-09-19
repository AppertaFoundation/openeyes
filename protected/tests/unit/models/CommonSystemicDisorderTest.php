<?php
class CommonSystemicDisorderTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'disorders' => 'CommonSystemicDisorder'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('disorder_id' => 5), 1, array('commonSystemicDisorder1')),
			array(array('disorder_id' => 6), 1, array('commonSystemicDisorder2')),
			array(array('disorder_id' => 7), 1, array('commonSystemicDisorder3')),
			array(array('disorder_id' => 1), 0, array()),
		);
	}

	public function setUp()
	{
		parent::setUp();
		$this->model = new CommonSystemicDisorder;
	}

	public function testModel()
	{
		$this->assertEquals('CommonSystemicDisorder', get_class(CommonSystemicDisorder::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'disorder_id' => 'Disorder',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$disorder = new CommonSystemicDisorder;
		$disorder->setAttributes($searchTerms);
		$results = $disorder->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->disorders($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Results list should match.');
	}

	public function testGetList_ReturnsCorrectResults()
	{
		$expected = array();
		foreach ($this->disorders as $data) {
			$disorder = Disorder::model()->findByPk($data['disorder_id']);
			$expected[$disorder->id] = $disorder->term;
		}

		$this->assertEquals($expected, $this->model->getList(), 'List results should match.');
	}
}
