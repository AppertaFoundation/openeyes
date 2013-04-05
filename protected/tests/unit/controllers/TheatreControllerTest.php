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

		$mockController->selectedFirmId = $this->firms['firm1']['id'];

		$mockController->expects($this->any())
			->method('render')
			->with('index');
		$this->assertNull($mockController->actionIndex());
	}

	public function testActionSearch_NoPostData_RendersIndexView()
	{
		$mockController = $this->getMock('TheatreController', array('renderPartial'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('renderPartial')
			->with('_list', array('theatres'=>array()), false, true);
		$this->assertNull($mockController->actionSearch());
	}

	public function testActionSearch_WithSiteId_RendersIndexView()
	{
		$_POST['site-id'] = $this->sites['site1']['id'];

		$mockController = $this->getMock('TheatreController', array('renderPartial'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('renderPartial')
			->with('_list', array('theatres'=>array()), false, true);
		$this->assertNull($mockController->actionSearch());
	}

	public function testActionSearch_WithTheatreId_RendersIndexView()
	{
		$theatreId = $this->theatres['theatre1']['id'];
		$_POST['theatre-id'] = $theatreId;

		$mockController = $this->getMock('TheatreController', array('renderPartial'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('renderPartial')
			->with('_list', array('theatres'=>array()), false, true);
		$this->assertNull($mockController->actionSearch());
	}

	public function testActionSearch_WithServiceId_RendersIndexView()
	{
		$serviceId = $this->serviceSpecialtyAssignments['servicespecialtyassignment1']['service_id'];
		$_POST['service-id'] = $serviceId;

		$mockController = $this->getMock('TheatreController', array('renderPartial'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('renderPartial')
			->with('_list', array('theatres'=>array()), false, true);
		$this->assertNull($mockController->actionSearch());
	}
/*
	public function testActionSearch_WithFirmId_RendersIndexView()
	{
		$firmId = $this->firms['firm1']['id'];
		$_POST['firm-id'] = $firmId;

		$mockController = $this->getMock('TheatreController', array('renderPartial'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('renderPartial')
			->with('_list', array('theatres'=>array()), false, true);
		$this->assertNull($mockController->actionSearch());
	}
*/
	public function testActionSearch_WithWardId_RendersIndexView()
	{
		$wardId = $this->wards['ward1']['id'];
		$_POST['ward-id'] = $wardId;

		$mockController = $this->getMock('TheatreController', array('renderPartial'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('renderPartial')
			->with('_list', array('theatres'=>array()), false, true);
		$this->assertNull($mockController->actionSearch());
	}

	public function testActionSearch_DailyDateFilter_RendersIndexView()
	{
		$dateFilter = 'today';
		$_POST['date-filter'] = $dateFilter;

		$mockController = $this->getMock('TheatreController', array('renderPartial'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('renderPartial')
			->with('_list', array('theatres'=>array()), false, true);
		$this->assertNull($mockController->actionSearch());
	}
/*
	public function testActionSearch_WeeklyDateFilter_RendersIndexView()
	{
		$dateFilter = 'week';
		$_POST['date-filter'] = $dateFilter;

		$mockController = $this->getMock('TheatreController', array('renderPartial'),
			array('TheatreController'));
		$mockController->expects($this->any())
			->method('renderPartial')
			->with('_list', array('theatres'=>array()), false, true);
		$this->assertNull($mockController->actionSearch());
	}
*/
	// @todo: figure out how to generate expected theatre data so this test reliably passes
//	public function testActionSearch_MonthlyDateFilter_RendersIndexView()
//	{
//		$dateFilter = 'month';
//		$_POST['date-filter'] = $dateFilter;
//
//		$mockController = $this->getMock('TheatreController', array('renderPartial'),
//			array('TheatreController'));
//		$mockController->expects($this->any())
//			->method('renderPartial')
//			->with('_list', array('theatres'=>array()), false, true);
//		$this->assertNull($mockController->actionSearch());
//	}

	// @todo: figure out how to generate expected theatre data so this test reliably passes
//	public function testActionSearch_CustomDateFilter_RendersIndexView()
//	{
//		$dateFilter = 'custom';
//		$dateStart = date('Y-m-d');
//		$dateEnd = date('Y-m-d', strtotime('+10 days'));
//		$_POST['date-filter'] = $dateFilter;
//		$_POST['date-start'] = $dateStart;
//		$_POST['date-end'] = $dateEnd;
//
//		$mockController = $this->getMock('TheatreController', array('renderPartial'),
//			array('TheatreController'));
//		$mockController->expects($this->any())
//			->method('renderPartial')
//			->with('_list', array('theatres'=>array()), false, true);
//		$this->assertNull($mockController->actionSearch());
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
		$specialtyId = $this->serviceSpecialtyAssignments['servicespecialtyassignment1']['specialty_id'];
		$_POST['specialty_id'] = $specialtyId;
		$mockController = $this->getMock('TheatreController', array('getFilteredFirms'),
			array('TheatreController'));

		$firmList = array($this->firms['firm1']['id'] => $this->firms['firm1']['name']);

		$mockController->expects($this->once())
			->method('getFilteredFirms')
			->with($specialtyId)
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
