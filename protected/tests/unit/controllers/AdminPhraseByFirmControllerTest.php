<?php
class AdminPhraseByFirmControllerTest extends CDbTestCase
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
		$this->controller = new AdminPhraseByFirmController('AdminPhraseByFirmController');
		parent::setUp();
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('AdminPhraseByFirmController', array('render'), array('AdminPhraseByFirmController'));
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
		$itemId = $this->phrasesByFirm['phraseByFirm1']['id'];
		$itemPhrase = $this->phrasesByFirm['phraseByFirm1']['phrase'];
		
		$mockController = $this->getMock('AdminPhraseByFirmController', array('render'), array('AdminPhraseByFirmController'));
		$mockController->expects($this->any())->method('render')->with('view', array('model' => $this->phrasesByFirm('phraseByFirm1')));
		
		$mockController->actionView($itemId);
	}

	public function testActionAdmin_NoGetParameters_RendersAdminView()
	{
		$item = new PhraseByFirm('search');
		$item->unsetAttributes();
		$mockController = $this->getMock('AdminPhraseByFirmController', array('render'), array('AdminPhraseByFirmController'));
		$mockController->expects($this->any())->method('render')->with('admin', array('model' => $item));
		
		$mockController->actionAdmin();
	}
								
	public function testActionAdmin_ValidGetParameters_RendersAdminView()
	{
		$_GET['id'] = $this->phrasesByFirm['phraseByFirm1']['id'];
		$item = new PhraseByFirm('search');
		$item->unsetAttributes();
		$item->attributes = $_GET['id'];
		$mockController = $this->getMock('AdminPhraseByFirmController', array('render'), array('AdminPhraseByFirmController'));
		$mockController->expects($this->any())->method('render')->with('admin', array('model' => $item));
		
		$mockController->actionAdmin();
	}
}
