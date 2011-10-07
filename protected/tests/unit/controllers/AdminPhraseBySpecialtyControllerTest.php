<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class AdminPhraseBySpecialtyControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'sections' => 'Section',
		'sectionTypes' => 'SectionType',
		'phrases' => 'Phrase',
		'phrasesBySpecialty' => 'PhraseBySpecialty',
		'phrasesBySpecialty' => 'PhraseBySpecialty',
		'firms' => 'Firm',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new AdminPhraseBySpecialtyController('AdminPhraseBySpecialtyController');
		parent::setUp();
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('AdminPhraseBySpecialtyController', array('render'),
			array('AdminPhraseBySpecialtyController'));
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
		$itemId = $this->phrasesBySpecialty['phraseBySpecialty1']['id'];
		$itemPhrase = $this->phrasesBySpecialty['phraseBySpecialty1']['phrase'];
		
		$mockController = $this->getMock('AdminPhraseBySpecialtyController', array('render'), array('AdminPhraseBySpecialtyController'));
		$mockController->expects($this->any())
			->method('render')
			->with('view', array('model' => $this->phrasesBySpecialty('phraseBySpecialty1')));
		
		$mockController->actionView($itemId);
	}

	public function testActionAdmin_NoGetParameters_RendersAdminView()
	{
		$item = new PhraseBySpecialty('search');
		$item->unsetAttributes();
		$mockController = $this->getMock('AdminPhraseBySpecialtyController', array('render'), array('AdminPhraseBySpecialtyController'));
		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $item));
		
		$mockController->actionAdmin();
	}
	
	public function testActionAdmin_ValidGetParameters_RendersAdminView()
	{
		$_GET['id'] = $this->phrasesBySpecialty['phraseBySpecialty1']['id'];
		$item = new PhraseBySpecialty('search');
		$item->unsetAttributes();
		$item->attributes = $_GET['id'];
		$mockController = $this->getMock('AdminPhraseBySpecialtyController', array('render'), array('AdminPhraseBySpecialtyController'));
		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $item));
		
		$mockController->actionAdmin();
	}
}
