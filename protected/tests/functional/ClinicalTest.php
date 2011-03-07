<?php
class ClinicalControllerTest extends WebTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'episodes' => 'Episode',
		'eventTypes' => 'EventType',
		'events' => 'Event',
		'firms' => 'Firm',
		'serviceSpecialtyAssignments' => 'ServiceSpecialtyAssignment',
		'services' => 'Service',
		'specialties' => 'Specialty',
	);

	public function testIndex()
	{
		$this->open('clinical');
	}
}