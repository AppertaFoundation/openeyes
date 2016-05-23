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

        $this->assertNull($manager->addPatientToWorklist($patient, $worklist));
        $this->assertTrue($manager->hasErrors());
    }

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

    public function test_adding_patient_to_worklist_succeeds()
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'startTransaction', 'audit'))
            ->getMock();

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

        $patient = new Patient();
        $worklist = new Worklist();

        $this->assertEquals($wp, $manager->addPatientToWorklist($patient, $worklist));
        $this->assertFalse($manager->hasErrors());
    }

    public function test_adding_patient_to_worklist_with_attributes_succeeds()
    {
        $patient = new Patient();
        $worklist = new Worklist();
        $when = new DateTime();
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

        $this->assertEquals($wp, $manager->addPatientToWorklist($patient, $worklist, $when, $attributes));

        $this->assertFalse($manager->hasErrors());
        $this->assertEquals($when->format('Y-m-d H:i:s'), $wp->when);
    }

    public function test_adding_patient_to_worklist_with_attributes_handles_attribute_failure()
    {
        $patient = new Patient();
        $worklist = new Worklist();
        $when = new DateTime();
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

        $this->assertNull($manager->addPatientToWorklist($patient, $worklist, $when, $attributes));

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
            ->setMethods(array('getModelForClass', 'getInstanceForClass', 'startTransaction', 'audit'))
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
            array('getDefaultStartTime', 'default_worklist_start_time', null, 'DEFAULT_WORKLIST_START_TIME'),
            array('getDefaultStartTime', 'default_worklist_start_time', 'misc', null),
            array('getDefaultEndTime', 'default_worklist_end_time', null, 'DEFAULT_WORKLIST_END_TIME'),
            array('getDefaultEndTime', 'default_worklist_end_time', 'misc', null)
        );
    }

    /**
     * @dataProvider defaultsDataProvider
     * @param $method
     * @param $key
     * @param $app_val
     * @param $prop
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
        }
        else {
            $expected = $app_val;
        }

        $this->assertEquals($expected, $manager->$method());
    }

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
     * Wrapper for testing this method in two slightly different ways
     *
     * @param null $limit
     */
    public function generateAutomaticWorklists($limit = null)
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getGenerationTimeLimitDate', 'getInstanceForClass', 'startTransaction', 'setDateLimitOnRrule', 'createAutomaticWorklist', 'audit'))
            ->getMock();

        if (is_null($limit)) {
            $manager->expects($this->once())
                ->method('getGenerationTimeLimitDate')
                ->will($this->returnValue(new DateTime()));
        }
        else {
            $manager->expects($this->never())
                ->method('getGenerationTimeLimitDate');
        }

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

    public function test_generateAutomaticWorklists_with_limit()
    {
        $this->generateAutomaticWorklists(new DateTime());
    }

    public function test_generateAutomaticWorklists_without_limit()
    {
        $this->generateAutomaticWorklists();
    }

    public function test_generateWorklistName()
    {
        $this->markTestIncomplete("Waiting to implement actual intended functionality");
    }

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

    protected function getMockArray($class, $count = 1, $methods = array())
    {
        $res = array();
        for ($i = 0; $i < $count; $i++)
            $res[] = $this->getMockBuilder($class)
                ->disableOriginalConstructor()
                ->setMethods($methods)
                ->getMock();

        return $res;
    }

    protected function getActiveDataProviderMock($class, $count, $class_methods = array())
    {
        $mock = $this->getMockBuilder("CActiveDataProvider")
            ->disableOriginalConstructor()
            ->setMethods(array('getData'))
            ->getMock();

        $mock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->getMockArray($class, $count,$class_methods)));

        return $mock;

    }

    public function test_getWorklistForMapping()
    {
        $test_date = new DateTime();
        $attributes = array('k' => 'v', 'k2' => 'v2');

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass', 'checkWorklistMappingMatch'))
            ->getMock();

        $wm = $this->getMockBuilder('Worklist')
            ->disableOriginalConstructor()
            ->setMethods(array('search'))
            ->getMock();

        $manager->expects($this->at(0))
            ->method('getModelForClass')
            ->with('Worklist')
            ->will($this->returnValue($wm));

        $adp = $this->getActiveDataProviderMock('Worklist', 3);
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

    public function test_updateWorklistPatientFromMapping()
    {
        $this->markTestIncomplete("New method. not had chance to write test yet.");
    }

    public function test_updateWorklistDefinitionMapping_empty()
    {
        $mapping = new WorklistDefinitionMapping();
        $manager = new WorklistManager();

        $this->assertFalse($manager->updateWorklistDefinitionMapping($mapping, 'test key', ''));
        $this->assertTrue($manager->hasErrors());
    }


    public function test_updateWorklistDefinitionMapping_invalid_key()
    {
        $definition = $this->getMockBuilder('WorklistDefinition')
            ->disableOriginalConstructor()
            ->setMethods(array('validateMappingKey'))
            ->getMock();

        $definition->expects($this->once())
            ->method('validateMappingKey')
            ->will($this->returnValue(false));

        $mapping = new WorklistDefinitionMapping();
        $mapping->worklist_definition = $definition;

        $manager = new WorklistManager();

        $this->assertFalse($manager->updateWorklistDefinitionMapping($mapping, 'test key', 'foo'));
        $this->assertTrue($manager->hasErrors());
    }

    public function updateWorklistDefinitionMapping_saveProvider()
    {
        return array(
            array(true, true), // create displayed mapping
            array(true, false), // create non-displayed mapping
            array(false, true), // update displayed mapping
            array(false, false) // update non-displayed mapping
        );
    }

    /**
     * @dataProvider updateWorklistDefinitionMapping_saveProvider
     *
     * @param bool $new
     * @param bool $display
     */
    public function test_updateWorklistDefinitionMapping_save($new, $display)
    {
        $key = 'test-key';

        $definition = $this->getMockBuilder('WorklistDefinition')
            ->disableOriginalConstructor()
            ->setMethods(array('validateMappingKey', 'getNextDisplayOrder'))
            ->getMock();

        $mapping = ComponentStubGenerator::generate("WorklistDefinitionMapping", array(
            'worklist_definition' => $definition,
            'isNewRecord' => $new,
            'id' => $new ? null : 4
        ));

        if ($new) {
            $definition->expects($this->once())
                ->method('validateMappingKey')
                ->with($key)
                ->will($this->returnValue(true));
        }
        else {
            $definition->expects($this->once())
                ->method('validateMappingKey')
                ->with($key, $mapping->id)
                ->will($this->returnValue(true));
        }

        if ($new && $display) {
            $definition->expects($this->once())
                ->method('getNextDisplayOrder')
                ->will($this->returnValue(3));
        }
        else {
            $definition->expects($this->never())
                ->method('getNextDisplayOrder');
        }

        $manager = $this->getMockBuilder("WorklistManager")
            ->disableOriginalConstructor()
            ->setMethods(array('startTransaction'))
            ->getMock();
        $manager->expects($this->once())
            ->method('startTransaction')
            ->will($this->returnValue($this->getTransactionMock(array('commit'))));

        $manager->updateWorklistDefinitionMapping($mapping, $key, 'one,two', $display);
    }

    public function test_getAvailableManualWorklistsForUser()
    {
        $user = ComponentStubGenerator::generate("User", array('id' => 2));

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass','getCurrentManualWorklistsForUser'))
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
            array(array('test1' => array('A', 'b'), 'test2' => array('foo')), array('test1' => 'B', 'test2' => 'foo  '), true)
        );
    }

    /**
     * @dataProvider checkWorklistMappingMatchProvider
     *
     * @param $wl_attrs
     * @param $map_attrs
     * @param $expected
     * @throws Exception
     */
    public function test_checkWorklistMappingMatch($wl_attrs, $map_attrs, $expected)
    {
        $manager =  new WorklistManager();
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
                'values' => $mapping_values
            ));
        }

        $definition = ComponentStubGenerator::generate("WorklistDefinition", array(
            'mappings' => $mappings
        ));

        $worklist = ComponentStubGenerator::generate("Worklist", array('worklist_definition' => $definition));

        $this->assertEquals($expected, $m->invokeArgs($manager,array($worklist, $map_attrs)));
    }
}
