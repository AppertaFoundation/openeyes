<?php
class PhraseNameTest extends CDbTestCase
{
	public $fixtures = array(
		'sections' => 'Section',
		'sectionTypes' => 'SectionType',
		'services' => 'Service',
		'specialties' => 'Specialty',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'eventTypes' => 'EventType',
		'elementTypes' => 'ElementType',
		'possibleElementTypes' => 'PossibleElementType',
		'siteElementTypes' => 'SiteElementType',
		'phraseNames'	=> 'PhraseName',
	);


	public function testGet_InvalidParameters_ReturnsFalse()
	{
		$fakeId = 9999;
		$result = PhraseName::model()->findByPk($fakeId);
		$this->assertNull($result);
	}

	public function testGet_ValidParameters_ReturnsCorrectData()
	{
		$fakeId = 9999;

		$expected = $this->phraseNames('phraseName1');
		$result = PhraseName::model()->findByPk($expected['id']);

		$this->assertEquals(get_class($result), 'PhraseName');
		$this->assertEquals($expected, $result);
	}

	public function testCreate()
	{
		$phraseName = new PhraseName;
		$phraseName->setAttributes(array(
			'name' => 'Testing phrasename',
		));
		$this->assertTrue($phraseName->save(true));
	}

	public function testUpdate()
	{
		$expected = 'Testing again';
		$phraseName = PhraseName::model()->findByPk($this->phraseNames['phraseName1']['id']);
		$phraseName->name = $expected;
		$phraseName->save();
		$phraseName = PhraseName::model()->findByPk($this->phraseNames['phraseName1']['id']);
		$this->assertEquals($expected, $phraseName->name);
	}

	public function testDelete()
	{
		$phraseName = PhraseName::model()->findByPk($this->phraseNames['phraseName37']['id']);
		$phraseName->delete();
		$result = PhraseName::model()->findByPk($this->phraseNames['phraseName37']['id']);
		$this->assertNull($result);
	}
}
