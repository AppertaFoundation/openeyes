<?php
class FirmTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'FirmUserAssignments' => 'FirmUserAssignment',
		'users' => 'User',
		'userContactAssignment' => 'UserContactAssignment',
		'contacts' => 'Contact',
		'consultants' => 'Consultant'
	);

	public function testGetServicespecialtyOptions()
	{
		$serviceSpecialties = Firm::model()->getServiceSpecialtyOptions();
		$this->assertTrue(is_array($serviceSpecialties));
		$this->assertEquals(count($this->serviceSpecialtyAssignment), count($serviceSpecialties));
	}

	public function testGetServiceText()
	{
		$firm = $this->firms('firm1');
		$this->assertEquals($firm->getServiceText(), 'Accident and Emergency Service');
	}

	public function testGetSpecialtyText()
	{
		$firm = $this->firms('firm1');
		$this->assertEquals($firm->getSpecialtyText(), 'Accident & Emergency');
	}

	public function testGetConsultant()
	{
		$firm = $this->firms('firm1');

		$consultant = $firm->getConsultant();

		$this->assertEquals($consultant->contact->nick_name, 'Aylward');
	}
}
