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

/**
 * Class ProcedureControllerTest
 * @group controllers
 */
class ProcedureControllerTest extends CDbTestCase
{

	/*
	public $fixtures = array(
		'procedures' => 'Procedure',
		'specialties' => 'Specialty',
		'services' => 'Service',
		'subsections' => 'SpecialtySubsection'
	);
	*/

	protected $controller;

	protected function setUp()
	{
		$this->controller = new ProcedureController('ProcedureController');
		parent::setUp();
	}

	public function test_MarkIncomplete()
	{
		$this->markTestIncomplete('Tests not implemented yet');
	}

	/*
	public function testActionDetails_EmptySession_RendersNothing()
	{
		Yii::app()->session['Procedures'] = null;

		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionDetails();
	}

	public function testActionDetails_ValidSessionData_NonMatchingTerm_RendersNothing()
	{
		$session = array();
		foreach ($this->procedures as $procedure) {
			$session[$procedure['id']] = array(
				'term' => $procedure['term'],
				'short_format' => $procedure['short_format'],
				'duration' => $procedure['default_duration'],
			);
		}
		Yii::app()->session['Procedures'] = $session;

		$_GET['name'] = 'Bar Procedure';

		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionDetails();
	}

	public function testActionDetails_NoSessionData_TermInDb_RendersAjaxPartial()
	{
		$session = array();
		Yii::app()->session['Procedures'] = $session;

		$procedure = $this->procedures['procedure1'];
		$_GET['name'] = $procedure['term'];

		$data = array(
			'term' => $procedure['term'],
			'short_format' => $procedure['short_format'],
			'duration' => $procedure['default_duration'],
			'id' => $procedure['id'],
		);

		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_ajaxProcedure', array('data' => $data), false, false);
		$mockController->actionDetails();
	}

	public function testActionDetails_ValidSessionData_MatchingTerm_RendersAjaxPartial()
	{
		$session = array();
		foreach ($this->procedures as $procedure) {
			$session[$procedure['id']] = array(
				'term' => $procedure['term'],
				'short_format' => $procedure['short_format'],
				'duration' => $procedure['default_duration'],
			);
		}
		Yii::app()->session['Procedures'] = $session;

		$procedure = $this->procedures['procedure1'];
		$_GET['name'] = $procedure['term'];

		$data = array(
			'term' => $procedure['term'],
			'short_format' => $procedure['short_format'],
			'duration' => $procedure['default_duration'],
			'id' => $procedure['id'],
		);

		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_ajaxProcedure', array('data' => $data), false, false);
		$mockController->actionDetails();
	}

	public function testActionList_MissingSubsection_RendersNothing()
	{
		$_POST = array();

		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionList();
	}

	public function testActionList_ValidSubsection_NoExistingProcedures_RendersAjaxPartial()
	{
		$sectionId = $this->subsections['section1']['id'];
		$_POST['subsection'] = $sectionId;

		$criteria = new CDbCriteria;
		$criteria->select = 't.id, term, short_format';
		$criteria->join = 'LEFT JOIN proc_specialty_subsection_assignment pssa ON t.id = pssa.proc_id';
		$criteria->compare('pssa.specialty_subsection_id', $_POST['subsection']);

		$procedures = Procedure::model()->findAll($criteria);

		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_procedureOptions', array('procedures' => $procedures), false, false);
		$mockController->actionList();
	}

	public function testActionList_ValidSubsection_WithExistingProcedures_RendersAjaxPartial()
	{
		$sectionId = $this->subsections['section1']['id'];
		$procedureName = "{$this->procedures['procedure1']['term']} - {$this->procedures['procedure1']['short_format']}";
		$_POST['subsection'] = $sectionId;
		$_POST['existing'] = array($procedureName);

		$criteria = new CDbCriteria;
		$criteria->select = 't.id, term, short_format';
		$criteria->join = 'LEFT JOIN proc_specialty_subsection_assignment pssa ON t.id = pssa.proc_id';
		$criteria->addNotInCondition("CONCAT_WS(' - ', term, short_format)", array($procedureName));
		$criteria->compare('pssa.specialty_subsection_id', $_POST['subsection']);

		$procedures = Procedure::model()->findAll($criteria);

		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_procedureOptions', array('procedures' => $procedures), false, false);
		$mockController->actionList();
	}
	*/
}
