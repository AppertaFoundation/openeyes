<?php
class TheatreControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'sites' => 'Site',
		'theatres' => 'Theatre',
		'sequences' => 'Sequence',
		'sequenceFirmAssignments' => 'SequenceFirmAssignment',
		'serviceSpecialtyAssignments' => 'ServiceSpecialtyAssignment',
		'operations' => 'ElementOperation',
		'procedures' => 'Procedure',
		'patients' => 'Patient'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new TheatreController('TheatreController');
		parent::setUp();
	}

	public function testActionIndex_NoPostData_RendersIndexView()
	{
		$mockController = $this->getMock('TheatreController', array('render'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index', array(
				'theatres'=>array(),
				'siteId' => null,
				'serviceId' => null,
				'firmId' => null,
				'theatreId' => null,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}

	public function testActionIndex_WithSiteId_RendersIndexView()
	{
		$_POST['site-id'] = $this->sites['site1']['id'];
		
		$theatre1 = $this->theatres['theatre1'];
		$theatre2 = $this->theatres['theatre2'];
		
		$theatreList = array(
			$theatre1['id'] => $theatre1['name'],
			$theatre2['id'] => $theatre2['name'],
		);
		
		$mockController = $this->getMock('TheatreController', array('render'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index', array(
				'theatres'=>array(),
				'siteId' => $_POST['site-id'],
				'serviceId' => null,
				'firmId' => null,
				'theatreId' => null,
				'dateFilter' => null,
				'theatreList' => $theatreList,
				'firmList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}

	public function testActionIndex_WithTheatreId_RendersIndexView()
	{
		$theatreId = $this->theatres['theatre1']['id'];
		$_POST['theatre-id'] = $theatreId;
		
		$mockController = $this->getMock('TheatreController', array('render'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index', array(
				'theatres'=>array(),
				'siteId' => null,
				'serviceId' => null,
				'firmId' => null,
				'theatreId' => $theatreId,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}

	public function testActionIndex_WithServiceId_RendersIndexView()
	{
		$serviceId = $this->serviceSpecialtyAssignments['servicespecialtyassignment1']['service_id'];
		$_POST['service-id'] = $serviceId;
		
		$firmList = array($this->firms['firm1']['id'] => $this->firms['firm1']['name']);
		
		$mockController = $this->getMock('TheatreController', array('render'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index', array(
				'theatres'=>array(),
				'siteId' => null,
				'serviceId' => $serviceId,
				'firmId' => null,
				'theatreId' => null,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => $firmList,
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}

	public function testActionIndex_WithFirmId_RendersIndexView()
	{
		$firmId = $this->firms['firm1']['id'];
		$_POST['firm-id'] = $firmId;
		
		$mockController = $this->getMock('TheatreController', array('render'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index', array(
				'theatres'=>array(),
				'siteId' => null,
				'serviceId' => null,
				'firmId' => $firmId,
				'theatreId' => null,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}

	public function testActionIndex_DailyDateFilter_RendersIndexView()
	{
		$dateFilter = 'today';
		$_POST['date-filter'] = $dateFilter;
		
		$mockController = $this->getMock('TheatreController', array('render'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index', array(
				'theatres'=>array(),
				'siteId' => null,
				'serviceId' => null,
				'firmId' => null,
				'theatreId' => null,
				'dateFilter' => $dateFilter,
				'theatreList' => array(),
				'firmList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}

	public function testActionIndex_WeeklyDateFilter_RendersIndexView()
	{
		$dateFilter = 'week';
		$_POST['date-filter'] = $dateFilter;
		
		$mockController = $this->getMock('TheatreController', array('render'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index', array(
				'theatres'=>array(),
				'siteId' => null,
				'serviceId' => null,
				'firmId' => null,
				'theatreId' => null,
				'dateFilter' => $dateFilter,
				'theatreList' => array(),
				'firmList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}

	// @todo: figure out how to generate expected theatre data so this test reliably passes
//	public function testActionIndex_MonthlyDateFilter_RendersIndexView()
//	{
//		$dateFilter = 'month';
//		$_POST['date-filter'] = $dateFilter;
//		
//		$mockController = $this->getMock('TheatreController', array('render'),
//			array('TheatreController'));
//		$mockController->expects($this->any())
//			->method('render')
//			->with('index', array(
//				'theatres'=>array(),
//				'siteId' => null,
//				'serviceId' => null,
//				'firmId' => null,
//				'theatreId' => null,
//				'dateFilter' => $dateFilter,
//				'theatreList' => array(),
//				'firmList' => array(),
//				'dateStart' => null,
//				'dateEnd' => null,
//			));
//		$this->assertNull($mockController->actionIndex());
//	}

	// @todo: figure out how to generate expected theatre data so this test reliably passes
//	public function testActionIndex_CustomDateFilter_RendersIndexView()
//	{
//		$dateFilter = 'custom';
//		$dateStart = date('Y-m-d');
//		$dateEnd = date('Y-m-d', strtotime('+10 days'));
//		$_POST['date-filter'] = $dateFilter;
//		$_POST['date-start'] = $dateStart;
//		$_POST['date-end'] = $dateEnd;
//		
//		$mockController = $this->getMock('TheatreController', array('render'),
//			array('TheatreController'));
//		$mockController->expects($this->any())
//			->method('render')
//			->with('index', array(
//				'theatres'=>array(),
//				'siteId' => null,
//				'serviceId' => null,
//				'firmId' => null,
//				'theatreId' => null,
//				'dateFilter' => $dateFilter,
//				'theatreList' => array(),
//				'firmList' => array(),
//				'dateStart' => $dateStart,
//				'dateEnd' => $dateEnd,
//			));
//		$this->assertNull($mockController->actionIndex());
//	}
	
	public function testActionFilterFirms_NoPostData_ListsOneFirm()
	{
		$mockController = $this->getMock('TheatreController', array('getFilteredFirms'),
			array('TheatreController'));
		
		$mockController->expects($this->never())
			->method('getFilteredFirms');
		$this->assertNull($mockController->actionFilterFirms());
	}
	
	public function testActionFilterFirms_ValidServiceId_ListsAllFirms()
	{
		$serviceId = $this->serviceSpecialtyAssignments['servicespecialtyassignment1']['service_id'];
		$_POST['service_id'] = $serviceId;
		$mockController = $this->getMock('TheatreController', array('getFilteredFirms'),
			array('TheatreController'));
		
		$firmList = array($this->firms['firm1']['id'] => $this->firms['firm1']['name']);
		
		$mockController->expects($this->once())
			->method('getFilteredFirms')
			->with($serviceId)
			->will($this->returnValue($firmList));
		$this->assertNull($mockController->actionFilterFirms());
	}
	
	public function testActionFilterTheatres_NoPostData_ListsOneTheatre()
	{
		$mockController = $this->getMock('TheatreController', array('getFilteredTheatres'),
			array('TheatreController'));
		
		$mockController->expects($this->never())
			->method('getFilteredTheatres');
		$this->assertNull($mockController->actionFilterTheatres());
	}
	
	public function testActionFilterTheatres_ValidSiteId_ListsAllTheatres()
	{
		$_POST['site_id'] = $this->sites['site1']['id'];
		
		$theatre1 = $this->theatres['theatre1'];
		$theatre2 = $this->theatres['theatre2'];
		
		$theatreList = array(
			$theatre1['id'] => $theatre1['name'],
			$theatre2['id'] => $theatre2['name'],
		);
		
		$mockController = $this->getMock('TheatreController', array('getFilteredTheatres'),
			array('TheatreController'));
		
		$mockController->expects($this->once())
			->method('getFilteredTheatres')
			->with($_POST['site_id'])
			->will($this->returnValue($theatreList));
		$this->assertNull($mockController->actionFilterTheatres());
	}
}