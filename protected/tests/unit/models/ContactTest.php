<?php
class ContactTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'contacts' => 'Contact',
		'addresses' => 'Address'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('nick_name' => 'Aylward'), 1, array('contact1')),
			array(array('nick_name' => 'Collin'), 1, array('contact2')),
			array(array('nick_name' => 'Allan'), 1, array('contact3')),
			array(array('nick_name' => 'Blah'), 0, array()),
		);
	}
	public function setUp()
	{
		parent::setUp();
		$this->model = new Contact;
	}

	public function testModel()
	{
		$this->assertEquals('Contact', get_class(Contact::model()), 'Class name should match model.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'nick_name' => 'Nick Name',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$contact = new Contact;
		$contact->setAttributes($searchTerms);
		$results = $contact->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->contacts($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}
}
