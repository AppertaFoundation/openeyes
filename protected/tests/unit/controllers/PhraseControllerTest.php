<?php
class PhraseControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'sections' => 'Section',
		'sectionTypes' => 'SectionType',
		'phrases' => 'Phrase',
		'phrasesBySpecialty' => 'PhraseBySpecialty',
		'phrasesByFirm' => 'PhraseByFirm',
		'firms' => 'Firm',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new PhraseController('PhraseController');
		parent::setUp();
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('PhraseController', array('render'),
			array('PhraseController'));
		$mockController->expects($this->any())
			->method('render')
			->with('index');
		$mockController->actionIndex();
	}

	public function testActionView_InvalidData_ThrowsException()
	{
		$fakeId = 9999;

		$this->setExpectedException('CHttpException', 'The requested page does not exist.');
		$this->controller->actionView($fakeId);
	}
	
	public function testActionView_ValidData_RendersViewView()
	{
		$itemId = $this->phrases['phrase1']['id'];
		$itemPhrase = $this->phrases['phrase1']['phrase'];
		
		$mockController = $this->getMock('PhraseController', array('render'), array('PhraseController'));
		$mockController->expects($this->any())
			->method('render')
			->with('view', array('model' => $this->phrases('phrase1')));
		
		$mockController->actionView($patientId);
	}
	
	public function testActionSearch_NoPostData_RendersSearchView()
	{
		$phrase = new Phrase;
		
		$mockController = $this->getMock('PhraseController', array('render', 'forward'), array('PhraseController'));
		$mockController->expects($this->never())
			->method('forward');
		$mockController->expects($this->once())
			->method('render')
			->with('search', array('model' => $phrase));
		
		$mockController->actionSearch();
	}
	
	public function testActionSearch_ValidPostData_ForwardsToResults()
	{
		$phrase = new Phrase;
		
		$_POST['Patient'] = $this->phrases['phrase1'];
		
		$mockController = $this->getMock('PhraseController', array('render', 'forward'), array('PhraseController'));
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
	
	public function testActionResults_RendersResultsView()
	{
		$_POST = array();
		$_POST['Patient'] = $this->phrases['phrase1'];
		$phrase = new Phrase;
		$patient->attributes = $_POST['Patient'];
		$data = $patient->search();
		
		$mockController = $this->getMock('PhraseController', 
			array('getSearch', 'render'), array('PhraseController'));
		$mockController->expects($this->once())
			->method('getSearch')
			->with($_POST['Patient'])
			->will($this->returnValue($data));
		$mockController->expects($this->once())
			->method('render')
			->with('results', array('dataProvider' => $data));
		
		$mockController->actionResults();
	}
	
	public function testGetSearch_ReturnsCorrectData()
	{
		$_POST['Patient'] = $this->phrases['phrase1'];
		$phrase = new Phrase;
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
		$mockController = $this->getMock('PhraseController', array('render'), array('PhraseController'));
		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $patient));
		
		$mockController->actionAdmin();
	}
	
	public function testActionAdmin_ValidGetParameters_RendersAdminView()
	{
		$_GET['Patient'] = $this->phrases['phrase1'];
		$patient = new Patient('search');
		$patient->unsetAttributes();
		$patient->attributes = $_GET['Patient'];
		$mockController = $this->getMock('PhraseController', array('render'), array('PhraseController'));
		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $patient));
		
		$mockController->actionAdmin();
	}
}
