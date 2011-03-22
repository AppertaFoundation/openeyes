<?php
class SiteElementTypeTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'eventTypes' => 'EventType',
		'elementTypes' => 'ElementType',
		'possibleElementTypes' => 'PossibleElementType',
		'siteElementTypes' => 'SiteElementType',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'events' => 'Event',
	);

        public function testGetAllPossible_ValidParameters_ReturnsCorrectData()
        {
                $specialty = $this->specialties('specialty1');
                $results = SiteElementType::model()->getAllPossible(1, $specialty->id);

                $expected = array($this->siteElementTypes('siteElementType1'));

                $this->assertEquals(count($results), 1);
                $this->assertEquals(get_class($results[0]), 'SiteElementType');
                $this->assertEquals($expected, $results);
        }
}
