<?php
class SequenceControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'sites' => 'Site',
		'theatres' => 'Theatre',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new SequenceController('SequenceController');
		parent::setUp();
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('SequenceController', array('render'),
			array('SequenceController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index');
		$mockController->actionIndex();
	}

	public function testActionView_InvalidId_ThrowsException()
	{
		$fakeId = 5829;

		$this->setExpectedException('CHttpException', 'The requested page does not exist.');
		$this->controller->actionView($fakeId);
	}

	public function testActionView_ValidId_RendersViewView()
	{
		$sequence = $this->sequences('sequence1');
		$firm = $this->firms('firm1');

		$mockController = $this->getMock('SequenceController', array('render'), array('SequenceController'));

		$mockController->expects($this->once())
			->method('render')
			->with('view', array('model' => $sequence));

		$mockController->actionView($sequence->id);
	}

	public function testActionCreate_NoPostData_RendersCreateView()
	{
		$sequence = new Sequence;
		$firm = new SequenceFirmAssignment;

		$mockController = $this->getMock('SequenceController', array('render'), array('SequenceController'));

		$mockController->expects($this->once())
			->method('render')
			->with('create', array(
				'model' => $sequence,
				'firm' => $firm,
			));

		$mockController->actionCreate();
	}

	public function testActionCreate_InvalidPostData_RendersCreateViewWithErrors()
	{
		$_POST['Sequence'] = array();
		$_POST['SequenceFirmAssignment'] = array();
		$_POST['action'] = 'create';

		$sequence = new Sequence;
		$sequence->validate();
		$firmAssignment = new SequenceFirmAssignment;
		$firmAssignment->validate();

		$mockController = $this->getMock('SequenceController',
			array('render', 'redirect'), array('SequenceController'));

		$mockController->expects($this->never())
			->method('redirect');
		
		$mockController->expects($this->once())
			->method('render')
			->with('create', array('model'=>$sequence,'firm'=>$firmAssignment));

		$mockController->actionCreate();
		
		$requiredFields = array('end_time', 'frequency', 'start_date', 'start_time', 'theatre_id');
		
		$this->assertEquals(array(), array_diff($requiredFields, array_keys($sequence->getErrors())));
		$this->assertEquals(array(), array_diff(array('firm_id'), array_keys($firmAssignment->getErrors())));
	}

	public function testActionCreate_ValidPostData_RendersViewView()
	{
		$sequenceData = $this->sequences['sequence1'];
		$sequenceData['start_date'] = date('Y-m-d', strtotime('+3 days'));
		
		$maxId = $this->sequences['sequence3']['id'];
		
		$_POST['Sequence'] = $sequenceData;
		$_POST['SequenceFirmAssignment'] = $this->sequenceFirmAssignments['sfa1'];
		$_POST['action'] = 'create';

		$mockController = $this->getMock('SequenceController',
			array('render', 'redirect'), array('SequenceController'));

		$mockController->expects($this->once())
			->method('redirect');

		$mockController->actionCreate();
	}

	public function testActionUpdate_NoPostData_RendersCreateView()
	{
		$sequence = $this->sequences('sequence1');
		$sequence->firmAssignment;
		$firm = $this->sequenceFirmAssignments('sfa1');

		$mockController = $this->getMock('SequenceController', array('render'), array('SequenceController'));

		$mockController->expects($this->once())
			->method('render')
			->with('update', array(
				'model' => $sequence,
				'firm' => $firm,
			));

		$mockController->actionUpdate($sequence->id);
	}

	public function testActionUpdate_InvalidPostData_RendersUpdateViewWithErrors()
	{
		$_POST['Sequence'] = array();
		$_POST['SequenceFirmAssignment'] = array();
		$_POST['action'] = 'update';

		$sequence = $this->sequences('sequence1');
		$firmAssignment = $sequence->firmAssignment;
		$sequence->validate();
		$firmAssignment->validate();

		$mockController = $this->getMock('SequenceController',
			array('render', 'redirect'), array('SequenceController'));

		$mockController->expects($this->once())
			->method('redirect');
		
		$mockController->expects($this->once())
			->method('render')
			->with('update', array('model'=>$sequence,'firm'=>$firmAssignment));

		$mockController->actionUpdate($sequence->id);
	}

	public function testActionUpdate_ValidPostData_RendersViewView()
	{
		$sequenceData = $this->sequences['sequence1'];
		$startDate = date('Y-m-d', strtotime('+3 days'));
		$sequenceData['start_date'] = $startDate;
		
		$_POST['Sequence'] = $sequenceData;
		$_POST['SequenceFirmAssignment'] = $this->sequenceFirmAssignments['sfa1'];
		$_POST['action'] = 'update';

		$sequence = $this->sequences('sequence1');
		$sequence->start_date = $startDate;
		$firm = $this->firms('firm1');

		$mockController = $this->getMock('SequenceController',
			array('render', 'redirect'), array('SequenceController'));

		$mockController->expects($this->any())
			->method('redirect')
			->with(array('view', 'id' => $sequence->id));

		$mockController->actionUpdate($sequence->id);
	}
	
	public function testActionAdmin_NoGetParameter_RendersAdminView()
	{
		$_GET = array();
		
		$sequence = new Sequence('search');
		$sequence->unsetAttributes();

		$mockController = $this->getMock('SequenceController',
			array('render'), array('SequenceController'));

		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $sequence));

		$mockController->actionAdmin();
	}
	
	public function testActionAdmin_WithGetParameter_RendersAdminView()
	{
		$data = $this->sequences['sequence1'];
		$_GET = array('Sequence' => $data);
		
		$sequence = new Sequence('search');
		$sequence->attributes = $data;

		$mockController = $this->getMock('SequenceController',
			array('render'), array('SequenceController'));

		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $sequence));

		$mockController->actionAdmin();
	}
}