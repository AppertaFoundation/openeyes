<?php
class ClinicalControllerTest extends CDbTestCase
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
		'siteElementTypes' => 'SiteElementType',
		'elementHistories' => 'ElementHistory',
		'patients' => 'Patient'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new ClinicalController('ClinicalController');
		parent::setUp();
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('ClinicalController', array('render'), array('ClinicalController'));
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
}