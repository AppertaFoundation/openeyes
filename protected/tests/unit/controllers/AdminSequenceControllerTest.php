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

class AdminSequenceControllerTest extends CDbTestCase
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
		$this->controller = new AdminSequenceController('AdminSequenceController');
		parent::setUp();
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('AdminSequenceController', array('render'),
			array('AdminSequenceController'));
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

		$mockController = $this->getMock('AdminSequenceController', array('render'), array('AdminSequenceController'));

		$mockController->expects($this->once())
			->method('render')
			->with('view', array('model' => $sequence));

		$mockController->actionView($sequence->id);
	}

	public function testActionCreate_NoPostData_RendersCreateView()
	{
		$sequence = new Sequence;
		$firm = new SequenceFirmAssignment;

		$mockController = $this->getMock('AdminSequenceController', array('render'), array('AdminSequenceController'));

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

		$mockController = $this->getMock('AdminSequenceController',
			array('render', 'redirect'), array('AdminSequenceController'));

		$mockController->expects($this->never())
			->method('redirect');

		$mockController->expects($this->once())
			->method('render')
			->with('create', array('model'=>$sequence,'firm'=>$firmAssignment));

		$mockController->actionCreate();

		$requiredFields = array('end_time', 'repeat_interval', 'start_date', 'start_time', 'theatre_id');

		$this->assertEquals(array(), array_diff($requiredFields, array_keys($sequence->getErrors())));
		$this->assertEquals(array(), $firmAssignment->getErrors());
	}

	public function testActionCreate_ValidPostData_RendersViewView()
	{
		$sequenceData = $this->sequences['sequence1'];
		$sequenceData['start_date'] = date('Y-m-d', strtotime('+3 days'));

		$maxId = $this->sequences['sequence3']['id'];

		$_POST['Sequence'] = $sequenceData;
		$_POST['SequenceFirmAssignment'] = $this->sequenceFirmAssignments['sfa1'];
		$_POST['action'] = 'create';

		$mockController = $this->getMock('AdminSequenceController',
			array('render', 'redirect'), array('AdminSequenceController'));

		$mockController->expects($this->once())
			->method('redirect');

		$mockController->actionCreate();
	}

	public function testActionUpdate_NoPostData_RendersCreateView()
	{
		$sequence = $this->sequences('sequence1');
		$sequence->sequenceFirmAssignment;
		$firm = $this->sequenceFirmAssignments('sfa1');

		$mockController = $this->getMock('AdminSequenceController', array('render'), array('AdminSequenceController'));

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
		$firmAssignment = $sequence->sequenceFirmAssignment;
		$sequence->validate();
//		$firmAssignment->validate();

		$mockController = $this->getMock('AdminSequenceController',
			array('render', 'redirect'), array('AdminSequenceController'));

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

		$mockController = $this->getMock('AdminSequenceController',
			array('render', 'redirect'), array('AdminSequenceController'));

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

		$mockController = $this->getMock('AdminSequenceController',
			array('render'), array('AdminSequenceController'));

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

		$mockController = $this->getMock('AdminSequenceController',
			array('render'), array('AdminSequenceController'));

		$mockController->expects($this->any())
			->method('render')
			->with('admin', array('model' => $sequence));

		$mockController->actionAdmin();
	}
}