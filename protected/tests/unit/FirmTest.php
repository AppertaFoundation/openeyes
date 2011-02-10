<?php
class FirmTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'contacts' => 'Contact'
	);

	public function testGetContactOptions()
	{
		$users = Firm::model()->getContactOptions();
		$this->assertTrue(is_array($users));
		$this->assertEquals(3, count($users));
	}

	public function testGetSpecialtyOptions()
	{
		$specialties = Firm::model()->getSpecialtyOptions();
		$this->assertTrue(is_array($specialties));
		$this->assertEquals(16, count($specialties));
	}

	public function testGetServiceOptions()
	{
		$services = Firm::model()->getServiceOptions();
		$this->assertTrue(is_array($services));
		$this->assertEquals(11, count($services));
	}
}
