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
 * Class PatientControllerTest
 * @group controllers
 */
class PatientControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'patients' => 'Patient',
		'firms' => 'Firm',
		'episodes' => 'Episode'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new PatientController('PatientController');
		parent::setUp();
	}

	public function test_MarkIncomplete()
	{
		$this->markTestIncomplete('Tests not implemented yet');
	}

	/*
	public function testActionIndex_RendersIndexView()
	{
		$this->markTestSkipped('May not be required');
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
/*
	public function testActionView_ValidPatient_RendersViewView()
	{
		$patientId = $this->patients['patient1']['id'];
		$patientName = $this->patients['patient1']['title'] . ' ' .
			$this->patients['patient1']['first_name'] . ' ' .
			$this->patients['patient1']['last_name'];

		$episodes = array();
		$episodes_open = 0;
		$episodes_closed = 0;

		foreach ($this->episodes as $ep) {
			if ($ep['patient_id'] == 1) {
				$episode = Episode::model()->findByPk($ep['id']);
				array_push($episodes, $episode);

				if ($episode->end_date === null) {
					$episodes_open++;
				} else {
					$episodes_closed++;
				}
			}
		}

		$mockController = $this->getMock('PatientController', array('render'), array('PatientController'));
		$mockController->expects($this->any())
			->method('render')
			->with('view', array('model' => $this->patients('patient1'), 'tab' => 0, 'event' => 0, 'episodes' => $episodes, 'episodes_open' => $episodes_open, 'episodes_closed' => $episodes_closed));

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
		/*
		$mockController->expects($this->any())
			->method('render');

		$mockController->actionSearch();
	}

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
	*/
}
