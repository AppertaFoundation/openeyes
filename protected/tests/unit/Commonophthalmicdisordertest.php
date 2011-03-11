<?php
class CommonophthalmicdisorderTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
	);

	public function testGetSpecialtyOptions()
	{
		$specialties = ExamPhrase::model()->getSpecialtyOptions();
		$this->assertTrue(is_array($specialties));
		$this->assertEquals(16, count($specialties));
	}
}
