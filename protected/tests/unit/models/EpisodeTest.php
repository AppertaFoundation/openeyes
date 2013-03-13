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

class EpisodeTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'eventTypes' => 'EventType',
		'elementTypes' => 'ElementType',
		'possibleElementTypes' => 'PossibleElementType',
		'siteElementTypes' => 'SiteElementType',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'events' => 'Event',
	);


	public function testGetBySpecialtyAndPatient_InvalidParameters_ReturnsFalse()
	{
		$specialtyId = 9278589128;
		$patientId = 2859290852;

		$result = Episode::model()->getBySpecialtyAndPatient($specialtyId, $patientId);

		$this->assertNull($result);
	}

	public function testGetBySpecialtyAndPatient_ValidParameters_ReturnsCorrectData()
	{
		$specialty = $this->specialties('specialty1');
		$patient = $this->patients('patient1');

		$expected = $this->episodes('episode1');

		$result = Episode::model()->getBySpecialtyAndPatient($specialty->id, $patient->id);

		$this->assertEquals(get_class($result), 'Episode');
		$this->assertEquals($expected, $result);
	}
}
