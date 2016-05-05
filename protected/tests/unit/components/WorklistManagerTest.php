<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class WorklistManagerTest extends PHPUnit_Framework_TestCase
{
    public function test_cannot_add_duplicate_patient_to_worklist()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getWorklistPatient'))
            ->getMock();

        $worklist = new Worklist();
        $patient = new Patient();

        $manager->expects($this->any())
            ->method('getWorklistPatient')
            ->with($worklist, $patient)
            ->will($this->returnValue(new WorklistPatient()));

        $this->assertFalse($manager->addPatientToWorklist($patient, $worklist));
        $this->assertTrue($manager->hasErrors());
    }

    public function test_adding_patient_to_worklist()
    {
        $this->markTestIncomplete();
    }
}
