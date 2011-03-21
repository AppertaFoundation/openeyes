<?php
class FirmTest extends CDbTestCase
{
	public $fixtures = array(
		'Services' => 'Service',
		'Specialties' => 'Specialty',
		'ServiceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'Firms' => 'Firm',
	);

	public function testGetServicespecialtyOptions()
	{
		$serviceSpecialties = Firm::model()->getServiceSpecialtyOptions();
		$this->assertTrue(is_array($serviceSpecialties));
		$this->assertEquals(4, count($serviceSpecialties));
	}

	public function testGetServiceText()
	{
		$firm = Firm::model()->findByPk(1);
		$this->assertEquals($firm->getServiceText(), 'Accident and Emergency Service');
	}

	public function testGetSpecialtyText()
	{
		$firm = Firm::model()->findByPk(1);
		$this->assertEquals($firm->getSpecialtyText(), 'Accident & Emergency');
	}
}
