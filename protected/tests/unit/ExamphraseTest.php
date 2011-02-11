<?php
class ExamphraseTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'firms' => 'Firm',
		'examphrase' => 'Examphrase',
	);

	public function testGetSpecialtyOptions()
	{
		$specialties = Examphrase::model()->getSpecialtyOptions();
		$this->assertTrue(is_array($specialties));
		$this->assertEquals(16, count($specialties));
	}

	public function testGetPartOptions()
	{
		$parts = Examphrase::model()->getPartOptions();
		$this->assertTrue(is_array($parts));
		$this->assertEquals(9, count($parts));
	}

	public function testPartText()
	{
		$examphrase = Examphrase::model()->find(
			'phrase = :phrase', array(':phrase' => 'Test examphrase 1')
		);

		$this->assertExists($examphrase);
		$this->assertEquals($examphrase->getPartText(), 'History');
	}
}
