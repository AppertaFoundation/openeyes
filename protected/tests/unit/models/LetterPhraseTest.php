<?php
class LetterPhraseTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'letterphrases' => 'LetterPhrase',
	);

	public function testGetFirmOptions()
	{
		$firms = LetterPhrase::model()->getFirmOptions();
		$this->assertTrue(is_array($firms));
		$this->assertEquals(3, count($firms));
	}

	public function testGetSectionOptions()
	{
		$sections = LetterPhrase::model()->getSectionOptions();
		$this->assertTrue(is_array($sections));
		$this->assertEquals(6, count($sections));
	}

	public function testSectionText()
	{
		$letterphrase = LetterPhrase::model()->findByPk(1);

		$this->assertEquals($letterphrase->getSectionText(), 'Introduction');
	}
}
