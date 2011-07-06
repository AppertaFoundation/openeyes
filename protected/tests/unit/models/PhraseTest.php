<?php
class PhraseTest extends CDbTestCase
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
		'phrases'	=> 'Phrase',
		'phraseNames'	=> 'PhraseName'
	);


	public function testGet_InvalidParameters_ReturnsFalse()
	{
		$fakeId = 9999;
		$result = Phrase::model()->findByPk($fakeId);
		$this->assertNull($result);
	}

	public function testGet_ValidParameters_ReturnsCorrectData()
	{
		$fakeId = 9999;

		$expected = $this->phrases('phrase1');
		$result = Phrase::model()->findByPk($expected['id']);

		$this->assertEquals(get_class($result), 'Phrase');
		$this->assertEquals($expected, $result);
	}

	public function testCreate()
	{
		$phrase = new Phrase;
		$phrase->setAttributes(array(
			'phrase' => 'Testing phrase',
			'section_id' => $this->sections['section1']['id'],
			'display_order' => 1,
			'phrase_name_id' => $this->phraseNames['phraseName1']['id'],
		));
		$this->assertTrue($phrase->save(true));
	}

	public function testUpdate()
	{
		$expected = 'Testing again';
		$phrase = Phrase::model()->findByPk($this->phrases['phrase1']['id']);
		$phrase->phrase = $expected;
		$phrase->save();
		$phrase = Phrase::model()->findByPk($this->phrases['phrase1']['id']);
		$this->assertEquals($expected, $phrase->phrase);
	}

	public function testDelete()
	{
		$phrase = Phrase::model()->findByPk($this->phrases['phrase1']['id']);
		$phrase->delete();
		$result = Phrase::model()->findByPk($this->phrases['phrase1']['id']);
		$this->assertNull($result);
	}

	public function testRelevantSectionTypesReturnsValidSectionTypeNames()
	{
		$relevantSectionTypes = Phrase::model()->relevantSectionTypes();
		$this->assertTrue(is_array($relevantSectionTypes));
		foreach ($relevantSectionTypes as $relevantSectionType) {
			$sectionType = SectionType::model()->findByAttributes(array('name' => $relevantSectionType));
			$this->assertInstanceOf('SectionType', $sectionType);
		}
	}
	public function testGetOverrideableNames()
	{
		// No names should be overrideable for Phrase - only for PhraseByFirm or PhraseBySpecialty
		$this->assertFalse(Phrase::model()->getOverrideableNames());
	}
}
