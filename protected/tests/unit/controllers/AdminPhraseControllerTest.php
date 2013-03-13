<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
