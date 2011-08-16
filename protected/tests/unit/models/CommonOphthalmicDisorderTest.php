<?php
class CommonOphthalmicDisorderTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'disorders' => 'CommonOphthalmicDisorder'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('disorder_id' => 1), 1, array('commonOphthalmicDisorder1')),
			array(array('disorder_id' => 2), 1, array('commonOphthalmicDisorder2')),
			array(array('disorder_id' => 3), 1, array('commonOphthalmicDisorder3')),
			array(array('disorder_id' => 4), 0, array()),
			array(array('specialty_id' => 1), 3, array('commonOphthalmicDisorder1', 'commonOphthalmicDisorder2', 'commonOphthalmicDisorder3')),
		);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new CommonOphthalmicDisorder;
	}
	
	public function testModel()
	{
		$this->assertEquals('CommonOphthalmicDisorder', get_class(CommonOphthalmicDisorder::model()), 'Class name should match model.');
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'disorder_id' => 'Disorder',
			'specialty_id' => 'Specialty',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$disorder = new CommonOphthalmicDisorder;
		$disorder->setAttributes($searchTerms);
		$results = $disorder->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->disorders($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}

	public function testGetSpecialtyOptions()
	{
		$specialties = CHtml::listData(Specialty::model()->findAll(), 'id', 'name');
		$this->assertEquals($specialties, $this->model->getSpecialtyOptions(), 'Correct specialties found.');
		$this->assertEquals(count($this->specialties), count($this->model->getSpecialtyOptions()), 'Correct number of specialties found.');
	}
}
