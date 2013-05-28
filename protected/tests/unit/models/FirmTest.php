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
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'FirmUserAssignments' => 'FirmUserAssignment',
		'users' => 'User',
		'userContactAssignment' => 'UserContactAssignment',
		'contacts' => 'Contact',
		'consultants' => 'Consultant'
	);

	public function testGetServicespecialtyOptions()
	{
		$serviceSpecialties = Firm::model()->getServiceSpecialtyOptions();
		$this->assertTrue(is_array($serviceSpecialties));
		$this->assertEquals(count($this->serviceSpecialtyAssignment), count($serviceSpecialties));
	}

	public function testGetServiceText()
	{
		$firm = $this->firms('firm1');
		$this->assertEquals($firm->getServiceText(), 'Accident and Emergency Service');
	}

	public function testGetSpecialtyText()
	{
		$firm = $this->firms('firm1');
		$this->assertEquals($firm->getSpecialtyText(), 'Accident & Emergency');
	}

	public function testGetConsultant()
	{
		$firm = $this->firms('firm1');

		$consultant = $firm->getConsultant();

		$this->assertEquals($consultant->contact->nick_name, 'Aylward');
	}
}
