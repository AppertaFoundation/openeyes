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
		'subspecialties' => 'Subspecialty',
		'firms' => 'Firm',
		'FirmUserAssignments' => 'FirmUserAssignment',
		'users' => 'User',
		'contacts' => 'Contact',
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
	 * @covers Firm::getServiceText
	 */
	public function testGetServiceText()
	{
		$firm = $this->firms('firm1');
		$this->assertEquals($firm->getServiceText(), $firm->serviceSubspecialtyAssignment->service->name);
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
	 */
	public function testGetConsultantName()
	{
		$this->assertEquals('Mr Jim Aylward', $this->firms('firm1')->getConsultantName());
	}

	/**
	 * @covers Firm::getConsultantName
	 */
	public function testGetConsultantName_NoConsultant()
	{
		$this->assertEquals('NO CONSULTANT', $this->firms('firm2')->getConsultantName());
	}

	/**
	 * @covers Firm::getReportDisplay
	 */
	public function testGetReportDisplay()
	{
		$this->assertEquals('Aylward Firm (Subspecialty 1)', $this->firms('firm1')->getReportDisplay());
	}

	/**
	 * @covers Firm::getNameAndSubspecialty
	 */
	public function testGetNameAndSubspecialty()
	{
		$this->assertEquals('Aylward Firm (Subspecialty 1)', $this->firms('firm1')->getNameAndSubspecialty());
	}

	/**
	 * @covers Firm::getSpecialty
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
