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
			array(array('hos_num' => 12345), 1, array('patient1')),
			array(array('first_name' => 'John'), 2, array('patient1', 'patient2')),
			array(array('hos_num' => 'invalid'), 0, null),
			array(array('first_name' => 'Katherine', 'gender' => 'M'), 0, array()),
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
}
