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
		'elementHistories' => 'ElementHistory',
		'elementPOHs' => 'ElementPOH'
	);

	protected $service;

	protected function setUp()
	{
		$this->service = new ClinicalService;
		parent::setUp();
	}

	public function testGetElements_EventTypeWithNoElements_ReturnsEmptyArray()
	{
		$firm = $this->firms('firm1');
		$eventType = $this->eventTypes('eventType10');

		$results = $this->service->getElements($eventType, $firm, 1, 1);

		$this->assertEquals(array(), $results);
	}

	public function testGetElements_ValidParameters_FromEvent_ReturnsCorrectData()
	{
		$event = $this->events('event1');

		$results = $this->service->getElements(null, null, null, 1, $event);

		$expected = array(
			$this->elementHistories('elementHistory1'),
			$this->elementPOHs('elementPOH1')
		);

		$this->assertTrue(is_array($results));
		$this->assertEquals(2, count($results));
		$this->assertEquals(get_class($expected[0]), get_class($results[0]));
		$this->assertEquals(get_class($expected[1]), get_class($results[1]));
		$this->assertEquals(1, $results[0]->userId);
		$this->assertEquals(1, $results[0]->patientId);
		$this->assertEquals(1, $results[1]->userId);
		$this->assertEquals(1, $results[1]->patientId);
	}

	public function testGetElements_ValidParameters_ReturnsCorrectData()
	{
		$firm = $this->firms('firm1');
		$eventType = $this->eventTypes('eventType1');

		$results = $this->service->getElements($eventType, $firm, 1, 1);

		$expected = array(
			$this->elementHistories('elementHistory1'),
			$this->elementPOHs('elementPOH1')
		);

		$this->assertTrue(is_array($results));
		$this->assertEquals(2, count($results));
		$this->assertEquals(get_class($expected[0]), get_class($results[0]));
		$this->assertEquals(get_class($expected[1]), get_class($results[1]));
		$this->assertEquals(1, $results[0]->userId);
		$this->assertEquals(1, $results[0]->patientId);
		$this->assertEquals(1, $results[1]->userId);
		$this->assertEquals(1, $results[1]->patientId);
	}

	public function testCreateElements_EmptySiteElementList_ReturnsCorrectData()
	{
		$elements = array();
		$data = array();
		$firm = $this->firms('firm1');

		$result = $this->service->createElements($elements, $data, $firm, 1, 1, 1);
		$this->assertEquals(3, $result);
	}

	public function testCreateElements_ValidParameters_ReturnsCorrectData()
	{
		$data = array('ElementHistory' => $this->elementHistories['elementHistory1']);

		$element = new ElementHistory;
		$element->attributes = $data['ElementHistory'];
		$element->validate();
		$expected = array($element);

		$firm = $this->firms('firm1');
		$result = $this->service->createElements(array($element), $data, $firm, 1, 1, 1);
		$this->assertEquals(3, $result);
	}

	public function testCreateElements_ValidParameters_NewEpisode_ReturnsCorrectData()
	{
		$data = array('ElementHistory' => $this->elementHistories['elementHistory1']);

		$element = new ElementHistory;
		$element->attributes = $data['ElementHistory'];
		$element->validate();
		$expected = array($element);

		$firm = $this->firms('firm1');
		$result = $this->service->createElements(array($element), $data, $firm, 2, 1, 1);
		$this->assertEquals(3, $result);
	}

	public function testCreateElements_InvalidElementData_ReturnsCorrectData()
	{
		$data = array('ElementHistory' => $this->elementHistories('elementHistory1'));
		$firm = $this->firms('firm1');

		$element = new ElementHistory;
		$element->attributes = $data['ElementHistory'];
		$element->description = '  ';
		$element->validate();
		$expected = array($element);

		$result = $this->service->createElements(array($element), $data, $firm, 1, 1, 1);
		$this->assertFalse($result);
	}

    /**
     * @expectedException Exception
     */
	public function testCreateElements_InvalidPatientId_NewEpisode_ReturnsCorrectData()
	{
		$data = array('ElementHistory' => $this->elementHistories['elementHistory1']);

		$element = new ElementHistory;

		$firm = $this->firms('firm1');
		$result = $this->service->createElements(array($element), $data, $firm, 1000, 1, 1);
	}

    /**
     * @expectedException Exception
     */
	public function testCreateElements_InvalidUserId_NewEpisode_ReturnsCorrectData()
	{
		$data = array('ElementHistory' => $this->elementHistories['elementHistory1']);

		$element = new ElementHistory;

		$firm = $this->firms('firm1');
		$result = $this->service->createElements(array($element), $data, $firm, 1, 1000, 1);
	}

	public function testUpdateElements_EmptyElementList_ReturnsValidData()
	{
		$elements = array();
		$data = array();

		$result = $this->service->updateElements($elements, $data, 1);
		$this->assertTrue($result);
	}

	public function testUpdateElements_FormDataInvalid_PreExisting_ReturnsFail()
	{
		// Note we do not provide a data array for ElementHistory, which is required
		$data = array('ElementPOH' => array());

		$event = $this->events('event1');
		$elements = $this->service->getElements(null, null, null, 1, $event);

		$result = $this->service->updateElements($elements, $data, $event);
		$this->assertFalse($result);
	}

	public function testUpdateElements_FormDataAbsent_PreExisting_ReturnsFail()
	{
		$data = array('ElementHistory' => array('description' => null));

		$event = $this->events('event1');
		// Need to use getElements here to ensure that 'required' is populated in the element
		$elements = $this->service->getElements(null, null, null, 1, $event);

		$result = $this->service->updateElements($elements, $data, $event);
		$this->assertFalse($result);
	}

	public function testUpdateElements_EmptyFormData_PreExisting_ReturnsFail()
	{
		$data = array();

		$event = $this->events('event1');
		// Need to use getElements here to ensure that 'required' is populated in the element
		$elements = $this->service->getElements(null, null, null, 1, $event);

		$result = $this->service->updateElements($elements, $data, $event);
		$this->assertFalse($result);
	}

	public function testUpdateElements_Delete_UnrequiredPreExisting_ReturnsFail()
	{
		$data = array(
			'ElementHistory' => array('description' => 'foo')
		);

		$event = $this->events('event1');
		// Need to use getElements here to ensure that 'required' is populated in the element
		$elements = $this->service->getElements(null, null, null, 1, $event);

		$result = $this->service->updateElements($elements, $data, $event);
		$this->assertTrue($result);
	}

	public function testUpdateElements_ValidFormData_NotPreExisting_ReturnsValidData()
	{
		$data = array(
			'ElementHistory' => array('description' => 'foo'),
			'ElementPOH' => array('value' => 'bar')
		);

		$event = $this->events('event1');
		// Need to use getElements here to ensure that 'required' is populated in the element
		$elements = $this->service->getElements(null, null, null, 1, $event);

		$result = $this->service->updateElements($elements, $data, $event);
		$this->assertTrue($result);
		$this->assertEquals($data['ElementHistory']['description'], $elements[0]->description);
		$this->assertEquals($data['ElementPOH']['value'], $elements[1]->value);
	}
}
