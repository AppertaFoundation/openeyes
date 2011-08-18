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
		'patients' => 'Patient',
		'wards' => 'Ward'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new TheatreController('TheatreController');
		parent::setUp();
		ob_start();
	}
	
	protected function tearDown()
	{
		ob_end_clean();
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
				'wardId' => null,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => array(),
				'wardList' => array(),
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
		
		$ward1 = $this->wards['ward1'];
		$ward2 = $this->wards['ward2'];
		$ward3 = $this->wards['ward3'];
		$ward4 = $this->wards['ward4'];
		
		$wardList = array(
			$ward1['id'] => $ward1['name'],
			$ward2['id'] => $ward2['name'],
			$ward3['id'] => $ward3['name'],
			$ward4['id'] => $ward4['name']
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
				'wardId' => null,
				'dateFilter' => null,
				'theatreList' => $theatreList,
				'firmList' => array(),
				'wardList' => $wardList,
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
				'wardId' => null,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => array(),
				'wardList' => array(),
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
				'wardId' => null,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => $firmList,
				'wardList' => array(),
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
				'wardId' => null,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => array(),
				'wardList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}

	public function testActionIndex_WithWardId_RendersIndexView()
	{
		$wardId = $this->wards['ward1']['id'];
		$_POST['ward-id'] = $wardId;
		
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
				'wardId' => $wardId,
				'dateFilter' => null,
				'theatreList' => array(),
				'firmList' => array(),
				'wardList' => array(),
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
				'wardId' => null,
				'dateFilter' => $dateFilter,
				'theatreList' => array(),
				'firmList' => array(),
				'wardList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}
/*
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
				'wardId' => null,
				'dateFilter' => $dateFilter,
				'theatreList' => array(),
				'wardList' => array(),
				'firmList' => array(),
				'dateStart' => null,
				'dateEnd' => null,
			));
		$this->assertNull($mockController->actionIndex());
	}
*/
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
		$results = ob_get_contents();
		
		$expected = CHtml::tag('option', array('value'=>''), 
			CHtml::encode('All firms'), true);
		
		$this->assertEquals($expected, $results, 'Output should match.');
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
		$results = ob_get_contents();
		
		$expected = CHtml::tag('option', array('value'=>''), 
			CHtml::encode('All firms'), true);
		foreach ($firmList as $id => $name) {
			$expected .= CHtml::tag('option', array('value'=>$id), 
				CHtml::encode($name), true);
		}
		
		$this->assertEquals($expected, $results, 'Output should match.');
	}
	
	public function testActionFilterTheatres_NoPostData_ListsOneTheatre()
	{
		$mockController = $this->getMock('TheatreController', array('getFilteredTheatres'),
			array('TheatreController'));
		
		$mockController->expects($this->never())
			->method('getFilteredTheatres');
		$this->assertNull($mockController->actionFilterTheatres());
		$results = ob_get_contents();
		
		$expected = CHtml::tag('option', array('value'=>''), 
			CHtml::encode('All theatres'), true);
		
		$this->assertEquals($expected, $results, 'Output should match.');
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
		$results = ob_get_contents();
		
		$expected = CHtml::tag('option', array('value'=>''), 
			CHtml::encode('All theatres'), true);
		foreach ($theatreList as $id => $name) {
			$expected .= CHtml::tag('option', array('value'=>$id), 
				CHtml::encode($name), true);
		}
		
		$this->assertEquals($expected, $results, 'Output should match.');
	}
	
	public function testActionFilterWards_NoPostData_ListsOneWard()
	{
		$mockController = $this->getMock('TheatreController', array('getFilteredWards'),
			array('TheatreController'));
		
		$mockController->expects($this->never())
			->method('getFilteredWards');
		$this->assertNull($mockController->actionFilterWards());
		$results = ob_get_contents();
		
		$expected = CHtml::tag('option', array('value'=>''), 
			CHtml::encode('All wards'), true);
		
		$this->assertEquals($expected, $results, 'Output should match.');
	}
	
	public function testActionFilterWards_ValidSiteId_ListsAllWards()
	{
		$_POST['site_id'] = $this->sites['site1']['id'];
		
		$ward1 = $this->wards['ward1'];
		$ward2 = $this->wards['ward2'];
		$ward3 = $this->wards['ward3'];
		$ward4 = $this->wards['ward4'];
		
		$wardList = array(
			$ward1['id'] => $ward1['name'],
			$ward2['id'] => $ward2['name'],
			$ward3['id'] => $ward3['name'],
			$ward4['id'] => $ward4['name']
		);
		
		$mockController = $this->getMock('TheatreController', array('getFilteredWards'),
			array('TheatreController'));
		
		$mockController->expects($this->once())
			->method('getFilteredWards')
			->with($_POST['site_id'])
			->will($this->returnValue($wardList));
		$this->assertNull($mockController->actionFilterWards());
		$results = ob_get_contents();
		
		$expected = CHtml::tag('option', array('value'=>''), 
			CHtml::encode('All wards'), true);
		foreach ($wardList as $id => $name) {
			$expected .= CHtml::tag('option', array('value'=>$id), 
				CHtml::encode($name), true);
		}
		
		$this->assertEquals($expected, $results, 'Output should match.');
	}
}
