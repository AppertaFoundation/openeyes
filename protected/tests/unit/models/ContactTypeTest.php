<?php
class ContactTypeTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'contactTypes' => 'ContactType'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('name' => 'GP'), 0, array()), // gp is ignored b/c of letter_template_type value
			array(array('name' => 'Op'), 2, array('contacttype2', 'contacttype3')),
			array(array('letter_template_only' => 0), 6,
				  array('contacttype2', 'contacttype3', 'contacttype4', 'contacttype5', 'contacttype6',
				  		'contacttype7')),
			array(array('letter_template_only' => 1), 6,
				  array('contacttype2', 'contacttype3', 'contacttype4', 'contacttype5', 'contacttype6',
				  		'contacttype7')), // search only allows letter_template_only value of 0, so it will always return those
		);
	}

	public function setUp()
	{
		parent::setUp();
		$this->model = new ContactType;
	}

	public function testModel()
	{
		$this->assertEquals('ContactType', get_class(ContactType::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'name' => 'Name',
			'letter_template_only' => 'Letter Template Only',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$type = new ContactType;
		$type->setAttributes($searchTerms);
		$results = $type->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->contactTypes($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}

	public function testNoContactTypes()
	{
		$this->assertEquals(8, count(ContactType::model()->findAll()));
	}
}