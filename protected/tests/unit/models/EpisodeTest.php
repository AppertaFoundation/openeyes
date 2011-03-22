<?php
class EpisodeTest extends CDbTestCase
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


	public function testGetBySpecialtyAndPatient_InvalidParameters_ReturnsFalse()
	{
		$specialtyId = 9278589128;
		$patientId = 2859290852;

		$result = Episode::model()->getBySpecialtyAndPatient($specialtyId, $patientId);

		$this->assertNull($result);
	}

	public function testGetBySpecialtyAndPatient_ValidParameters_ReturnsCorrectData()
	{
		$specialty = $this->specialties('specialty1');
		$patient = $this->patients('patient1');

		$expected = $this->episodes('episode1');

		$result = Episode::model()->getBySpecialtyAndPatient($specialty->id, $patient->id);

		$this->assertEquals(get_class($result), 'Episode');
		$this->assertEquals($expected, $result);
	}
}
