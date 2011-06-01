<?php
class AdminPhraseControllerTest extends CDbTestCase
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
		$this->controller = new AdminPhraseController('AdminPhraseController');
		parent::setUp();
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('AdminPhraseController', array('render'), array('AdminPhraseController'));
		$mockController->expects($this->any())->method('render')->with('index');
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
		
		$mockController = $this->getMock('AdminPhraseController', array('render'), array('AdminPhraseController'));
		$mockController->expects($this->any())
			->method('render')
			->with('view', array('model' => $this->phrases('phrase1')));
		
		$mockController->actionView($itemId);
	}

	public function testActionAdmin_NoGetParameters_RendersAdminView()
	{
		$item = new Phrase('search');
		$item->unsetAttributes();
		$mockController = $this->getMock('AdminPhraseController', array('render'), array('AdminPhraseController'));
		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $item));
		
		$mockController->actionAdmin();
	}
	
	public function testActionAdmin_ValidGetParameters_RendersAdminView()
	{
		$_GET['id'] = $this->phrases['phrase1']['id'];
		$item = new Phrase('search');
		$item->unsetAttributes();
		$item->attributes = $_GET['id'];
		$mockController = $this->getMock('AdminPhraseController', array('render'), array('AdminPhraseController'));
		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $item));
		
		$mockController->actionAdmin();
	}
}
