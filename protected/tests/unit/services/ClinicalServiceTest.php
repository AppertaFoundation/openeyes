<?php

class ClinicalServiceTest extends CDbTestCase
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
		'elementHistories' => 'ElementHistory'
	);

	protected $service;

	protected function setUp()
	{
		$this->service = new ClinicalService;
		parent::setUp();
	}

	public function dataProvider_InvalidEventElementTypeParameters()
	{
		$eventId = 82758934902;

		return array(
			array(array(), $eventId, true),
			array(array(), $eventId, false)
		);
	}

	public function testGetSiteElementTypeObjects_ValidParameters_ReturnsCorrectData()
	{
		$firm = $this->firms('firm1');
		$results = $this->service->getSiteElementTypeObjects(1, $firm);

		$expected = array($this->siteElementTypes('siteElementType1'));

		$this->assertEquals(count($results), 1);
		$this->assertEquals(get_class($results[0]), 'SiteElementType');
		$this->assertEquals($expected, $results);
	}

	public function testGetEpisodeBySpecialtyAndPatient_InvalidParameters_ReturnsFalse()
	{
		$specialtyId = 9278589128;
		$patientId = 2859290852;

		$result = $this->service->getEpisodeBySpecialtyAndPatient($specialtyId, $patientId);

		$this->assertNull($result);
	}

	public function testGetEpisodeBySpecialtyAndPatient_ValidParameters_ReturnsCorrectData()
	{
		$specialty = $this->specialties('specialty1');
		$patient = $this->patients('patient1');

		$expected = $this->episodes('episode1');

		$result = $this->service->getEpisodeBySpecialtyAndPatient($specialty->id, $patient->id);

		$this->assertEquals(get_class($result), 'Episode');
		$this->assertEquals($expected, $result);
	}

	/**
	 * @dataProvider dataProvider_InvalidEventElementTypeParameters
	 */
	public function testGetEventElementTypes_InvalidParameters_ReturnsEmptyArray($siteElementTypes, $eventId, $createElement)
	{
		$results = $this->service->getEventElementTypes($siteElementTypes, $eventId, $createElement);

		$this->assertEquals(array(), $results);
	}

	public function testGetEventElementTypes_ValidParameters_CreateElementFalse_ReturnsCorrectData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$event = $this->events('event1');

		$results = $this->service->getEventElementTypes($siteElementTypes, $event->id, false);

		$expected = array();
		$elementHistory = $this->elementHistories('elementHistory1');
		foreach ($this->siteElementTypes as $name => $values) {
			$expected[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name),
			);
		}

		$this->assertEquals($expected, $results);
	}

	public function testGetEventElementTypes_ValidParameters_CreateElementMissing_ReturnsCorrectData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$event = $this->events('event1');

		$results = $this->service->getEventElementTypes($siteElementTypes, $event->id);

		$expected = array();
		$elementHistory = $this->elementHistories('elementHistory1');
		foreach ($this->siteElementTypes as $name => $values) {
			$expected[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name),
			);
		}

		$this->assertEquals($expected, $results);
	}

	public function testGetEventElementTypes_ValidParameters_CreateElementTrue_PreExistingEvent_ReturnsCorrectData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$event = $this->events('event1');

		$results = $this->service->getEventElementTypes($siteElementTypes, $event->id, true);

		$expected = array();
		$elementHistory = $this->elementHistories('elementHistory1');
		foreach ($this->siteElementTypes as $name => $values) {
			$expected[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name),
				'preExisting' => true
			);
		}

		$this->assertEquals($expected, $results);
	}

	public function testGetEventElementTypes_ValidParameters_CreateElementTrue_NoPreExistingEvent_ReturnsCorrectData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$event = $this->events('event2');

		$results = $this->service->getEventElementTypes($siteElementTypes, $event->id, true);

		$expected = array();
		$elementHistory = new ElementHistory;
		foreach ($this->siteElementTypes as $name => $values) {
			$expected[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name),
				'preExisting' => false
			);
		}

		$this->assertEquals($expected, $results);
	}

	public function testValidateElements_EmptySiteElementList_ReturnsCorrectData()
	{
		$siteElementTypes = array();
		$data = array();

		$results = $this->service->validateElements($siteElementTypes, $data);
		$this->assertTrue($results['valid']);
		$this->assertEquals(array(), $results['elements']);
	}

	public function testValidateElements_ValidParameters_ReturnsCorrectData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$data = array('ElementHistory' => $this->elementHistories['elementHistory1']);

		$element = new ElementHistory;
		$element->attributes = $data['ElementHistory'];
		$element->validate();
		$expected = array();
		foreach ($this->siteElementTypes as $name => $values) {
			$expected[] = $element;
		}

		$results = $this->service->validateElements($siteElementTypes, $data);
		$this->assertTrue($results['valid']);
		$this->assertEquals($expected, $results['elements']);
	}

	public function testValidateElements_InvalidElementData_ReturnsCorrectData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$data = array('ElementHistory' => $this->elementHistories('elementHistory1'));

		$element = new ElementHistory;
		$element->attributes = $data['ElementHistory'];
		$element->description = '  ';
		$element->validate();
		$expected = array();

		$results = $this->service->validateElements($siteElementTypes, $data);
		$this->assertFalse($results['valid']);
		$this->assertEquals(count($expected), count($results['elements']));
	}

	public function testUpdateElements_EmptyElementList_ReturnsValidData()
	{
		$siteElementTypes = array();
		$data = array();

		$result = $this->service->updateElements($siteElementTypes, $data, 1);
		$this->assertTrue($result);
	}

	public function testUpdateElements_EmptyFormData_NotPreExisting_ReturnsValidData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$data = array('ElementHistory' => array('foo'));

		$elements = array();
		$elementHistory = $this->elementHistories('elementHistory1');
		foreach ($this->siteElementTypes as $name => $values) {
			$elements[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name),
			);
		}

		$result = $this->service->updateElements($elements, $data, 1);
		$this->assertTrue($result);
	}

	public function testUpdateElements_EmptyFormData_PreExisting_ReturnsValidData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$data = array('ElementHistory' => array('foo'));

		$elements = array();
		$elementHistory = $this->elementHistories('elementHistory1');
		foreach ($this->siteElementTypes as $name => $values) {
			$elements[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name),
				'preExisting' => true
			);
		}

		$this->assertEquals('ElementHistory', get_class($elements[0]['element']));
		$result = $this->service->updateElements($elements, $data, 1);
		$this->assertTrue($result);
	}

	public function testUpdateElements_InvalidEventId_ReturnsValidData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$data = array('ElementHistory' => array('foo'));

		$elements = array();
		$elementHistory = $this->elementHistories('elementHistory1');
		foreach ($this->siteElementTypes as $name => $values) {
			$elements[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name)
			);
		}

		$this->assertEquals('ElementHistory', get_class($elements[0]['element']));
		$result = $this->service->updateElements($elements, $data, 'test');
		$this->assertFalse($result);
	}

	public function testUpdateElements_ValidFormData_NotPreExisting_ReturnsValidData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$data = array('ElementHistory' => array('description' => 'foo'));

		$elements = array();
		$elementHistory = $this->elementHistories('elementHistory1');
		foreach ($this->siteElementTypes as $name => $values) {
			$elements[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name),
			);
		}

		$result = $this->service->updateElements($elements, $data, 1);
		$this->assertTrue($result);
		foreach ($elements as $element) {
			$this->assertEquals($data['ElementHistory']['description'], $element['element']->description);
		}
	}

	public function testUpdateElements_ValidFormData_PreExisting_ReturnsValidData()
	{
		$siteElementTypes = SiteElementType::model()->findAll();
		$data = array('ElementHistory' => array('description' => 'foo'));

		$elements = array();
		$elementHistory = $this->elementHistories('elementHistory1');
		foreach ($this->siteElementTypes as $name => $values) {
			$elements[] = array(
				'element' => $elementHistory,
				'siteElementType' => $this->siteElementTypes($name),
				'preExisting' => true
			);
		}

		$this->assertEquals('ElementHistory', get_class($elements[0]['element']));
		$result = $this->service->updateElements($elements, $data, 1);
		$this->assertTrue($result);
		foreach ($elements as $element) {
			$this->assertEquals($data['ElementHistory']['description'], $element['element']->description);
		}
	}

	/*
	 *
	public function updateElements($elements, $data)
	{
		$success = true;

		foreach ($elements as $element) {
			$elementClassName = get_class($element['element']);

			if ($data[$elementClassName]) {
				// The user has entered information for this element
				// Check if it's a pre-existing element
				if (!$element['preExisting']) {
					// It's not pre-existing so give it an event id
					$element['element']->event_id = $event->id;
				}

				// @todo - is there a risk they could change the event id here?
				$element['element']->attributes = $data[$elementClassName];
			}

			if (!$element['element']->save()) {
				$success = true;
			}
		}

		return $success;
	}
	 */
}