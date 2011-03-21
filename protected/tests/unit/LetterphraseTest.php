<?php
class LetterphraseTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'letterphrases' => ':letter_phrase',
	);

	public function testGetFirmOptions()
	{
		$firms = Letterphrase::model()->getFirmOptions();
		$this->assertTrue(is_array($firms));
		$this->assertEquals(3, count($firms));
	}

	public function testGetSectionOptions()
	{
		$sections = Letterphrase::model()->getSectionOptions();
		$this->assertTrue(is_array($sections));
		$this->assertEquals(6, count($sections));
	}

	public function testSectionText()
	{
		$letterphrase = Letterphrase::model()->findByPk(1);
		$this->assertNotNull($letterphrase);
		$this->assertEquals($letterphrase->getSectionText(), 'Introduction');
	}
}
