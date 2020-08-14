<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class MeasurementReferenceTest extends ActiveRecordTestCase
{
    public $fixtures = array(
        'ep' => 'Episode',
        'ev' => 'Event',
        'ref' => 'MeasurementReference',
        'pm' => 'PatientMeasurement',
    );

    public function getModel()
    {
        return  MeasurementReference::model();
    }

    /**
     * @covers MeasurementReference
     * @throws Exception
     */
    public function testDuplicateForEpisode()
    {
        $this->expectExceptionMessage("Measurement reference already exists from episode");
        $this->expectException(Exception::class);
        $ref = new MeasurementReference();
        $ref->patient_measurement_id = $this->pm('height')->id;
        $ref->episode_id = 1;
        $ref->save();
    }

    /**
     * @covers MeasurementReference
     * @throws Exception
     */
    public function testDuplicateForEvent()
    {
        $this->expectExceptionMessage("Measurement reference already exists from event");
        $this->expectException(Exception::class);
        $ref = new MeasurementReference();
        $ref->patient_measurement_id = $this->pm('height')->id;
        $ref->event_id = 1;
        $ref->save();
    }

    /**
     * @covers MeasurementReference
     * @throws Exception
     */
    public function testOriginAlreadyExists()
    {
        $this->expectExceptionMessage("Origin reference already exists");
        $this->expectException(Exception::class);
        $ref = new MeasurementReference();
        $ref->patient_measurement_id = $this->pm('height')->id;
        $ref->episode_id = 2;
        $ref->origin = true;
        $ref->save();
    }

    /**
     * @covers MeasurementReference
     * @throws Exception
     */
    public function testEpisodeIdAndEventIdSet()
    {
        $this->expectExceptionMessage("Measurement references cannot reference both an episode and an event");
        $this->expectException(Exception::class);
        $ref = new MeasurementReference();
        $ref->patient_measurement_id = $this->pm('height')->id;
        $ref->episode_id = 2;
        $ref->event_id = 2;
        $ref->save();
    }
}
