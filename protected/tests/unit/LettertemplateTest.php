<?php
class LettertemplateTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'contacttypes' => ':contact_type',
	);

	public function testGetSpecialtyOptions()
	{
		$specialties = LetterTemplate::model()->getSpecialtyOptions();
		$this->assertTrue(is_array($specialties));
		$this->assertEquals(16, count($specialties));
	}

	public function testGetContactTypeOptions()
	{
		$contacttypes = LetterTemplate::model()->getContactTypeOptions();
		$this->assertTrue(is_array($contacttypes));
		$this->assertEquals(8, count($contacttypes));
	}
}
