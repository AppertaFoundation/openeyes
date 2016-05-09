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
    protected function getTransactionMock($methods = array())
    {
        $transaction = $this->getMockBuilder('CDbTransaction')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();

        foreach ($methods as $method) {
            $transaction->expects($this->once())
                ->method($method);
        }

        return $transaction;
    }

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

    public function test_adding_patient_to_worklist_fails_when_worklistpatient_does_not_save()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass'))
            ->getMock();

        $wp = $this->getMockBuilder('WorklistPatient')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $wp->expects($this->any())
            ->method('save')
            ->will($this->returnValue(false));

        $manager->expects($this->once())
            ->method('getInstanceForClass')
            ->will($this->returnValue($wp));

        $patient = new Patient();
        $worklist = new Worklist();

        $this->assertFalse($manager->addPatientToWorklist($patient, $worklist));
        $this->assertTrue($manager->hasErrors());
    }

    public function test_adding_patient_to_worklist_succeeds()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'audit'))
            ->getMock();

        $wp = $this->getMockBuilder('WorklistPatient')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $wp->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));

        $manager->expects($this->once())
            ->method('getInstanceForClass')
            ->will($this->returnValue($wp));

        $manager->expects($this->once())
            ->method('audit');

        $patient = new Patient();
        $worklist = new Worklist();

        $this->assertTrue($manager->addPatientToWorklist($patient, $worklist));
        $this->assertFalse($manager->hasErrors());
    }

    public function test_adding_patient_to_worklist_with_attributes_succeeds()
    {
        $patient = new Patient();
        $worklist = new Worklist();
        $when = '11:30';
        $attributes = array(
            'key1' => 'val1',
            'key2' => 'val2'
        );

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'startTransaction', 'setAttributesForWorklistPatient', 'audit'))
            ->getMock();

        $wp = $this->getMockBuilder('WorklistPatient')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $wp->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));

        $transaction = $this->getTransactionMock(array('commit'));

        $manager->expects($this->at(0))
            ->method('startTransaction')
            ->will($this->returnValue($transaction));

        $manager->expects($this->at(1))
            ->method('getInstanceForClass')
            ->with('WorklistPatient')
            ->will($this->returnValue($wp));

        $manager->expects($this->at(2))
            ->method('setAttributesForWorklistPatient')
            ->with($wp, $attributes)
            ->will($this->returnValue(true));

        $manager->expects($this->once())
            ->method('audit');

        $this->assertTrue($manager->addPatientToWorklist($patient, $worklist, $when, $attributes));

        $this->assertFalse($manager->hasErrors());
        $this->assertEquals($when, $wp->when);
    }

    public function test_adding_patient_to_worklist_with_attributes_handles_attribute_failure()
    {
        $patient = new Patient();
        $worklist = new Worklist();
        $when = '11:30';
        $attributes = array(
            'key1' => 'val1',
            'key2' => 'val2'
        );

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'startTransaction', 'setAttributesForWorklistPatient'))
            ->getMock();

        $transaction = $this->getTransactionMock(array('rollback'));
        $manager->expects($this->at(0))
            ->method('startTransaction')
            ->will($this->returnValue($transaction));

        $wp = $this->getMockBuilder('WorklistPatient')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $wp->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));

        $manager->expects($this->at(1))
            ->method('getInstanceForClass')
            ->with('WorklistPatient')
            ->will($this->returnValue($wp));

        $manager->expects($this->at(2))
            ->method('setAttributesForWorklistPatient')
            ->with($wp, $attributes)
            ->will($this->returnValue(false));

        $this->assertFalse($manager->addPatientToWorklist($patient, $worklist, $when, $attributes));

        $this->assertTrue($manager->hasErrors());
    }

    public function test_setAttributesForWorklistPatient()
    {
        $attributes = array(
            'key1' => 'val1',
            'key2' => 'val2'
        );

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'startTransaction'))
            ->getMock();

        $transaction = $this->getTransactionMock(array('commit'));

        $manager->expects($this->at(0))
            ->method('startTransaction')
            ->will($this->returnValue($transaction));

        $wpa = $this->getMockBuilder('WorklistPatientAttribute')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $wpa->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));

        $manager->expects($this->any())
            ->method('getInstanceForClass')
            ->with('WorklistPatientAttribute')
            ->will($this->returnValue($wpa));

        $mapping_attributes = array();
        foreach ($attributes as $k => $v) {
            $wla = new WorklistAttribute();
            $wla->name = $k;
            $mapping_attributes[] = $wla;
        }
        $w = ComponentStubGenerator::generate('Worklist', array(
            'mapping_attributes' => $mapping_attributes
        ));

        $wp = new WorklistPatient();
        $wp->worklist = $w;

        $this->assertTrue($manager->setAttributesForWorklistPatient($wp,array(
            'key1' => 'val1',
            'key2' => 'val2'
        )));

        $this->assertFalse($manager->hasErrors());
    }

    public function test_setWorklistDisplayOrderForUser()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass', 'getInstanceForClass', 'audit'))
            ->getMock();

        $u = ComponentStubGenerator::generate('User', array(
            'id' => 5
        ));

        $wdo = $this->getMockBuilder('WorklistDisplayOrder')
            ->disableOriginalConstructor()
            ->setMethods(array('deleteAllByAttributes', 'save'))
            ->getMock();

        // clear all previous entries out
        $wdo->expects($this->once())
            ->method('deleteAllByAttributes')
            ->will($this->returnValue(true));

        // save the new entry
        $wdo->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $manager->expects($this->once())
            ->method('getModelForClass')
            ->with('WorklistDisplayOrder')
            ->will($this->returnValue($wdo));

        $manager->expects($this->any())
            ->method('getInstanceForClass')
            ->with('WorklistDisplayOrder')
            ->will($this->returnValue($wdo));

        $manager->expects($this->once())
            ->method('audit');

        $this->assertTrue($manager->setWorklistDisplayOrderForUser($u, array(5)));
    }
}
