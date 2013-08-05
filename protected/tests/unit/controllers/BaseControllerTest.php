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

class BaseControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'patients' => 'Patient',
		'firms' => 'Firm',
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new BaseController('BaseController');
		parent::setUp();
	}

	public function testCheckPatientId_EmptySessionData_ThrowsException()
	{
		$this->setExpectedException('CHttpException', 'You are not authorised to perform this action.');
		$this->controller->checkPatientId();
	}

	public function testCheckPatientId_ValidSessionData_StoresPatientId()
	{
		$this->assertNull($this->controller->patientId, 'Patient name should not be set originally.');
		$this->assertNull($this->controller->patientName, 'Patient name should not be set originally.');

		$patientId = $this->patients['patient1']['id'];
		$patientName = "{$this->patients['patient1']['first_name']} {$this->patients['patient1']['last_name']}";
		Yii::app()->session['patient_id'] = $patientId;
		Yii::app()->session['patient_name'] = $patientName;

		$this->controller->checkPatientId();

		$this->assertEquals($patientId, $this->controller->patientId, 'Patient Id should be set after the function call.');
		$this->assertEquals($patientName, $this->controller->patientName, 'Patient Name should be set after the function call.');
	}

	public function testStoreData_EmptySession_StoresNothing()
	{
		$this->assertFalse($this->controller->showForm, 'showForm should default to false.');
		$this->assertNull($this->controller->firms, 'Firms should default to null.');
		$this->assertNull($this->controller->selectedFirmId, 'Selected Firm Id should default to null.');
		$this->assertNull($this->controller->patientName, 'Patient Name should default to null.');
		$this->assertNull($this->controller->storeData(), 'This function should have no return.');

		$this->assertFalse($this->controller->showForm, 'showForm should still be false.');
		$this->assertNull($this->controller->firms, 'Firms should still be null.');
		$this->assertNull($this->controller->selectedFirmId, 'Selected Firm Id should still be null.');
		$this->assertNull($this->controller->patientName, 'Patient Name should still be null.');
	}

	public function testStoreData_FirmDataInSession_StoresFirms()
	{
		$this->assertFalse($this->controller->showForm, 'showForm should default to false.');
		$this->assertNull($this->controller->firms, 'Firms should default to null.');
		$this->assertNull($this->controller->selectedFirmId, 'Selected Firm Id should default to null.');
		$this->assertNull($this->controller->patientName, 'Patient Name should default to null.');

		$firms = array();
		foreach ($this->firms as $name => $values) {
			$firms[$values['id']] = $values['name'] .
						' (' . $values['pas_code'] . ') (' .
						$this->firms($name)->serviceSpecialtyAssignment->service->name .')';
		}

		Yii::app()->session['firms'] = $firms;
		$this->assertNull($this->controller->storeData(), 'This function should have no return.');

		$this->assertTrue($this->controller->showForm, 'showForm should now be true.');
		$this->assertEquals($firms, $this->controller->firms, 'Firms should now match session data.');
		$this->assertNull($this->controller->selectedFirmId, 'Selected Firm Id should still be null.');
		$this->assertNull($this->controller->patientName, 'Patient Name should still be null.');
	}

	public function testStoreData_FirmIdInSession_StoresFirmId()
	{
		$this->assertFalse($this->controller->showForm, 'showForm should default to false.');
		$this->assertNull($this->controller->firms, 'Firms should default to null.');
		$this->assertNull($this->controller->selectedFirmId, 'Selected Firm Id should default to null.');
		$this->assertNull($this->controller->patientName, 'Patient Name should default to null.');

		$firmId = $this->firms['firm1']['id'];
		$firms = array();
		foreach ($this->firms as $name => $values) {
			$firms[$values['id']] = $values['name'] .
						' (' . $values['pas_code'] . ') (' .
						$this->firms($name)->serviceSpecialtyAssignment->service->name .')';
		}

		Yii::app()->session['firms'] = $firms;
		Yii::app()->session['selected_firm_id'] = $firmId;
		$this->assertNull($this->controller->storeData(), 'This function should have no return.');

		$this->assertTrue($this->controller->showForm, 'showForm should now be true.');
		$this->assertEquals($firms, $this->controller->firms, 'Firms should now match session data.');
		$this->assertEquals($firmId, $this->controller->selectedFirmId, 'Firm Id should now match session data.');
		$this->assertNull($this->controller->patientName, 'Patient Name should still be null.');
	}

	public function testStoreData_PatientNameInSession_StoresPatientName()
	{
		$this->assertFalse($this->controller->showForm, 'showForm should default to false.');
		$this->assertNull($this->controller->firms, 'Firms should default to null.');
		$this->assertNull($this->controller->selectedFirmId, 'Selected Firm Id should default to null.');
		$this->assertNull($this->controller->patientName, 'Patient Name should default to null.');

		$patientName = "{$this->patients['patient1']['first_name']} {$this->patients['patient1']['last_name']}";

		Yii::app()->session['patient_name'] = $patientName;
		$this->assertNull($this->controller->storeData(), 'This function should have no return.');

		$this->assertFalse($this->controller->showForm, 'showForm should still be false.');
		$this->assertNull($this->controller->firms, 'Firms should still be null.');
		$this->assertNull($this->controller->selectedFirmId, 'Selected Firm Id should still be null.');
		$this->assertEquals($patientName, $this->controller->patientName, 'Patient Name should now match session data.');
	}

	public function testStoreData_FirmAndPatientInSession_StoresAllData()
	{
		$this->assertFalse($this->controller->showForm, 'showForm should default to false.');
		$this->assertNull($this->controller->firms, 'Firms should default to null.');
		$this->assertNull($this->controller->selectedFirmId, 'Selected Firm Id should default to null.');
		$this->assertNull($this->controller->patientName, 'Patient Name should default to null.');

		$firmId = $this->firms['firm1']['id'];
		$firms = array();
		foreach ($this->firms as $name => $values) {
			$firms[$values['id']] = $values['name'] .
						' (' . $values['pas_code'] . ') (' .
						$this->firms($name)->serviceSpecialtyAssignment->service->name .')';
		}

		$patientName = "{$this->patients['patient1']['first_name']} {$this->patients['patient1']['last_name']}";

		Yii::app()->session['firms'] = $firms;
		Yii::app()->session['selected_firm_id'] = $firmId;
		Yii::app()->session['patient_name'] = $patientName;
		$this->assertNull($this->controller->storeData(), 'This function should have no return.');

		$this->assertTrue($this->controller->showForm, 'showForm should now be true.');
		$this->assertEquals($firms, $this->controller->firms, 'Firms should now match session data.');
		$this->assertEquals($firmId, $this->controller->selectedFirmId, 'Firm Id should now match session data.');
		$this->assertEquals($patientName, $this->controller->patientName, 'Patient Name should now match session data.');
	}
}
