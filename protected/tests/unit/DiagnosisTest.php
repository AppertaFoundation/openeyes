<?php

class DiagnosisTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'firms' => 'Firm',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'disorders' => 'Disorder',
		'diagnoses' => 'Diagnosis',
		'commonOphthalmicDisorders' => 'CommonOphthalmicDisorder',
		'commonSystemicDisorders' => 'CommonSystemicDisorder',
	);

	public function testGetLocationOptions()
	{
		$locations = Diagnosis::Model()->getLocationOptions();
		$this->assertTrue(is_array($locations));
		$this->assertEquals(3, count($locations));
	}

	public function testGetLocationText()
	{
		$diagnosis = $this->diagnoses('diagnosis1');
		$this->assertEquals(0, $diagnosis->location);
		$this->assertEquals('Left', $diagnosis->getLocationText());
		$diagnosis2 = $this->diagnoses('diagnosis2');
		$this->assertEquals(1, $diagnosis2->location);
		$this->assertEquals('Right', $diagnosis2->getLocationText());
	}

	public function testGetCommonOphthalmicDisorderOptions()
	{
		$firm = $this->firms('firm1');

		$diagnosis = new Diagnosis;

		$disorders = $diagnosis->getCommonOphthalmicDisorderOptions($firm);

		$this->assertTrue(is_array($disorders));
		$this->assertEquals(count($this->commonOphthalmicDisorders), count($disorders));
	}

	public function testGetCommonSystemicDisorderOptions()
	{
		$diagnosis = new Diagnosis;

		$disorders = $diagnosis->getCommonSystemicDisorderOptions();

		$this->assertTrue(is_array($disorders));
		$this->assertEquals(4, count($disorders));
	}

	public function getGetDisorderTerm()
	{
		$diagnosis = Diagnosis::Model()->findByPk(1);

		$this->assertEquals($diagnosis->getDisorderTerm(), 'Myopia (disorder)');
	}
}
