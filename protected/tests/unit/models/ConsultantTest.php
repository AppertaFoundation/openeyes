<?php
class ConsultantTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'consultants' => 'Consultant',
		'contacts' => 'Contact'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('contact_id' => 1), 1, array('consultant1')),
			array(array('contact_id' => 2), 1, array('consultant2')),
			array(array('contact_id' => 3), 1, array('consultant3')),
			array(array('contact_id' => 4), 0, array()),
			array(array('obj_prof' => 'prof1'), 2, array('consultant1', 'consultant2')),
			array(array('obj_prof' => 'prof2'), 1, array('consultant3')),
			array(array('nat_id' => 'uk'), 1, array('consultant1')),
			array(array('nat_id' => 'us'), 1, array('consultant2')),
			array(array('nat_id' => 'can'), 1, array('consultant3')),
		);
	}
	public function setUp()
	{
		parent::setUp();
		$this->model = new Consultant;
	}

	public function testModel()
	{
		$this->assertEquals('Consultant', get_class(Consultant::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'obj_prof' => 'Obj Prof',
			'nat_id' => 'Nat',
			'contact_id' => 'Contact',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$consultant = new Consultant;
		$consultant->setAttributes($searchTerms);
		$results = $consultant->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->consultants($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}
}
