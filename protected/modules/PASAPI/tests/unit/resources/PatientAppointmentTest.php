<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\tests\unit\resources;

use OEModule\PASAPI\models\PasApiAssignment;
use OEModule\PASAPI\resources\PatientAppointment;

/**
 * @group sample-data
 */
class PatientAppointmentTest extends \PHPUnit_Framework_TestCase
{
    public function getMockResource($resource, $methods = array())
    {
        return $this->getMockBuilder($resource)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function test_save_success()
    {
        $pa = $this->getMockResource(
            PatientAppointment::class,
            array('getAssignment', 'validate', 'startTransaction', 'saveModel', 'audit')
        );

        $papi_ass = $this->getMockBuilder(PasApiAssignment::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getInternal', 'save', 'unlock'))
            ->getMock();

        $pa->expects($this->at(0))
            ->method('getAssignment')
            ->will($this->returnValue($papi_ass));

        $pa->expects($this->at(1))
            ->method('validate')
            ->will($this->returnValue(true));

        $pa->expects($this->at(2))
            ->method('startTransaction')
            ->will($this->returnvalue(null));

        $worklist_patient = \ComponentStubGenerator::generate('WorklistPatient', array('id' => 5));

        $pa->expects($this->once())
            ->method('saveModel')
            ->will($this->returnValue($worklist_patient));

        $papi_ass->expects($this->once())
            ->method('getInternal')
            ->will($this->returnValue($worklist_patient));

        $papi_ass->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $papi_ass->expects($this->once())
            ->method('unlock')
            ->will($this->returnValue(true));

        $this->assertEquals(5, $pa->save());
    }

    public function test_saveModel_update()
    {
        $pa = $this->getMockResource(
            PatientAppointment::class,
            array('resolvePatient', 'resolveWhen', 'resolveAttributes')
        );

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('updateWorklistPatientFromMapping'))
            ->getMock();

        $rc = new \ReflectionClass($pa);
        $p = $rc->getProperty('worklist_manager');
        $p->setAccessible(true);
        $p->setValue($pa, $manager);

        $patient = \ComponentStubGenerator::generate('Patient', array('id' => 12));
        $when = new \DateTime('2012-08-04');
        $attributes = array('foo' => 'bar');

        $pa->expects($this->once())
            ->method('resolvePatient')
            ->will($this->returnValue($patient));

        $pa->expects($this->once())
            ->method('resolveWhen')
            ->will($this->returnValue($when));
        $pa->expects($this->once())
            ->method('resolveAttributes')
            ->will($this->returnValue($attributes));

        $model = \ComponentStubGenerator::generate('WorklistPatient', array('isNewRecord' => false, 'patient_id' => 4));

        $manager->expects($this->once())
            ->method('updateWorklistPatientFromMapping')
            ->with($model, $when, $attributes)
            ->will($this->returnValue(true));

        $pa->saveModel($model);
        // verify that the patient has been updated
        $this->assertEquals($patient->id, $model->patient_id);
    }
}
