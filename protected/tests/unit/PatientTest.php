<?php
class PatientTest extends CDbTestCase
{
	public $fixtures = array(
		'patients' => 'Patient'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('first_name' => 'Katherine'), 1, array('patient3')),
			array(array('last_name' => 'jones'), 1, array('patient2')),  /* case insensitivity test */
			array(array('hos_num' => 12345), 1, array('patient1')),
			array(array('first_name' => 'John'), 2, array('patient1', 'patient2')),
			array(array('hos_num' => 'invalid'), 0, null),
			array(array('first_name' => 'Katherine', 'gender' => 'M'), 0, array()),
		);
	}

	# 'first_name', 'last_name', 'dob', 'title', 'city', 'postcode', 'telephone', 'mobile', 'email', 'address1', 'address2'
	public function dataProvider_Pseudo()
	{
		return array(
			array(array('hos_num' => 5550101, 'first_name' => 'Rod', 'last_name' => 'Flange', 'dob' => '1979-09-08', 'title' => 'MR', 'city' => 'London', 'postcode' => 'EC1V 0DX', 'telephone' => '0208 1111111', 'mobile' => '0208 0101010101', 'email' => 'me@my.com', 'address1' => '1 Fish Plaice', 'address2' => 'Fishington')),
			array(array('hos_num' => 5550101, 'first_name' => 'Jane', 'last_name' => 'Hinge', 'dob' => '2000-01-02', 'title' => 'Ms.', 'city' => 'Hull', 'postcode' => 'NW10 9LN', 'telephone' => '0207 1111111', 'mobile' => '0208 0202020202', 'email' => 'me@their.com', 'address1' => '32 Chip Crescent', 'address2' => 'Mashley')),
			array(array('hos_num' => 5550101, 'first_name' => 'Freddy', 'last_name' => 'Globular', 'dob' => '1943-04-05', 'title' => 'WC', 'city' => 'Rome', 'postcode' => 'AR1 2AR', 'telephone' => '0845 11111111', 'mobile' => '0208 03030303', 'email' => 'me@our.com', 'address1' => '104 Pea Avenue', 'address2' => 'Peasley')),

		);
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$patient = new Patient;
		$patient->setAttributes($searchTerms);
		$results = $patient->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->patients($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}


	/**
	 * @dataProvider dataProvider_Pseudo
	 */
	public function testPseudoData($data)
	{
		$patient = new Patient;
		$patient->setAttributes($data);
		$patient->save();

		foreach (array('first_name', 'last_name', 'dob', 'title', 'city', 'postcode', 'telephone', 'mobile', 'email', 'address1', 'address2') as $field) {
			$this->assertNotNull($patient->$field, 'Patient does not have anticipated field testing pseudonymous data');
			// this is the important one - patient data now in the model shouldn't match the source data
			$this->assertNotEquals($patient->$field, $data[$field]);
		}
		// some fields we don't mind being the same
		$this->assertNotNull($patient->hos_num);
		$this->assertEquals($patient->hos_num, $data['hos_num']);
	}
}
