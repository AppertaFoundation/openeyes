<?php
class AddressTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'patients' => 'Patient',
		'addresses' => 'Address'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('address1' => 'flat 1'), 1, array('address1')),
			array(array('address1' => 'FLAT 1'), 1, array('address1')), /* case insensitivity test */
			array(array('address2' => 'bleakley'), 3, array('address1', 'address2', 'address3')),
			array(array('city' => 'flitchley'), 3, array('address1', 'address2', 'address3')),
			array(array('postcode' => 'ec1v'), 3, array('address1', 'address2', 'address3')),
			array(array('county' => 'london'), 3, array('address1', 'address2', 'address3')),
			array(array('email' => 'bleakley1'), 1, array('address1')),
			array(array('email' => 'foobar'), 0, array()),
		);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new Address;
	}
	
	public function testModel()
	{
		$this->assertEquals('Address', get_class(Address::model()), 'Class name should match model.');
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'address1' => 'Address1',
			'address2' => 'Address2',
			'city' => 'City',
			'postcode' => 'Postcode',
			'county' => 'County',
			'country_id' => 'Country',
			'email' => 'Email',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$address = new Address;
		$address->setAttributes($searchTerms);
		$results = $address->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->addresses($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}
}
