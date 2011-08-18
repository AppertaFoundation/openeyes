<?php
class ClinicalControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'eventTypes' => 'EventType',
		'events' => 'Event',
		'serviceSpecialtyAssignments' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'services' => 'Service',
		'specialties' => 'Specialty',
		'siteElementTypes' => 'SiteElementType',
		'elementHistories' => 'ElementHistory',
		'elementPOHs' => 'ElementPOH',
		'referrals' => 'Referral',
		'referralEpisodeAssignments' => 'ReferralEpisodeAssignment'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new ClinicalController('ClinicalController');
		parent::setUp();
	}

	public function dataProvider_EventTypesForAccidentAndEmergencySpecialty()
	{
		return array(
			array('1','9'),
		);
	}

	public function testActionChooseReferral_InvalidReferral_ThrowsException()
	{
		$fakeId = 12345;

		$this->setExpectedException('CHttpException', 'Invalid referral episode assignment id.');
		$this->controller->actionChooseReferral($fakeId);
	}

	public function testActionChooseReferral_RendersChooseReferralView()
	{
		$id = 1;
		$patientId = 1;

		$mockController = $this->getMock('ClinicalController', array('render'),
			array('ClinicalController'));

		$mockController->patientId = $patientId;

		$referrals = array(
			'referral1' => $this->referrals['referral1'],
			'referral2' => $this->referrals['referral2']
		);

		$mockController->expects($this->any())
			->method('render')
			->with('chooseReferral', array(
				'id' => $id,
				'referrals' => CHtml::listData($referrals, 'id', 'refno')
			)
		);

		$mockController->actionChooseReferral($id);
	}

	public function testActionChooseReferral_ValidPostData_RendersViewView()
	{
		$id = 1;
		$patientId = 1;

		$_POST['action'] = 'chooseReferral';
		$_POST['referral_id'] = 1;

		$mockController = $this->getMock('ClinicalController',
			array('redirect'), array('ClinicalController'));

		$mockController->patientId = $patientId;

		$mockController->expects($this->once())
			->method('redirect')
			->with(array('view', 'id' => 1));

		$mockController->actionChooseReferral($id);
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
		$event = $this->events('event1');
		$firm = $this->firms('firm1');

		$elementHistory = $this->elementHistories('elementHistory1');
		$elementPOH = $this->elementPOHs('elementPOH1');

		$expectedElements = array($elementHistory, $elementPOH);

		$mockController = $this->getMock('ClinicalController', array('renderPartial', 'getUserId'), array('ClinicalController'));

		$mockService = $this->getMock('ClinicalService',
			array('getElements'));

		$mockService->expects($this->once())
			->method('getElements')
			->with(null, null, null, 1, $event)
			->will($this->returnValue($expectedElements));

		$mockController->service = $mockService;
		$mockController->firm = $firm;

		$mockController->expects($this->any())
			->method('renderPartial')
			->with('view', array(
				'elements' => $expectedElements,
				'eventId' => $event->id,
				'editable' => true), false, true);

		$mockController->expects($this->once())
			->method('getUserId')
			->will($this->returnValue(1));

		$mockController->actionView($event->id);
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

	public function testActionCreate_ValidElement_RendersCreateView()
	{
		$patientId = 1;
		$eventTypeId = 1;
		$_GET['event_type_id'] = $eventTypeId;
		
		$patient = $this->patients('patient1');

		$event = $this->events('event1');
		$eventType = $this->eventTypes('eventType1');
		$firm = $this->firms('firm1');

		$elementHistory = $this->elementHistories('elementHistory1');
		$elementPOH = $this->elementPOHs('elementPOH1');

		$expectedElements = array($elementHistory, $elementPOH);

		$specialties = Specialty::model()->findAll();

		$mockController = $this->getMock('ClinicalController', array('renderPartial', 'getUserId'), array('ClinicalController'));
		$mockController->patientId = $patientId;
		$mockController->firm = $firm;

		$mockService = $this->getMock('ClinicalService',
			array('getElements'));

		$mockService->expects($this->once())
			->method('getElements')
			->with($eventType, $firm, $patientId, 1)
			->will($this->returnValue($expectedElements));

		$mockController->service = $mockService;
		$mockController->firm = $firm;
		
		$referrals = $mockController->checkForReferrals($firm, $patient->id);

		$mockController->expects($this->once())
			->method('renderPartial')
			->with($mockController->getTemplateName('create', $eventTypeId), array(
				'elements' => $expectedElements,
				'eventTypeId' => $eventTypeId,
				'specialties' => $specialties,
				'patient' => $patient,
				'referrals' => $referrals
			), false, true);

		$mockController->expects($this->once())
			->method('getUserId')
			->will($this->returnValue(1));

		$mockController->actionCreate($event->id);
	}

	public function testActionCreate_ValidPostData_RendersViewView()
	{
		$_POST['elementPOH'] = $this->elementPOHs['elementPOH1'];
		$_POST['elementHistory'] = $this->elementHistories['elementHistory1'];
		$_POST['action'] = 'create';
		$_GET['event_type_id'] = 1;

		$event = $this->events('event1');
		$firm = $this->firms('firm1');
		$eventType = $this->eventTypes('eventType1');
		$patientId = 1;
		$expectedEventId = 1;

		$elementHistory = $this->elementHistories('elementHistory1');
		$elementPOH = $this->elementPOHs('elementPOH1');

		$expectedElements = array($elementHistory, $elementPOH);

		$mockController = $this->getMock('ClinicalController',
			array('render', 'redirect', 'getUserId'), array('ClinicalController'));

		$mockController->expects($this->once())
			->method('redirect')
			->with(array('patient/view', 'id' => $patientId, 'tabId' => 1, 
				'eventId' => $expectedEventId));

		$mockController->expects($this->any())
			->method('getUserId')
			->will($this->returnValue(1));

		$mockService = $this->getMock('ClinicalService',
			array('getElements', 'createElements'));

		$mockService->expects($this->once())
			->method('getElements')
			->with($eventType, $firm, $patientId, 1)
			->will($this->returnValue($expectedElements));

		$mockService->expects($this->once())
			->method('createElements')
			->with($expectedElements, $_POST, $firm, $patientId, 1, $eventType->id)
			->will($this->returnValue($expectedEventId));

		$mockController->firm = $firm;
		$mockController->service = $mockService;
		$mockController->patientId = $patientId;
		$mockController->actionCreate($event->id);
	}

	public function testActionUpdate_InvalidFirmSelected_ThrowsException()
	{
		$event = $this->events('event1');
		$this->controller->firm = $this->firms('firm2');

		$this->setExpectedException('CHttpException', 'The firm you are using is not associated with the specialty for this event.');
		$this->controller->actionUpdate($event->id);
	}

	public function testActionUpdate_InvalidData_RendersUpdateView()
	{
		$patient = $this->patients('patient1');
		$event = $this->events('event1');
		$firm = $this->firms('firm1');
		$userId = 1;

		$this->populateObjects($event, $firm);

		$elementHistory = $this->elementHistories('elementHistory1');
		$elementPOH = $this->elementPOHs('elementPOH1');

		$expectedElements = array($elementHistory, $elementPOH);

		$specialties = Specialty::model()->findAll();
		
		
		$mockController = $this->getMock('ClinicalController', array('renderPartial', 'getUserId'), array('ClinicalController'));

		$mockService = $this->getMock('ClinicalService',
			array('getElements'));

		$mockService->expects($this->once())
			->method('getElements')
			->with(null, null, null, $userId, $event)
			->will($this->returnValue($expectedElements));

		$mockController->service = $mockService;
		$mockController->firm = $firm;

		$mockController->expects($this->any())
			->method('renderPartial')
			->with($mockController->getTemplateName('update', $event->event_type_id),
				array('id' => $event->id, 'elements' => $expectedElements,
					'specialties' => $specialties, 'patient' => $patient), false, true);

		$mockController->expects($this->once())
			->method('getUserId')
			->will($this->returnValue($userId));

		$mockController->actionUpdate($event->id);
	}

	public function testActionUpdate_ValidPostData_RendersViewView()
	{
		$_POST = $this->events['event1'];
		$_POST['action'] = 'update';
		$userId = 1;

		$event = $this->events('event1');
		$firm = $this->firms('firm1');

		$this->populateObjects($event, $firm);

		$elementHistory = $this->elementHistories('elementHistory1');
		$elementPOH = $this->elementPOHs('elementPOH1');

		$expectedElements = array($elementHistory, $elementPOH);

		$mockController = $this->getMock('ClinicalController',
			array('renderPartial', 'redirect', 'getUserId'), array('ClinicalController'));
		$mockController->expects($this->once())
			->method('redirect')
			->with(array('patient/view', 'id' => null, 'tabId' => 1,  // Id is from $controller->patientId, but it's not stored in the mock
				'eventId' => $event->id));

		$mockController->expects($this->once())
			->method('getUserId')
			->will($this->returnValue($userId));

		$mockService = $this->getMock('ClinicalService',
			array('getElements', 'updateElements'));

		$mockService->expects($this->once())
			->method('getElements')
			->with(null, null, null, $userId, $event)
			->will($this->returnValue($expectedElements));

		$mockService->expects($this->once())
			->method('updateElements')
			->with($expectedElements, $_POST, $event)
			->will($this->returnValue(true));

		$mockController->expects($this->once())
			->method('getUserId')
			->will($this->returnValue($userId));

		$mockController->firm = $firm;
		$mockController->service = $mockService;
		$mockController->actionUpdate($event->id);
	}

	public function testActionEpisodeSummary_InvalidEpisode_ThrowsException()
	{
		$fakeId = 5829;

		$this->setExpectedException('CHttpException', 'Invalid episode id.');
		$this->controller->actionEpisodeSummary($fakeId);
	}

	public function testActionEpisodeSummary_ValidEpisode_RendersEpisodeSummaryView()
	{
		$episode = $this->episodes('episode1');

		$mockController = $this->getMock('ClinicalController', array('renderPartial'), array('ClinicalController'));

		$mockService = $this->getMock('ClinicalService');
		$mockController->service = $mockService;

		$mockController->expects($this->any())
			->method('renderPartial')
			->with('episodeSummary', array(
				'episode' => $episode
			), false, true);

		$mockController->actionEpisodeSummary($episode->id);
	}

	public function testActionSummary_InvalidEpisode_ThrowsException()
	{
		$fakeId = 5829;

		$this->setExpectedException('CHttpException', 'Invalid episode id.');
		$this->controller->actionSummary($fakeId);
	}

	public function testActionSummary_NoSummary_ThrowsException()
	{
		$episode = $this->episodes('episode1');

		$this->setExpectedException('CHttpException', 'No summary.');

		$this->controller->actionSummary($episode->id);
	}

	public function testActionSummary_ValidElement_RendersSummaryView()
	{
		$episode = $this->episodes('episode1');
		$summaryName = 'episodeSummary';

		$mockController = $this->getMock('ClinicalController', array('renderPartial'), array('ClinicalController'));

		$mockService = $this->getMock('ClinicalService');
		$mockController->service = $mockService;

		$mockController->expects($this->any())
			->method('renderPartial')
			->with('summary', array(
				'episode' => $episode,
				'summary' => $summaryName), false, true);

		$_GET['summary'] = $summaryName;
		$mockController->actionSummary($episode->id);
	}

	public function testListEpisodes()
	{
		$patient = $this->patients('patient1');
		$firm = $this->firms('firm1');

		$mockController = $this->getMock('ClinicalController', array('checkPatientId'), array('ClinicalController'));
		$mockController->expects($this->any())->method('checkPatientId');
		$mockController->patientId = $patient->id;
		$mockController->selectedFirmId = $firm->id;
		$mockController->firm = $firm;

		$this->assertNull($mockController->episodes);
		$mockController->listEpisodesAndEventTypes();
		$this->assertEquals($patient->episodes, $mockController->episodes);
	}

	public function testGetUserId_NoUserIdSet_ReturnsNull()
	{
		$this->assertNull($this->controller->getUserId());
	}

	public function testGetUserId_UserIdSet_ReturnsCorrectData()
	{
		$userInfo = $this->users['user1'];
		$identity = new UserIdentity('JoeBloggs', 'secret');
		$identity->authenticate();
		Yii::app()->user->login($identity);

		$userId = $this->users['user1']['id'];
		$this->assertEquals($userId, $this->controller->getUserId(), 'Should return the correct user id');
	}

	/**
	 * These two stupid bits of code are here to ensure that the event and firm objects
	 * match properly, else the test fails on the
	 *		//	'The firm you are using is not associated with the specialty for this event.'
	 *		// test.
	 *
	 * @param object $event
	 * @param object $firm
	 */
	public function populateObjects($event, $firm)
	{
		$foo = $event->episode->firm->serviceSpecialtyAssignment->specialty_id;
		$bar = $firm->serviceSpecialtyAssignment->specialty_id;
	}

	public function testStoreData_StoresValidData()
	{
		$mockController = $this->getMock('ClinicalController',
			array('checkPatientId', 'listEpisodesAndEventTypes'),
			array('ClinicalController'), 'Mock_ClinicalController', false);

		$firmId = $this->firms['firm1']['id'];
		$service = new ClinicalService;

		$mockController->selectedFirmId = $firmId;
		$mockController->expects($this->once())
			->method('checkPatientId');
		$mockController->expects($this->once())
			->method('listEpisodesAndEventTypes');

		$mockController->storeData();
		$this->assertEquals($this->firms('firm1'), $mockController->firm, 'Firm should be loaded.');
		$this->assertEquals($service, $mockController->service, 'Service should be created.');
	}
}
