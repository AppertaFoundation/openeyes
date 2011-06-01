<?php
class SectionTypeTest extends CDbTestCase
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
		$result = SectionType::model()->findByPk($fakeId);
		$this->assertNull($result);
	}

	public function testGet_ValidParameters_ReturnsCorrectData()
	{
		$fakeId = 9999;

		$expected = $this->sectionTypes('sectionType1');
		$result = SectionType::model()->findByPk($expected['id']);

		$this->assertEquals(get_class($result), 'SectionType');
		$this->assertEquals($expected, $result);
	}

	public function testCreate()
	{
		$sectionType = new SectionType;
		$sectionType->setAttributes(array(
			'name' => 'Testing phrasename',
			'section_type_id' => $this->sectionTypes['sectionType1']['id']
		));
		$this->assertTrue($sectionType->save(true));
	}

	public function testUpdate()
	{
		$expected = 'Testing again';
		$sectionType = SectionType::model()->findByPk($this->sectionTypes['sectionType1']['id']);
		$sectionType->name = $expected;
		$sectionType->save();
		$sectionType = SectionType::model()->findByPk($this->sectionTypes['sectionType1']['id']);
		$this->assertEquals($expected, $sectionType->name);
	}

	public function testDelete()
	{
		$sectionType = SectionType::model()->findByPk($this->sectionTypes['sectionType2']['id']);
		$sectionType->delete();
		$result = SectionType::model()->findByPk($this->sectionTypes['sectionType2']['id']);
		$this->assertNull($result);
	}
}
