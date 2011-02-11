<?php
class LettertemplateTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'contacttypses' => 'Contacttype',
	);

	public function testGetSpecialtyOptions()
	{
		$specialties = Lettertemplate::model()->getSpecialtyOptions();
		$this->assertTrue(is_array($specialties));
		$this->assertEquals(16, count($specialties));
	}

	public function testGetContacttypeOptions()
	{
		$contacttypes = Lettertemplate::model()->getContacttypeOptions();
		$this->assertTrue(is_array($contacttypes));
		$this->assertEquals(8, count($contacttypes));
	}
}
