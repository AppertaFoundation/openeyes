<?php
class PatientControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'patients' => 'Patient',
		'firms' => 'Firm',
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new PatientController('PatientController');
		parent::setUp();
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('PatientController', array('render'),
			array('PatientController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index');
		$mockController->actionIndex();
	}

	public function testActionView_InvalidPatient_ThrowsException()
	{
		$fakeId = 5829;

		$this->setExpectedException('CHttpException', 'The requested page does not exist.');
		$this->controller->actionView($fakeId);
	}
	
	public function testActionView_ValidPatient_RendersViewView()
	{
		$patientId = $this->patients['patient1']['id'];
		$patientName = $this->patients['patient1']['title'] . ' ' . 
			$this->patients['patient1']['first_name'] . ' ' .
			$this->patients['patient1']['last_name'];
		
		$mockController = $this->getMock('PatientController', array('render'), array('PatientController'));
		$mockController->expects($this->any())
			->method('render')
			->with('view', array('model' => $this->patients('patient1')));
		
		$mockController->actionView($patientId);
		$this->assertEquals($patientId, Yii::app()->session['patient_id'], 'Patient id should be stored in the session.');
		$this->assertEquals($patientName, Yii::app()->session['patient_name'], 'Patient name should be stored in the session.');
	}
	
	public function testActionSearch_NoPostData_RendersSearchView()
	{
		$patient = new Patient;
		
		$mockController = $this->getMock('PatientController', array('render', 'forward'), array('PatientController'));
		$mockController->expects($this->never())
			->method('forward');
		$mockController->expects($this->once())
			->method('render')
			->with('search', array('model' => $patient));
		
		$mockController->actionSearch();
	}
	
	public function testActionSearch_ValidPostData_ForwardsToResults()
	{
		$patient = new Patient;
		
		$_POST['Patient'] = $this->patients['patient1'];
		
		$mockController = $this->getMock('PatientController', array('render', 'forward'), array('PatientController'));
		$mockController->expects($this->once())
			->method('forward')
			->with('results');
		/* note: render expects should really be never(), but because the 
		 * forward() is mocked out, it doesn't actually forward when the test runs
		 */
		$mockController->expects($this->any())
			->method('render');
		
		$mockController->actionSearch();
	}
/*
	public function testActionResults_RendersResultsView()
	{
		$_POST = array();
		$_POST['Patient'] = $this->patients['patient1'];
		$patient = new Patient;
		$patient->attributes = $_POST['Patient'];
		$data = $patient->search();
		
		$mockController = $this->getMock('PatientController', 
			array('getSearch', 'render'), array('PatientController'));
		$mockController->expects($this->once())
			->method('getSearch')
			->with($_POST['Patient'])
			->will($this->returnValue($data));
		$mockController->expects($this->once())
			->method('render')
			->with('results', array('dataProvider' => $data));
		
		$mockController->actionResults();
	}
*/	
	public function testGetSearch_ReturnsCorrectData()
	{
		$_POST['Patient'] = $this->patients['patient1'];
		$patient = new Patient;
		$patient->attributes = $_POST['Patient'];
		$data = $patient->search();
		
		$expected = $data->getData();
		$results = $this->controller->getSearch($_POST['Patient']);
		$this->assertEquals($expected, $results->getData(), 'Search data should match.');
	}
	
	public function testActionAdmin_NoGetParameters_RendersAdminView()
	{
		$patient = new Patient('search');
		$patient->unsetAttributes();
		$mockController = $this->getMock('PatientController', array('render'), array('PatientController'));
		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $patient));
		
		$mockController->actionAdmin();
	}
	
	public function testActionAdmin_ValidGetParameters_RendersAdminView()
	{
		$_GET['Patient'] = $this->patients['patient1'];
		$patient = new Patient('search');
		$patient->unsetAttributes();
		$patient->attributes = $_GET['Patient'];
		$mockController = $this->getMock('PatientController', array('render'), array('PatientController'));
		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $patient));
		
		$mockController->actionAdmin();
	}
}
