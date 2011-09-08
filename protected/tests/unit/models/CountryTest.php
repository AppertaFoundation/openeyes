<?php
class CountryTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'countries' => 'Country'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('code' => 'US'), 1, array('us')),
			array(array('code' => 'UK'), 1, array('uk')),
			array(array('code' => 'IRE'), 0, array()),
			array(array('name' => 'United'), 2, array('us', 'uk')),
			array(array('name' => 'Can'), 1, array('can')),
			array(array('name' => 'Ireland'), 0, array()),
		);
	}

	public function setUp()
	{
		parent::setUp();
		$this->model = new Country;
	}

	public function testModel()
	{
		$this->assertEquals('Country', get_class(Country::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'code' => 'Code',
			'name' => 'Name',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$country = new Country;
		$country->setAttributes($searchTerms);
		$results = $country->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->countries($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}
}
