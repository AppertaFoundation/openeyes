<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class MeasurementReferenceTest extends CDbTestCase
{
	public $fixtures = array(
		'ep' => 'Episode',
		'ev' => 'Event',
		'ref' => 'MeasurementReference',
		'pm' => 'PatientMeasurement',
	);

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Measurement reference already exists from episode
	 */
	public function testDuplicateForEpisode()
	{
		$ref = new MeasurementReference;
		$ref->patient_measurement_id = $this->pm('height')->id;
		$ref->episode_id = 1;
		$ref->save();
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Measurement reference already exists from event
	 */
	public function testDuplicateForEvent()
	{
		$ref = new MeasurementReference;
		$ref->patient_measurement_id = $this->pm('height')->id;
		$ref->event_id = 1;
		$ref->save();
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Origin reference already exists
	 */
	public function testOriginAlreadyExists()
	{
		$ref = new MeasurementReference;
		$ref->patient_measurement_id = $this->pm('height')->id;
		$ref->episode_id = 2;
		$ref->origin = true;
		$ref->save();
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Measurement references cannot reference both an episode and an event
	 */
	public function testEpisodeIdAndEventIdSet()
	{
		$ref = new MeasurementReference;
		$ref->patient_measurement_id = $this->pm('height')->id;
		$ref->episode_id = 2;
		$ref->event_id = 2;
		$ref->save();
	}
}
