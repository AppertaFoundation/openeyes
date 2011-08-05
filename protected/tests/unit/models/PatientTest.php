<?php
class PatientTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'patients' => 'Patient',
		'addresses' => 'Address'
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

	// 'first_name', 'last_name', 'dob', 'title', 'city', 'postcode', 'telephone', 'mobile', 'email', 'address1', 'address2'
	public function dataProvider_Pseudo()
	{
		return array(
			array(array('hos_num' => 5550101, 'first_name' => 'Rod', 'last_name' => 'Flange', 'dob' => '1979-09-08', 'title' => 'MR', 'primary_phone' => '0208 1111111', 'address_id' => 1)),
			array(array('hos_num' => 5550101, 'first_name' => 'Jane', 'last_name' => 'Hinge', 'dob' => '2000-01-02', 'title' => 'Ms.', 'primary_phone' => '0207 1111111', 'address_id' => 2)),
			array(array('hos_num' => 5550101, 'first_name' => 'Freddy', 'last_name' => 'Globular', 'dob' => '1943-04-05', 'title' => 'WC', 'primary_phone' => '0845 11111111', 'address_id' => 3)),

		);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new Patient;
	}
	
	public function testModel()
	{
		$this->assertEquals('Patient', get_class(Patient::model()), 'Class name should match model.');
	}
	
	public function testAttributeLabels()
	{
		$expected = array(                                                                                                                         
			'id' => 'ID',
			'pas_key' => 'PAS Key',
			'title' => 'Title',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'dob' => 'Date of Birth',
			'gender' => 'Gender',
			'hos_num' => 'Hospital Number',
			'nhs_num' => 'NHS Number',
			'primary_phone' => 'Primary Phone',
			'address_id' => 'Address'
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
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
	public function testPseudoData_ParamSetOn_RandomizesData($data)
	{
		Yii::app()->params['pseudonymise_patient_details'] = 'yes';
		
		$patient = new Patient;
		$patient->setAttributes($data);
		$patient->save();

		foreach (array('first_name', 'last_name', 'dob', 'title', 'primary_phone') as $field) {
			$this->assertNotNull($patient->$field, "Patient does not have anticipated field testing pseudonymous data [{$field}]");
			// this is the important one - patient data now in the model shouldn't match the source data
			$this->assertNotEquals($patient->$field, $data[$field], 'Data should be pseudonomized (unreliable)');
		}
		// some fields we don't mind being the same
		$this->assertNotNull($patient->hos_num);
		$this->assertEquals($patient->hos_num, $data['hos_num']);
	}
	
	public function testRandomData_ParamSetOff_ReturnsFalse()
	{
		Yii::app()->params['pseudonymise_patient_details'] = 'no';
		
		$attributes = array(
			'hos_num' => 5550101,
			'first_name' => 'Rod',
			'last_name' => 'Flange',
			'dob' => '1979-09-08',
			'title' => 'MR',
			'primary_phone' => '0208 1111111',
			'address_id' => 1);
		
		$patient = new Patient;
		$patient->setAttributes($attributes);
		$patient->save();
		
		$this->assertEquals($attributes['first_name'], $patient->first_name, 'Data should not have changed.');
	}
	
	public function testGetAge_ReturnsCorrectValue()
	{
		$attributes = array(
			'hos_num' => 5550101,
			'first_name' => 'Rod',
			'last_name' => 'Flange',
			'dob' => '1979-09-08',
			'title' => 'MR',
			'primary_phone' => '0208 1111111',
			'address_id' => 1);
		
		$patient = new Patient;
		$patient->setAttributes($attributes);
		$patient->save();
		
		$age = date('Y') - 1979;
		if (date('md') < '0908') {
			$age--; // have not had a birthday yet
		}
		
		$this->assertEquals($age, $patient->getAge());
	}
}
