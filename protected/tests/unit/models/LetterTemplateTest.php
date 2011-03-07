<?php
class LettertemplateTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'contacttypses' => 'ContactType',
	);

	public function testGetSpecialtyOptions()
	{
		$specialties = LetterTemplate::model()->getSpecialtyOptions();
		$this->assertTrue(is_array($specialties));
		$this->assertEquals(16, count($specialties));
	}

	public function testGetContacttypeOptions()
	{
		$contacttypes = LetterTemplate::model()->getContacttypeOptions();
		$this->assertTrue(is_array($contacttypes));
		$this->assertEquals(8, count($contacttypes));
	}
}