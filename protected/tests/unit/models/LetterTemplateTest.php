<?php
class LetterTemplateTest extends CDbTestCase
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

	public function testGetContactTypeOptions()
	{
		$contactTypes = LetterTemplate::model()->getContactTypeOptions();
		$this->assertTrue(is_array($contactTypes));
		$this->assertEquals(8, count($contactTypes));
	}
}
