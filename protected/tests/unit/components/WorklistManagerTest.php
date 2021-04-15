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

    /**
     * @covers WorklistManager
     */
    public function test_cannot_add_duplicate_patient_to_worklist()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('allowDuplicatePatients', 'getWorklistPatient'))
            ->getMock();

        $worklist = new Worklist();
        $patient = new Patient();

        $manager->expects($this->once())
            ->method('allowDuplicatePatients')
            ->will($this->returnValue(false));

        $manager->expects($this->once())
            ->method('getWorklistPatient')
            ->with($worklist, $patient)
            ->will($this->returnValue(new WorklistPatient()));

        $this->assertNull($manager->addPatientToWorklist($patient, $worklist));
        $this->assertTrue($manager->hasErrors());
    }

    /**
     * @covers WorklistManager
     */
    public function test_adding_patient_to_worklist_fails_when_worklistpatient_does_not_save()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'startTransaction'))
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

        $manager->expects($this->once())
            ->method('startTransaction')
            ->will($this->returnValue($this->getTransactionMock(array('rollback'))));

        $patient = new Patient();
        $worklist = new Worklist();

        $this->assertNull($manager->addPatientToWorklist($patient, $worklist));
        $this->assertTrue($manager->hasErrors());
    }

    /**
     * @covers WorklistManager
     */
    public function adding_patient_to_worklist_succeeds_provider()
    {
        return array(
            array(true),
            array(false),
        );
    }
    /**
     * @covers WorklistManager
     * @dataProvider adding_patient_to_worklist_succeeds_provider
     *
     * @param $duplicate
     */
    public function test_adding_patient_to_worklist_succeeds($duplicate)
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('allowDuplicatePatients', 'getWorklistPatient', 'getInstanceForClass', 'startTransaction', 'audit'))
            ->getMock();

        $patient = new Patient();
        $worklist = new Worklist();

        $manager->expects($this->once())
            ->method('allowDuplicatePatients')
            ->will($this->returnValue($duplicate));

        if ($duplicate) {
            $manager->expects($this->never())
                ->method('getWorklistPatient');
        } else {
            $manager->expects($this->once())
                ->method('getWorklistPatient')
                ->with($worklist, $patient)
                ->will($this->returnValue(null));
        }

        $wp = $this->getMockBuilder('WorklistPatient')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $wp->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));

        $manager->expects($this->once())
            ->method('startTransaction')
            ->will($this->returnValue($this->getTransactionMock(array('commit'))));

        $manager->expects($this->once())
            ->method('getInstanceForClass')
            ->will($this->returnValue($wp));

        $manager->expects($this->once())
            ->method('audit');

        $this->assertEquals($wp, $manager->addPatientToWorklist($patient, $worklist));
        $this->assertFalse($manager->hasErrors());
    }

    /**
     * @covers WorklistManager
     */
    public function test_adding_patient_to_worklist_with_attributes_succeeds()
    {
        $patient = new Patient();
        $worklist = new Worklist();
        $when = new DateTime();
        $attributes = array(
            'key1' => 'val1',
            'key2' => 'val2',
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

        $this->assertEquals($wp, $manager->addPatientToWorklist($patient, $worklist, $when, $attributes));

        $this->assertFalse($manager->hasErrors());
        $this->assertEquals($when->format('Y-m-d H:i:s'), $wp->when);
    }

    /**
     * @covers WorklistManager
     */
    public function test_adding_patient_to_worklist_with_attributes_handles_attribute_failure()
    {
        $patient = new Patient();
        $worklist = new Worklist();
        $when = new DateTime();
        $attributes = array(
            'key1' => 'val1',
            'key2' => 'val2',
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

        $this->assertNull($manager->addPatientToWorklist($patient, $worklist, $when, $attributes));

        $this->assertTrue($manager->hasErrors());
    }

    /**
     * @covers WorklistManager
     * Helper function to generate appropriate Worklist Mock.
     *
     * @param $attributes
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildWorklistFor_setAttributesForWorklistPatient($attributes)
    {
        $mapping_attributes = array();
        $id = 1;
        foreach ($attributes as $k => $v) {
            $mapping_attributes[$k] = $id++;
        }

        $w = $this->getMockBuilder('Worklist')
            ->disableOriginalConstructor()
            ->setMethods(array('getMappingAttributeIdsByName'))
            ->getMock();

        $w->expects($this->any())
            ->method('getMappingAttributeIdsByName')
            ->will($this->returnValue($mapping_attributes));

        return $w;
    }

    /**
     * @covers WorklistManager
     */
    public function test_setAttributesForWorklistPatient_new()
    {
        $attributes = array(
            'key1' => 'val1',
            'key2' => 'val2',
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

        $manager->expects($this->exactly(2))
            ->method('getInstanceForClass')
            ->with('WorklistPatientAttribute')
            ->will($this->returnValue($wpa));

        $wp = $this->getMockBuilder('WorklistPatient')
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentAttributesById'))
            ->getMock();

        $w = $this->buildWorklistFor_setAttributesForWorklistPatient($attributes);

        $wp->worklist = $w;

        $wp->expects($this->once())
            ->method('getCurrentAttributesById')
            ->will($this->returnValue(array()));

        $this->assertTrue($manager->setAttributesForWorklistPatient($wp, array(
            'key1' => 'val1',
            'key2' => 'val2',
        )));

        $this->assertFalse($manager->hasErrors());
    }

    /**
     * @covers WorklistManager
     */
    public function test_setAttributesForWorklistPatient_change_values()
    {
        $attributes = array(
            'key1' => 'val1',
            'key2' => 'val2',
        );

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'startTransaction'))
            ->getMock();

        $manager->expects($this->at(0))
            ->method('startTransaction')
            ->will($this->returnValue($this->getTransactionMock(array('commit'))));

        $manager->expects($this->never())
            ->method('getInstanceForClass');

        $wp = $this->getMockBuilder('WorklistPatient')
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentAttributesById'))
            ->getMock();

        $w = $this->buildWorklistFor_setAttributesForWorklistPatient($attributes);
        $wp->worklist = $w;

        // build out curent attributes on the worklist patient instance
        $current_by_id = array();
        foreach ($w->getMappingAttributeIdsByName() as $id) {
            $wpa = $this->getMockBuilder('WorklistPatientAttribute')
                ->disableOriginalConstructor()
                ->setMethods(array('save'))
                ->getMock();
            $wpa->expects($this->once())
                ->method('save')
                ->will($this->returnValue(true));
            $current_by_id[$id] = $wpa;
        }

        $wp->expects($this->once())
            ->method('getCurrentAttributesById')
            ->will($this->returnValue($current_by_id));

        $this->assertTrue($manager->setAttributesForWorklistPatient($wp, array(
            'key1' => 'val1',
            'key2' => 'val2',
        )));

        $this->assertFalse($manager->hasErrors());
    }

    /**
     * @covers WorklistManager
     */
    public function test_setWorklistDisplayOrderForUser()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass', 'getInstanceForClass', 'startTransaction', 'audit'))
            ->getMock();

        $u = ComponentStubGenerator::generate('User', array(
            'id' => 5,
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
            ->method('startTransaction')
            ->will($this->returnValue($this->getTransactionMock(array('commit'))));

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

    public function defaultsDataProvider()
    {
        return array(
            array('getDefaultStartTime', 'worklist_default_start_time', null, 'DEFAULT_WORKLIST_START_TIME'),
            array('getDefaultStartTime', 'worklist_default_start_time', 'misc', null),
            array('getDefaultEndTime', 'worklist_default_end_time', null, 'DEFAULT_WORKLIST_END_TIME'),
            array('getDefaultEndTime', 'worklist_default_end_time', 'misc', null),
        );
    }

    /**
     * @covers WorklistManager
     * @dataProvider defaultsDataProvider
     *
     * @param $method
     * @param $key
     * @param $app_val
     * @param $prop
     * @throws ReflectionException
     */
    public function test_getDefaultMethods($method, $key, $app_val, $prop)
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getAppParam'))
            ->getMock();

        $manager->expects($this->once())
            ->method('getAppParam')
            ->with($key)
            ->will($this->returnValue($app_val));

        if ($prop) {
            $r = new ReflectionClass($manager);
            $p = $r->getProperty($prop);
            $p->setAccessible(true);
            $expected = $p->getValue($manager);
        } else {
            $expected = $app_val;
        }

        $this->assertEquals($expected, $manager->$method());
    }

    /**
     * @covers WorklistManager
     */
    public function test_getWorklistDefinition_new()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'getDefaultStartTime', 'getDefaultEndTime'))
            ->getMock();

        $wd = new WorklistDefinition();

        $manager->expects($this->once())
            ->method('getInstanceForClass')
            ->with('WorklistDefinition')
            ->will($this->returnValue($wd));

        $st = '10';
        $et = '14';
        $manager->expects($this->once())
            ->method('getDefaultStartTime')
            ->will($this->returnValue($st));

        $manager->expects($this->once())
            ->method('getDefaultEndTime')
            ->will($this->returnValue($et));

        $this->assertEquals($wd, $manager->getWorklistDefinition());
        $this->assertEquals($st, $wd->start_time);
        $this->assertEquals($et, $wd->end_time);
    }

    /**
     * @covers WorklistManager
     */
    public function test_getWorklistDefinition_existing()
    {
        $pk = 123;
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass'))
            ->getMock();

        $model = $this->getMockBuilder('WorklistDefinition')
            ->disableOriginalConstructor()
            ->setMethods(array('findByPk'))
            ->getMock();

        $manager->expects($this->once())
            ->method('getModelForClass')
            ->with('WorklistDefinition')
            ->will($this->returnValue($model));

        $model->expects($this->once())
            ->method('findByPk')
            ->with($pk)
            ->will($this->returnValue('result'));

        $this->assertEquals('result', $manager->getWorklistDefinition($pk));
    }

    /**
     * Wrapper for testing this method in two slightly different ways.
     *
     * @param null $limit
     * @throws ReflectionException
     */
    public function generateAutomaticWorklists($limit = null)
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getGenerationTimeLimitDate', 'getInstanceForClass', 'startTransaction', 'setDateLimitOnRrule', 'createAutomaticWorklist', 'audit'))
            ->getMock();

        $manager->expects($this->once())
            ->method('getGenerationTimeLimitDate')
            ->will($this->returnValue(new DateTime()));

        $orig_rrule = 'original';

        $definition = ComponentStubGenerator::generate('WorklistDefinition', array('rrule' => $orig_rrule));

        $rrule_str = 'test';
        $manager->expects($this->once())
            ->method('setDateLimitOnRrule')
            ->with($orig_rrule)
            ->will($this->returnValue($rrule_str));

        $date = new DateTime();
        $fake_rrule = array($date, $date, $date);

        $manager->expects($this->once())
            ->method('startTransaction')
            ->will($this->returnValue($this->getTransactionMock(array('commit'))));

        $manager->expects($this->once())
            ->method('getInstanceForClass')
            ->with('\RRule\RRule', array($rrule_str))
            ->will($this->returnValue($fake_rrule));

        $manager->expects($this->exactly(3))
            ->method('createAutomaticWorklist');

        $manager->expects($this->once())
            ->method('audit');

        $manager->generateAutomaticWorklists($definition, $limit);
    }

    /**
     * @covers WorklistManager
     * @throws ReflectionException
     */
    public function test_generateAutomaticWorklists_with_limit()
    {
        $this->generateAutomaticWorklists(new DateTime());
    }

    /**
     * @covers WorklistManager
     * @throws ReflectionException
     */
    public function test_generateAutomaticWorklists_without_limit()
    {
        $this->generateAutomaticWorklists();
    }

    /**
     * @covers WorklistManager
     */
    public function test_mapPatientToWorklistDefinition()
    {
        $patient = ComponentStubGenerator::generate('Patient');
        $test_date = new DateTime();
        $attributes = array('key1' => 'value');

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getWorklistForMapping', 'getModelForClass', 'getInstanceForClass', 'addPatientToWorklist', 'audit'))
            ->getMock();

        $wi = ComponentStubGenerator::generate('Worklist');
        $manager->expects($this->at(0))
            ->method('getWorklistForMapping')
            ->with($test_date, $attributes)
            ->will($this->returnValue($wi));

        $manager->expects($this->at(1))
            ->method('addPatientToWorklist')
            ->with($patient, $wi, $test_date, $attributes)
            ->will($this->returnValue(true));

        $this->assertTrue($manager->mapPatientToWorklistDefinition($patient, $test_date, $attributes));
    }

    /**
     * @param $class
     * @param int $count
     * @param array $attrs
     * @return array
     * @throws ReflectionException
     */
    protected function getMockArray($class, $count = 1, $attrs = array())
    {
        $res = array();
        for ($i = 0; $i < $count; ++$i) {
            $res[] = ComponentStubGenerator::generate($class, $attrs);
        }

        return $res;
    }

    protected function getActiveDataProviderMock($class, $count, $class_attrs = array())
    {
        $mock = $this->getMockBuilder('CActiveDataProvider')
            ->disableOriginalConstructor()
            ->setMethods(array('getData'))
            ->getMock();

        $mock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->getMockArray($class, $count, $class_attrs)));

        return $mock;
    }

    /**
     * @throws ReflectionException
     */
    public function test_getWorklistForMapping()
    {
        $test_date = new DateTime();
        $attributes = array('k' => 'v', 'k2' => 'v2');

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass', 'checkWorklistMappingMatch'))
            ->getMock();

        $manager->patient_identifier_type = PatientIdentifierType::model()->findByPk(1);

        $wm = $this->getMockBuilder('Worklist')
            ->disableOriginalConstructor()
            ->setMethods(array('search'))
            ->getMock();

        $manager->expects($this->at(0))
            ->method('getModelForClass')
            ->with(Worklist::class)
            ->will($this->returnValue($wm));

        $adp = $this->getActiveDataProviderMock(
            'Worklist',
            3,
            array('worklist_definition' => WorklistDefinition::model()->findByPk(1))
        );
        $wls = $adp->getData();

        $wm->expects($this->once())
            ->method('search')
            ->will($this->returnValue($adp));

        $manager->expects($this->at(1))
            ->method('checkWorklistMappingMatch')
            ->with($wls[0])
            ->will($this->returnValue(false));

        $manager->expects($this->at(2))
            ->method('checkWorklistMappingMatch')
            ->with($wls[1])
            ->will($this->returnValue(false));

        $manager->expects($this->at(3))
            ->method('checkWorklistMappingMatch')
            ->with($wls[2])
            ->will($this->returnValue(true));

        $r = new ReflectionClass('WorklistManager');
        $m = $r->getMethod('getWorklistForMapping');
        $m->setAccessible(true);

        $this->assertEquals($wls[2], $m->invokeArgs($manager, array($test_date, $attributes)));

        // check search criteria was applied to the worklist model
        $this->assertEquals($test_date, $wm->at);
        $this->assertTrue($wm->automatic);
    }

    /**
     * @covers WorklistManager
     * @throws ReflectionException
     */
    public function test_updateWorklistPatientFromMapping_worklist_not_found()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getWorklistForMapping'))
            ->getMock();

        $manager->expects($this->once())
            ->method('getWorklistForMapping')
            ->will($this->returnValue(null));

        $wp = ComponentStubGenerator::generate('WorklistPatient');
        $when = new DateTime();
        $mapping = array();
        $this->assertNull($manager->updateWorklistPatientFromMapping($wp, $when, $mapping));
    }

    /**
     * @covers WorklistManager
     * @throws ReflectionException
     */
    public function test_updateWorklistPatientFromMapping_worklist_changed_error()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getWorklistForMapping', 'addError'))
            ->getMock();

        $changed_worklist = ComponentStubGenerator::generate('Worklist', array('id' => 2));

        $manager->expects($this->once())
            ->method('getWorklistForMapping')
            ->will($this->returnValue($changed_worklist));

        $manager->expects($this->once())
            ->method('addError');

        $original_worklist = ComponentStubGenerator::generate('Worklist', array('id' => 4));
        $wp = ComponentStubGenerator::generate('WorklistPatient', array('worklist' => $original_worklist));
        $when = new DateTime();
        $mapping = array();

        $this->assertNull($manager->updateWorklistPatientFromMapping($wp, $when, $mapping));
    }

    /**
     * @covers WorklistManager
     * @throws ReflectionException
     */
    public function test_updateWorklistPatientFromMapping_worklist_changed_success()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getWorklistForMapping', 'addError', 'startTransaction', 'setAttributesForWorklistPatient'))
            ->getMock();

        $changed_worklist = ComponentStubGenerator::generate('Worklist', array('id' => 2));

        $manager->expects($this->once())
            ->method('getWorklistForMapping')
            ->will($this->returnValue($changed_worklist));

        $manager->expects($this->never())
            ->method('addError');

        $manager->expects($this->once())
            ->method('startTransaction')
            ->will($this->returnValue($this->getTransactionMock(array('commit'))));

        $original_worklist = ComponentStubGenerator::generate('Worklist', array('id' => 4));
        $original_when = DateTime::createFromFormat('Y-m-d H:i', '2016-05-03 10:40');

        $wp = ComponentStubGenerator::generate('WorklistPatient', array('worklist' => $original_worklist, 'when' => $original_when));
        $wp->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $new_when = DateTime::createFromFormat('Y-m-d H:i', '2016-05-03 11:40');
        $mapping = array('key1' => 'val1');

        $manager->expects($this->once())
            ->method('setAttributesForWorklistPatient')
            ->with($wp, $mapping)
            ->will($this->returnValue(true));

        $this->assertEquals($wp, $manager->updateWorklistPatientFromMapping($wp, $new_when, $mapping, true));
        $this->assertEquals($new_when->format('Y-m-d H:i:s'), $wp->when);
        $this->assertEquals($changed_worklist->id, $wp->worklist_id);
        $this->assertFalse($manager->hasErrors());
    }

    public function updateWorklistDefinitionMapping_saveProvider()
    {
        return array(
            array(true, true), // create displayed mapping
            array(true, false), // create non-displayed mapping
            array(false, true), // update displayed mapping
            array(false, false), // update non-displayed mapping
        );
    }

    /**
     * @covers WorklistManager
     * @throws ReflectionException
     */
    public function test_getAvailableManualWorklistsForUser()
    {
        $user = ComponentStubGenerator::generate('User', array('id' => 2));

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass', 'getCurrentManualWorklistsForUser'))
            ->getMock();

        $wm = $this->getMockBuilder('Worklist')
            ->disableOriginalConstructor()
            ->setMethods(array('search'))
            ->getMock();

        $manager->expects($this->once())
            ->method('getModelForClass')
            ->with('Worklist')
            ->will($this->returnValue($wm));

        $adp = $this->getActiveDataProviderMock('Worklist', 3);
        $wls = $adp->getData();
        $wls[0]->id = 6;

        $manager->expects($this->once())
            ->method('getCurrentManualWorklistsForUser')
            ->with($user)
            ->will($this->returnValue(array($wls[0])));

        $wm->expects($this->once())
            ->method('search')
            ->will($this->returnValue($adp));

        $this->assertEquals(array($wls[1], $wls[2]), $manager->getAvailableManualWorklistsForUser($user));
        $this->assertEquals(false, $wm->automatic);
        $this->assertEquals(2, $wm->created_user_id);
    }

    public function checkWorklistMappingMatchProvider()
    {
        return array(
            array(array('test1' => array('a', 'b')), array(), false),
            array(array('test1' => array('a', 'b')), array('test1' => 'a'), true),
            array(array('test1' => array('a', 'b')), array('test1' => 'A'), true),
            array(array('test1' => array('A', 'b')), array('test1' => 'a'), true),
            array(array('test1' => array('A', 'b'), 'test2' => array('foo')), array('test1' => 'a'), false),
            array(array('test1' => array('A', 'b'), 'test2' => array('foo')), array('test1' => 'a', 'test2' => 'f oo'), false),
            array(array('test1' => array('A', 'b'), 'test2' => array('foo')), array('test1' => 'B', 'test2' => 'foo  '), true),
        );
    }

    /**
     * @covers WorklistManager
     * @dataProvider checkWorklistMappingMatchProvider
     *
     * @param $wl_attrs
     * @param $map_attrs
     * @param $expected
     *
     * @throws Exception
     */
    public function test_checkWorklistMappingMatch($wl_attrs, $map_attrs, $expected)
    {
        $manager = new WorklistManager();
        $r = new ReflectionClass($manager);
        $m = $r->getMethod('checkWorklistMappingMatch');
        $m->setAccessible(true);

        $mappings = array();
        foreach ($wl_attrs as $key => $values) {
            $mapping_values = array();
            foreach ($values as $val) {
                $mapping_values[] = ComponentStubGenerator::generate('WorklistDefinitionMappingValue', array('mapping_value' => $val));
            }
            $mappings[] = ComponentStubGenerator::generate('WorklistDefinitionMapping', array(
                'key' => $key,
                'values' => $mapping_values,
            ));
        }

        $definition = ComponentStubGenerator::generate('WorklistDefinition', array(
            'mappings' => $mappings,
        ));

        $worklist = ComponentStubGenerator::generate('Worklist', array('worklist_definition' => $definition));

        $this->assertEquals($expected, $m->invokeArgs($manager, array($worklist, $map_attrs)));
    }

    public function getDashboardRenderDatesProvider()
    {
        return array(
            array('2012-02-24', 4, array('Sat', 'Sun'),
                array('2012-02-24', '2012-02-27', '2012-02-28', '2012-02-29', '2012-03-01'), ),
            array('2012-02-25', 2, array('Sat', 'Sun'),
                array('2012-02-27', '2012-02-28'), ),
            array('2012-02-25', 2, array('Sun'),
                array('2012-02-25', '2012-02-27', '2012-02-28'), ),
            array('2018-10-27', 0, array('Sat'), array()),
        );
    }

    public function setWorklistDefinitionDisplayOrder_simpleProvider()
    {
        return array(
            array(array(1, 3, 5, 2, 4), array(1, 2, 3, 4, 5), true),
            array(array(1, 3, 5, 2, 4), array(1, 2, 3, 5), false),
        );
    }

    /**
     * @covers WorklistManager
     * @dataProvider setWorklistDefinitionDisplayOrder_simpleProvider
     *
     * @param $ordered_ids
     * @param $definition_ids
     * @param $expected
     */
    public function test_setWorklistDefinitionDisplayOrder_simple($ordered_ids, $definition_ids, $expected)
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass', 'getInstanceForClass', 'startTransaction', 'audit'))
            ->getMock();

        if ($expected) {
            $manager->expects($this->once())
                ->method('startTransaction')
                ->will($this->returnValue($this->getTransactionMock(array('commit'))));
            $manager->expects($this->once())
                ->method('audit');
        } else {
            $manager->expects($this->once())
                ->method('startTransaction')
                ->will($this->returnValue($this->getTransactionMock(array('rollback'))));
            $manager->expects($this->never())
                ->method('audit');
        }

        $definitions = array();
        foreach ($definition_ids as $id) {
            $definition = $this->getMockBuilder('WorklistDefinition')
                ->disableOriginalConstructor()
                ->setMethods(array('save'))
                ->getMock();
            $definition->id = $id;

            // verify save is called
            if ($expected) {
                $definition->expects($this->once())
                    ->method('save')
                    ->will($this->returnValue(true));
            }

            $definitions[] = $definition;
        }

        $criteria = $this->getMockBuilder('CDbCriteria')
            ->disableOriginalConstructor()
            ->getMock();

        $manager->expects($this->once())
            ->method('getInstanceForClass')
            ->with('CDbCriteria')
            ->will($this->returnValue($criteria));

        $definition_model = $this->getMockBuilder('WorklistDefinition')
            ->disableOriginalConstructor()
            ->setMethods(array('findAll'))
            ->getMock();

        $manager->expects($this->once())
            ->method('getModelForClass')
            ->with('WorklistDefinition')
            ->will($this->returnValue($definition_model));

        $definition_model->expects($this->once())
            ->method('findAll')
            ->with($criteria)
            ->will($this->returnValue($definitions));

        $this->assertEquals($expected, $manager->setWorklistDefinitionDisplayOrder($ordered_ids));
        $this->assertEquals(!$expected, $manager->hasErrors());
    }

    public function canUpdateWorklistDefinitionProvider()
    {
        return array(
            array(true, false, 2, true),
            array(false, false, 2, false),
            array(false, true, 3, true),
            array(false, false, 0, true),
            array(false, false, 3, false),
        );
    }

    /**
     * @covers WorklistManager
     */
    public function test_getCurrentAutomaticWorklistsForUserContext()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass', 'shouldDisplayWorklistForContext'))
            ->getMock();

        $adp = $this->getActiveDataProviderMock('Worklist', 2);
        $wls = $adp->getData();

        $wm = $this->getMockBuilder('Worklist')
            ->disableOriginalConstructor()
            ->setMethods(array('search'))
            ->getMock();

        $wm->expects($this->once())
            ->method('search')
            ->will($this->returnValue($adp));

        $manager->expects($this->at(0))
            ->method('getModelForClass')
            ->with('Worklist')
            ->will($this->returnValue($wm));

        // verify the filter of display context
        $institution = ComponentStubGenerator::generate('Institution');
        $site = ComponentStubGenerator::generate('Site');
        $firm = ComponentStubGenerator::generate('Firm');

        $manager->expects($this->at(1))
            ->method('shouldDisplayWorklistForContext')
            ->with($wls[0], $institution, $site, $firm)
            ->will($this->returnValue(true));
        $manager->expects($this->at(2))
            ->method('shouldDisplayWorklistForContext')
            ->with($wls[1])
            ->will($this->returnValue(false));

        $this->assertEquals(array($wls[0]), $manager->getCurrentAutomaticWorklistsForUserContext($institution, $site, $firm, new DateTime()));
    }

    public function shouldDisplayWorklistForContextProvider()
    {
        return array(
            array(
                array(
                    array('checkInstitution' => false, 'checkSite' => false, 'checkFirm' => false),
                ),
                false, ),
            array(
                array(
                    array('checkInstitution' => false, 'checkSite' => false, 'checkFirm' => false),
                    array('checkInstitution' => false, 'checkSite' => false, 'checkFirm' => false),
                ),
                false, ),
            array(
                array(
                    array('checkInstitution' => false, 'checkSite' => true, 'checkFirm' => false),
                    array('checkInstitution' => false, 'checkSite' => false, 'checkFirm' => true),
                ),
                false, ),
            array(
                array(
                    array('checkInstitution' => true, 'checkSite' => false, 'checkFirm' => false),
                    array('checkInstitution' => true, 'checkSite' => true, 'checkFirm' => true),
                ),
                true, ),
            array(
                array(),
                true, ), // special case where no contexts for definition, and therefore no restriction
        );
    }

    /**
     * @covers WorklistManager
     * @dataProvider shouldDisplayWorklistForContextProvider
     *
     * @param $context_list
     * @param $expected
     * @throws ReflectionException
     */
    public function test_shouldDisplayWorklistForContext($context_list, $expected)
    {
        /**
         * @var $institution Institution
         * @var $site Site
         * @var $firm Firm
         * @var $worklist Worklist
         */
        $manager = new WorklistManager();

        $contexts = array();
        $institution = ComponentStubGenerator::generate('Institution');
        $site = ComponentStubGenerator::generate('Site', array('institution_id' => 1));
        $firm = ComponentStubGenerator::generate('Firm');

        foreach ($context_list as $ctx) {
            $c = $this->getMockBuilder('WorklistDefinitionDisplayContext')
                ->disableOriginalConstructor()
                ->setMethods(array('checkInstitution','checkSite', 'checkFirm'))
                ->getMock();
            $c->method('checkInstitution')
                ->with($institution)
                ->willReturn($ctx['checkInstitution']);
            $c->method('checkSite')
                ->with($site)
                ->willReturn($ctx['checkSite']);
            $c->method('checkFirm')
                ->with($firm)
                ->willReturn($ctx['checkFirm']);
            $contexts[] = $c;
        }

        $definition = ComponentStubGenerator::generate('WorklistDefinition', array(
            'display_contexts' => $contexts,
            'patient_identifier_type' => PatientIdentifierType::model()->findByPk(1)
        ));

        $worklist = ComponentStubGenerator::generate('Worklist', array('worklist_definition' => $definition));

        self::assertEquals($expected, $manager->shouldDisplayWorklistForContext($worklist, $institution, $site, $firm));
    }
}
