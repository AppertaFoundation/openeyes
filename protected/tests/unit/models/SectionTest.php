<?php
class SectionTest extends CDbTestCase
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
		$result = Section::model()->findByPk($fakeId);
		$this->assertNull($result);
	}

	public function testGet_ValidParameters_ReturnsCorrectData()
	{
		$fakeId = 9999;

		$expected = $this->sections('section1');
		$result = Section::model()->findByPk($expected['id']);

		$this->assertEquals(get_class($result), 'Section');
		$this->assertEquals($expected, $result);
	}

	public function testCreate()
	{
		$section = new Section;
		$section->setAttributes(array(
			'name' => 'Testing phrasename',
			'section_type_id' => $this->sectionTypes['sectionType1']['id']
		));
		$this->assertTrue($section->save(true));
	}

	public function testUpdate()
	{
		$expected = 'Testing again';
		$section = Section::model()->findByPk($this->sections['section1']['id']);
		$section->name = $expected;
		$section->save();
		$section = Section::model()->findByPk($this->sections['section1']['id']);
		$this->assertEquals($expected, $section->name);
	}

	public function testDelete()
	{
		$section = Section::model()->findByPk($this->sections['section22']['id']);
		$section->delete();
		$result = Section::model()->findByPk($this->sections['section22']['id']);
		$this->assertNull($result);
	}
}
