<?php
class ClinicalControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'eventTypes' => 'EventType',
		'events' => 'Event',
		'firms' => 'Firm',
		'serviceSpecialtyAssignments' => 'ServiceSpecialtyAssignment',
		'services' => 'Service',
		'specialties' => 'Specialty',
		'siteElementTypes' => 'SiteElementType',
		'elementHistories' => 'ElementHistory',
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new ClinicalController('ClinicalController');
		parent::setUp();
	}

	public function dataProvider_InvalidCreatePostData()
	{
		return array(
			array(null),
			array('action' => 'index'),
			array('action' => 'edit'),
			array('action' => 'view'),
		);
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('ClinicalController', array('render'),
			array('ClinicalController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index');
		$mockController->actionIndex();
	}

	public function testActionView_InvalidEvent_ThrowsException()
	{
		$fakeId = 5829;

		$this->setExpectedException('CHttpException', 'Invalid event id.');
		$this->controller->actionView($fakeId);
	}

	public function testActionView_ValidElement_RendersViewView()
	{
		$eventId = $this->events['event1']['id'];
		$eventTypeId = $this->eventTypes['eventType1']['id'];
		$firm = $this->firms('firm1');
		$siteElementTypes = SiteElementType::model()->findAll();
		$expectedElements = array();

		$mockController = $this->getMock('ClinicalController', array('render'), array('ClinicalController'));
		$mockService = $this->getMock('ClinicalService',
			array('getSiteElementTypeObjects', 'getEventElementTypes'));

		$mockService->expects($this->once())
			->method('getSiteElementTypeObjects')
			->with($this->events['event1']['event_type_id'], $firm)
			->will($this->returnValue($siteElementTypes));
		$mockService->expects($this->once())
			->method('getEventElementTypes')
			->with($siteElementTypes, $eventId)
			->will($this->returnValue($expectedElements));

		$mockController->firm = $firm;
		$mockController->service = $mockService;
		$mockController->expects($this->any())
			->method('render')
			->with('view', array('elements' => $expectedElements));
		$mockController->actionView($eventId);
	}

	public function testBeforeAction()
	{
		$this->markTestSkipped('figure out how to test beforeAction');
		$mockController = $this->getMock('ClinicalController',
			array('checkPatientId', 'listEpisodesAndEventTypes'),
			array('ClinicalController'), 'Mock_ClinicalController', false);

		$mockController->selectedFirmId = $this->firms['firm1']['id'];
		$mockController->expects($this->once())
			->method('checkPatientId');
		$mockController->expects($this->once())
			->method('listEpisodesAndEventTypes');

		$mockController->beforeAction('index');
	}

	public function testActionCreate_MissingEventTypeId_ThrowsException()
	{
		$this->setExpectedException('CHttpException', 'No event_type_id specified.');
		$this->controller->actionCreate();
	}

	public function testActionCreate_InvalidEventTypeId_ThrowsException()
	{
		$_GET['event_type_id'] = 927490278592;

		$this->setExpectedException('CHttpException', 'Invalid event_type_id.');
		$this->controller->actionCreate();
	}

	public function testActionUpdate_InvalidFirmSelected_ThrowsException()
	{
		$event = $this->events('event1');
		$this->controller->firm = $this->firms('firm2');

		$this->setExpectedException('CHttpException', 'The firm you are using is not associated with the specialty for this event.');
		$this->controller->actionUpdate($event->id);
	}

	/**
	 * @dataProvider dataProvider_InvalidCreatePostData
	 */
	public function testActionUpdate_InvalidPostData_RendersUpdateView($data)
	{
		$_POST = $data;
		$eventId = $this->events['event1']['id'];
		$firm = $this->firms('firm1');
		$siteElementTypes = SiteElementType::model()->findAll();
		$expectedElements = $siteElementTypes;

		$mockController = $this->getMock('ClinicalController', array('render'), array('ClinicalController'));
		$mockController->expects($this->any())
			->method('render')
			->with('update', array(
				'id' => $eventId,
				'elements' => $siteElementTypes,
			));

		$mockService = $this->getMock('ClinicalService',
			array('getSiteElementTypeObjects', 'getEventElementTypes'));

		$mockService->expects($this->once())
			->method('getSiteElementTypeObjects')
			->with($this->events['event1']['event_type_id'], $firm)
			->will($this->returnValue($siteElementTypes));
		$mockService->expects($this->once())
			->method('getEventElementTypes')
			->with($siteElementTypes, $eventId, true)
			->will($this->returnValue($expectedElements));
		$mockController->firm = $firm;
		$mockController->service = $mockService;
		$mockController->actionUpdate($eventId);
	}

	public function testActionUpdate_ValidPostData_RendersViewView()
	{
		$_POST = $this->events['event1'];
		$_POST['action'] = 'update';
		$eventId = $this->events['event1']['id'];
		$firm = $this->firms('firm1');
		$siteElementTypes = SiteElementType::model()->findAll();
		$expectedElements = $siteElementTypes;

		$mockController = $this->getMock('ClinicalController',
			array('render', 'redirect'), array('ClinicalController'));
		$mockController->expects($this->once())
			->method('redirect')
			->with(array('view', 'id' => $eventId));

		$mockService = $this->getMock('ClinicalService',
			array('getSiteElementTypeObjects', 'getEventElementTypes',
				  'updateElements'));

		$mockService->expects($this->once())
			->method('getSiteElementTypeObjects')
			->with($this->events['event1']['event_type_id'], $firm)
			->will($this->returnValue($siteElementTypes));
		$mockService->expects($this->once())
			->method('getEventElementTypes')
			->with($siteElementTypes, $eventId, true)
			->will($this->returnValue($expectedElements));
		$mockService->expects($this->once())
			->method('updateElements')
			->with($expectedElements, $_POST)
			->will($this->returnValue(true));
		$mockController->firm = $firm;
		$mockController->service = $mockService;
		$mockController->actionUpdate($eventId);
	}

	public function testListEpisodesAndEventTypes()
	{
		$patient = $this->patients('patient1');
		$eventTypes = EventType::model()->findAll();
		$mockController = $this->getMock('ClinicalController',
			array('checkPatientId'), array('ClinicalController'));
		$mockController->expects($this->any())
			->method('checkPatientId');
		$mockController->patientId = $patient->id;

		$this->assertNull($mockController->episodes);
		$this->assertNull($mockController->eventTypes);
		$mockController->listEpisodesAndEventTypes();
		$this->assertEquals($patient->episodes, $mockController->episodes);
		$this->assertEquals($eventTypes, $mockController->eventTypes);
	}
	
	public function testGetEpisode()
	{
		
	}

	/*
	 * 		$specialty = $this->firm->serviceSpecialtyAssignment->specialty;
		$episode = Episode::modelBySpecialtyIdAndPatientId(
			$specialty->id,
			$this->patientId
		);
		return $episode;
	 */
}