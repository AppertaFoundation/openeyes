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

class FirmTest extends CDbTestCase
{

	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'serviceSubspecialtyAssignment' => 'ServiceSubspecialtyAssignment',
		'firms' => 'Firm',
		'FirmUserAssignments' => 'FirmUserAssignment',
		'users' => 'User',
		//'userContactAssignment' => 'UserContactAssignment',
		'contacts' => 'Contact',
		'consultants' => 'Consultant'
	);

	/**
	 * @covers Firm::model
	 * @todo   Implement testModel().
	 */
	public function testModel()
	{
		$this->assertEquals('Firm', get_class(Firm::model()), 'Class name should match model.');
	}

	/**
	 * @covers Firm::tableName
	 * @todo   Implement testTableName().
	 */
	public function testTableName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::search
	 * @todo   Implement testSearch().
	 */
	public function testSearch()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::getServiceSubspecialtyOptions
	 * @todo   Implement testGetServiceSubspecialtyOptions().
	 */
	public function testGetServiceSubspecialtyOptions()
	{
		$this->markTestSkipped(
			'This test has hardcoded references, needs to be fixed by making values dynamic.'
		);
		$serviceSpecialties = Firm::model()->getServiceSubspecialtyOptions();
		$this->assertTrue(is_array($serviceSpecialties));
		$this->assertEquals(count($this->serviceSubspecialtyAssignment), count($serviceSpecialties));
	}

	/**
	 * @covers Firm::getServiceText
	 * @todo   Implement testGetServiceText().
	 */
	public function testGetServiceText()
	{
		$firm = $this->firms('firm1');
		$this->assertEquals($this->services['service1']['name'], 'Accident and Emergency Service');
	}

	/**
	 * @covers Firm::getSubspecialtyText
	 * @todo   Implement testGetSubspecialtyText().
	 */
	public function testGetSubspecialtyText()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::getList
	 * @todo   Implement testGetList().
	 */
	public function testGetList()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::getListWithoutDupes
	 * @todo   Implement testGetListWithoutDupes().
	 */
	public function testGetListWithoutDupes()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::getListWithSpecialties
	 * @todo   Implement testGetListWithSpecialties().
	 */
	public function testGetListWithSpecialties()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::getCataractList
	 * @todo   Implement testGetCataractList().
	 */
	public function testGetCataractList()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::getConsultantName
	 * @todo   Implement testGetConsultantName().
	 */
	public function testGetConsultantName()
	{
		$firm = $this->firms('firm1');
		$this->assertEquals($this->contacts['contact1']['nick_name'], 'Aylward');
	}

	/**
	 * @covers Firm::getReportDisplay
	 * @todo   Implement testGetReportDisplay().
	 */
	public function testGetReportDisplay()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::getNameAndSubspecialty
	 * @todo   Implement testGetNameAndSubspecialty().
	 */
	public function testGetNameAndSubspecialty()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::getSpecialty
	 * @todo   Implement testGetSpecialty().
	 */
	public function testGetSpecialty()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Firm::beforeSave
	 * @todo   Implement testBeforeSave().
	 */
	public function testBeforeSave()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	public function testIsSupportServicesFirm_False()
	{
		$this->assertFalse(Firm::model()->findByPk(1)->isSupportServicesFirm());
	}

	public function testIsSupportServicesFirm_True()
	{
		$this->assertTrue(Firm::model()->findByPk(4)->isSupportServicesFirm());
	}
}
