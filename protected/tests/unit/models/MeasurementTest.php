<?php

use PHPUnit\Framework\MockObject\MockObject;

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
class MeasurementTest extends CDbTestCase
{
    public $fixtures = array(
        'measurement_type' => 'MeasurementType',
        'patient' => 'Patient',
        'patient_measurement' => 'PatientMeasurement',
    );

    /**
     * @covers MeasurementType
     */
    public function testGetMeasurementType()
    {
        $measurement = new MeasurementTest_HeightMeasurement();

        $this->assertEquals(
            $this->measurement_type('height'),
            $measurement->getMeasurementType()
        );
    }

    /**
     * @covers MeasurementType
     */
    public function testGetPatientMeasurement_NewRecord()
    {
        $measurement = new MeasurementTest_HeightMeasurement(true);

        $patient_measurement = $measurement->getPatientMeasurement();
        $this->assertTrue($patient_measurement->getIsNewRecord());
        $this->assertEquals($this->measurement_type('height'), $patient_measurement->type);
    }

    /**
     * @covers MeasurementType
     */
    public function testGetPatientMeasurement_ExistingRecord()
    {
        $measurement = new MeasurementTest_HeightMeasurement(false);
        $measurement->patient_measurement_id = $this->patient_measurement('height')->id;

        $this->assertEquals($this->patient_measurement('height'), $measurement->getPatientMeasurement());
    }
}

class MeasurementTest_HeightMeasurement extends Measurement
{
    public $patient_measurement_id;

    public function __construct($new = true)
    {
        $this->setIsNewRecord($new);
    }

    /**
     * @return CActiveRecordMetaData|MockObject
     * @throws ReflectionException
     */
    public function getMetadata()
    {
        return (new PHPUnit\Framework\MockObject\Generator())->getMock('CActiveRecordMetaData', array(), array(), '', false);
    }
}
