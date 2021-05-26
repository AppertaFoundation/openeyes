<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ReportDiagnosesTest extends CDbTestCase
{
    public $fixtures = array(
        'institution' => Institution::class,
        'site' => Site::class,
        'patient_identifier_type' => PatientIdentifierType::class,
        'patient' => Patient::class,
        'patient_identifier' => PatientIdentifier::class,
        'diagnoses' => 'SecondaryDiagnosis',
        'episodes' => 'Episode',
    );

    public static function setupBeforeClass()
    {
        Yii::app()->session['selected_institution_id'] = 1;
        Yii::app()->params['display_primary_number_usage_code'] = 'LOCAL';
    }

    public static function tearDownAfterClass()
    {
        unset(Yii::app()->session['selected_institution_id']);
        unset(Yii::app()->params['display_primary_number_usage_code']);
    }

    public function setUp()
    {
        parent::setUp();
    }

    public function testAfterValidate_NoDiagnoses()
    {
        $r = new ReportDiagnoses();

        $r->validate();

        $this->assertTrue(isset($r->errors['principal']));
        $this->assertEquals(array('Please select at least one diagnosis'), $r->errors['principal']);
    }

    public function testAfterValidate_PrincipalOnly()
    {
        $r = new ReportDiagnoses();
        $r->principal = array(1);

        $this->assertFalse(isset($r->errors['principal']));
    }

    public function testAfterValidate_SecondaryOnly()
    {
        $r = new ReportDiagnoses();
        $r->secondary = array(1);

        $this->assertFalse(isset($r->errors['principal']));
    }

    public function testAfterValidate_AllOnly()
    {
        $r = new ReportDiagnoses();
        $r->all = array(1);

        $this->assertFalse(isset($r->errors['principal']));
    }

    public function testFilterDiagnoses()
    {
        $r = new ReportDiagnoses();
        $r->principal = array(1, 5, 7, 1001, 20202);
        $r->secondary = array(2, 4, 8, 9);
        $r->all = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 1001, 20203);

        $all = $r->filterDiagnoses();

        $this->assertEquals(array(3, 6, 10, 20203), $all);
    }

    public function testRun_FilterAllDiagnoses()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('filterDiagnoses'))
            ->getMock();

        $r->expects($this->once())
            ->method('filterDiagnoses');

        $r->all = array(1);

        $r->run();
    }

    public function testRun_DontFilterWithNoAllDiagnoses()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('filterDiagnoses'))
            ->getMock();

        $r->expects($this->never())
            ->method('filterDiagnoses');

        $r->principal = array(1);
        $r->secondary = array(2);

        $r->run();
    }

    public function testRun_JoinDisorders_Principal()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('filterDiagnoses', 'getDbCommand', 'joinDisorders', 'addDiagnosesResultItem'))
            ->getMock();

        $r->principal = array(1, 2, 3);

        $query = Yii::app()->db->createCommand()
            ->from('patient p')
            ->join('contact c', 'p.contact_id = c.id');

        $r->expects($this->once())
            ->method('getDbCommand')
            ->will($this->returnValue($query));

        $r->expects($this->once())
            ->method('joinDisorders')
            ->with('Principal', array(1, 2, 3), 'p.id, c.first_name, c.last_name, p.dob', array(), array(), $query);

        $r->run();
    }

    public function testRun_JoinDisorders_Secondary()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDbCommand', 'joinDisorders', 'addDiagnosesResultItem'))
            ->getMock();

        $r->secondary = array(1, 2, 3);

        $query = Yii::app()->db->createCommand()
            ->from('patient p')
            ->join('contact c', 'p.contact_id = c.id');

        $r->expects($this->once())
            ->method('getDbCommand')
            ->will($this->returnValue($query));

        $r->expects($this->once())
            ->method('joinDisorders')
            ->with('Secondary', array(1, 2, 3), 'p.id, c.first_name, c.last_name, p.dob', array(), array(), $query);

        $r->run();
    }

    /**
     * @covers ReportDiagnoses
     */
    public function testRun_JoinDisorders_All()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDbCommand', 'joinDisorders', 'addDiagnosesResultItem'))
            ->getMock();

        $r->all = array(1, 2, 3);

        $query = Yii::app()->db->createCommand()
            ->from('patient p')
            ->join('contact c', 'p.contact_id = c.id');

        $r->expects($this->once())
            ->method('getDbCommand')
            ->will($this->returnValue($query));

        $r->expects($this->once())
            ->method('joinDisorders')
            ->with('All', array(1, 2, 3), 'p.hos_num, c.first_name, c.last_name, p.dob', array(), array(), $query);

        $r->run();
    }

    public function testRun_JoinDisorders_PrincipalAndSecondary()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDbCommand', 'joinDisorders', 'addDiagnosesResultItem'))
            ->getMock();

        $r->principal = array(1, 2, 3);
        $r->secondary = array(4, 5, 6);

        $query = Yii::app()->db->createCommand()
            ->from('patient p')
            ->join('contact c', 'p.contact_id = c.id');

        $r->expects($this->once())
            ->method('getDbCommand')
            ->will($this->returnValue($query));

        $r->expects($this->at(1))
            ->method('joinDisorders')
            ->with('Principal', array(1, 2, 3), 'p.id, c.first_name, c.last_name, p.dob', array(), array(), $query);

        $r->expects($this->at(2))
            ->method('joinDisorders')
            ->with('Secondary', array(4, 5, 6), 'p.id, c.first_name, c.last_name, p.dob', array(), array(), $query);

        $r->run();
    }

    /**
     * @covers ReportDiagnoses
     */
    public function testRun_JoinDisorders_PrincipalAndSecondaryAndAll()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDbCommand', 'joinDisorders', 'addDiagnosesResultItem'))
            ->getMock();

        $r->principal = array(1, 2, 3);
        $r->secondary = array(4, 5, 6);
        $r->all = array(7, 8, 9);

        $query = Yii::app()->db->createCommand()
            ->from('patient p')
            ->join('contact c', 'p.contact_id = c.id');

        $r->expects($this->once())
            ->method('getDbCommand')
            ->will($this->returnValue($query));

        $r->expects($this->at(1))
            ->method('joinDisorders')
            ->with('Principal', array(1, 2, 3), 'p.hos_num, c.first_name, c.last_name, p.dob', array(), array(), $query);

        $r->expects($this->at(2))
            ->method('joinDisorders')
            ->with('Secondary', array(4, 5, 6), 'p.hos_num, c.first_name, c.last_name, p.dob', array(), array(), $query);

        $r->expects($this->at(3))
            ->method('joinDisorders')
            ->with('All', array(7, 8, 9), 'p.hos_num, c.first_name, c.last_name, p.dob', array(), array(), $query);

        $r->run();
    }

    public function testRun_ConditionType_Or()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDbCommand', 'addDiagnosesResultItem'))
            ->getMock();

        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        $r->expects($this->once())
            ->method('getDbCommand')
            ->will($this->returnValue($query));

        $query->expects($this->once())
            ->method('select')
            ->with('p.id, c.first_name, c.last_name, p.dob, e0.created_date as pdis0_date, pdis0.fully_specified_name as pdis0_fully_specified_name, e0.eye_id as pdis0_eye, e1.created_date as pdis1_date, pdis1.fully_specified_name as pdis1_fully_specified_name, e1.eye_id as pdis1_eye, e2.created_date as pdis2_date, pdis2.fully_specified_name as pdis2_fully_specified_name, e2.eye_id as pdis2_eye');

        $query->expects($this->once())
            ->method('where')
            ->with('( pdis0.id is not null or pdis1.id is not null or pdis2.id is not null )');

        $query->expects($this->once())
            ->method('queryAll')
            ->will($this->returnValue(array()));

        $r->principal = array(1, 2, 3);
        $r->condition_type = 'or';

        $r->run();
    }

    public function testRun_ConditionType_And()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDbCommand', 'addDiagnosesResultItem'))
            ->getMock();

        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        $r->expects($this->once())
            ->method('getDbCommand')
            ->will($this->returnValue($query));

        $query->expects($this->once())
            ->method('select')
            ->with('p.id, c.first_name, c.last_name, p.dob, e0.created_date as pdis0_date, pdis0.fully_specified_name as pdis0_fully_specified_name, e0.eye_id as pdis0_eye, e1.created_date as pdis1_date, pdis1.fully_specified_name as pdis1_fully_specified_name, e1.eye_id as pdis1_eye, e2.created_date as pdis2_date, pdis2.fully_specified_name as pdis2_fully_specified_name, e2.eye_id as pdis2_eye');

        $query->expects($this->once())
            ->method('where')
            ->with('( pdis0.id is not null and pdis1.id is not null and pdis2.id is not null )');

        $query->expects($this->once())
            ->method('queryAll')
            ->will($this->returnValue(array()));

        $r->principal = array(1, 2, 3);
        $r->condition_type = 'and';

        $r->run();
    }

    public function testRun_AddDiagnosesResultItem()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('addDiagnosesResultItem'))
            ->getMock();

        $r->secondary = array(1, 2, 3);

        $results = array(
            array(
                'id' => 1,
                'first_name' => 'Jim',
                'last_name' => 'Aylward',
                'dob' => '1970-01-01',
                'sdis0_date' => date('Y-m-d', strtotime('-10 days')),
                'sdis0_fully_specified_name' => 'Myopia (disorder)',
                'sdis0_eye' => '1',
                'sdis1_date' => date('Y-m-d', strtotime('-12 days')),
                'sdis1_fully_specified_name' => 'Retinal lattice degeneration (disorder)',
                'sdis1_eye' => '2',
                'sdis2_date' => date('Y-m-d', strtotime('-22 days')),
                'sdis2_fully_specified_name' => 'Posterior vitreous detachment (disorder)',
                'sdis2_eye' => '3',
            ),
            array(
                'id' => 2,
                'first_name' => 'Bob',
                'last_name' => 'Collin',
                'dob' => '1972-01-01',
                'sdis0_date' => '2004',
                'sdis0_fully_specified_name' => 'Myopia (disorder)',
                'sdis0_eye' => '1',
                'sdis1_date' => '2006',
                'sdis1_fully_specified_name' => 'Retinal lattice degeneration (disorder)',
                'sdis1_eye' => '2',
                'sdis2_date' => '2005',
                'sdis2_fully_specified_name' => 'Posterior vitreous detachment (disorder)',
                'sdis2_eye' => '3',
            ),
            array(
                'id' => 3,
                'first_name' => 'Edward',
                'last_name' => 'Allan',
                'dob' => '1960-01-01',
                'sdis0_date' => null,
                'sdis0_fully_specified_name' => null,
                'sdis0_eye' => null,
                'sdis1_date' => null,
                'sdis1_fully_specified_name' => null,
                'sdis1_eye' => null,
                'sdis2_date' => null,
                'sdis2_fully_specified_name' => null,
                'sdis2_eye' => null,
            ),
            array(
                'id' => 4,
                'first_name' => 'Sarah',
                'last_name' => 'Shore',
                'dob' => '1977-01-01',
                'sdis0_date' => null,
                'sdis0_fully_specified_name' => null,
                'sdis0_eye' => null,
                'sdis1_date' => null,
                'sdis1_fully_specified_name' => null,
                'sdis1_eye' => null,
                'sdis2_date' => null,
                'sdis2_fully_specified_name' => null,
                'sdis2_eye' => null,
            ),
        );

        foreach ($results as $i => $result) {
            $r->expects($this->at($i))
                ->method('addDiagnosesResultItem')
                ->with($results[$i]);
        }

        $r->run();
    }

    public function testJoinDisorders_Principal_Or()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 2))
                ->method('leftJoin')
                ->with('episode e'.$i, 'e'.$i.'.patient_id = p.id and e'.$i.'.disorder_id = :pdis'.$i);

            $query->expects($this->at(($i * 2) + 1))
                ->method('leftJoin')
                ->with('disorder pdis'.$i, 'pdis'.$i.'.id = e'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'or';

        $select = 'p.id, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('Principal', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.id, c.first_name, c.last_name, p.dob, e0.created_date as pdis0_date, pdis0.fully_specified_name as pdis0_fully_specified_name, '.
            'e0.eye_id as pdis0_eye, e1.created_date as pdis1_date, pdis1.fully_specified_name as pdis1_fully_specified_name, e1.eye_id as pdis1_eye, '.
            'e2.created_date as pdis2_date, pdis2.fully_specified_name as pdis2_fully_specified_name, e2.eye_id as pdis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':pdis0' => 1,
                ':pdis1' => 2,
                ':pdis2' => 3,
            ), $whereParams);

        $this->assertEquals(array(
                'pdis0.id is not null',
                'pdis1.id is not null',
                'pdis2.id is not null',
            ), $or_conditions);
    }

    public function testJoinDisorders_Principal_And()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 2))
                ->method('leftJoin')
                ->with('episode e'.$i, 'e'.$i.'.patient_id = p.id and e'.$i.'.disorder_id = :pdis'.$i);

            $query->expects($this->at(($i * 2) + 1))
                ->method('leftJoin')
                ->with('disorder pdis'.$i, 'pdis'.$i.'.id = e'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'and';

        $select = 'p.id, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('Principal', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.id, c.first_name, c.last_name, p.dob, e0.created_date as pdis0_date, pdis0.fully_specified_name as pdis0_fully_specified_name, '.
            'e0.eye_id as pdis0_eye, e1.created_date as pdis1_date, pdis1.fully_specified_name as pdis1_fully_specified_name, e1.eye_id as pdis1_eye, '.
            'e2.created_date as pdis2_date, pdis2.fully_specified_name as pdis2_fully_specified_name, e2.eye_id as pdis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':pdis0' => 1,
                ':pdis1' => 2,
                ':pdis2' => 3,
            ), $whereParams);

        $this->assertEquals(array(
                'pdis0.id is not null',
                'pdis1.id is not null',
                'pdis2.id is not null',
            ), $or_conditions);
    }

    public function testJoinDisorders_Secondary_Or()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 2))
                ->method('leftJoin')
                ->with('secondary_diagnosis sd'.$i, 'sd'.$i.'.patient_id = p.id and sd'.$i.'.disorder_id = :sdis'.$i);

            $query->expects($this->at(($i * 2) + 1))
                ->method('leftJoin')
                ->with('disorder sdis'.$i, 'sdis'.$i.'.id = sd'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'or';

        $select = 'p.id, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('Secondary', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.id, c.first_name, c.last_name, p.dob, sd0.date as sdis0_date, sdis0.fully_specified_name as sdis0_fully_specified_name, '.
            'sd0.eye_id as sdis0_eye, sd1.date as sdis1_date, sdis1.fully_specified_name as sdis1_fully_specified_name, sd1.eye_id as sdis1_eye, '.
            'sd2.date as sdis2_date, sdis2.fully_specified_name as sdis2_fully_specified_name, sd2.eye_id as sdis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':sdis0' => 1,
                ':sdis1' => 2,
                ':sdis2' => 3,
            ), $whereParams);

        $this->assertEquals(array(
                'sdis0.id is not null',
                'sdis1.id is not null',
                'sdis2.id is not null',
            ), $or_conditions);
    }

    public function testJoinDisorders_Secondary_And()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 2))
                ->method('leftJoin')
                ->with('secondary_diagnosis sd'.$i, 'sd'.$i.'.patient_id = p.id and sd'.$i.'.disorder_id = :sdis'.$i);

            $query->expects($this->at(($i * 2) + 1))
                ->method('leftJoin')
                ->with('disorder sdis'.$i, 'sdis'.$i.'.id = sd'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'and';

        $select = 'p.id, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('Secondary', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.id, c.first_name, c.last_name, p.dob, sd0.date as sdis0_date, sdis0.fully_specified_name as sdis0_fully_specified_name, '.
            'sd0.eye_id as sdis0_eye, sd1.date as sdis1_date, sdis1.fully_specified_name as sdis1_fully_specified_name, sd1.eye_id as sdis1_eye, '.
            'sd2.date as sdis2_date, sdis2.fully_specified_name as sdis2_fully_specified_name, sd2.eye_id as sdis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':sdis0' => 1,
                ':sdis1' => 2,
                ':sdis2' => 3,
            ), $whereParams);

        $this->assertEquals(array(
                'sdis0.id is not null',
                'sdis1.id is not null',
                'sdis2.id is not null',
            ), $or_conditions);
    }

    /**
     * @covers ReportDiagnoses
     */
    public function testJoinDisorders_All_Or()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 4))
                ->method('leftJoin')
                ->with('episode ea'.$i, 'ea'.$i.'.patient_id = p.id and ea'.$i.'.disorder_id = :padis'.$i);

            $query->expects($this->at(($i * 4) + 1))
                ->method('leftJoin')
                ->with('disorder padis'.$i, 'padis'.$i.'.id = ea'.$i.'.disorder_id');

            $query->expects($this->at(($i * 4) + 2))
                ->method('leftJoin')
                ->with('secondary_diagnosis sda'.$i, 'sda'.$i.'.patient_id = p.id and sda'.$i.'.disorder_id = :sadis'.$i);

            $query->expects($this->at(($i * 4) + 3))
                ->method('leftJoin')
                ->with('disorder sadis'.$i, 'sadis'.$i.'.id = sda'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'or';

        $select = 'p.hos_num, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('All', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.hos_num, c.first_name, c.last_name, p.dob, ea0.created_date as padis0_date, padis0.fully_specified_name as padis0_fully_specified_name, '.
            'ea0.eye_id as padis0_eye, sda0.date as sadis0_date, sadis0.fully_specified_name as sadis0_fully_specified_name, sda0.eye_id as sadis0_eye, '.
            'ea1.created_date as padis1_date, padis1.fully_specified_name as padis1_fully_specified_name, ea1.eye_id as padis1_eye, sda1.date as sadis1_date,'.
            ' sadis1.fully_specified_name as sadis1_fully_specified_name, sda1.eye_id as sadis1_eye, ea2.created_date as padis2_date, '.
            'padis2.fully_specified_name as padis2_fully_specified_name, ea2.eye_id as padis2_eye, sda2.date as sadis2_date, '.
            'sadis2.fully_specified_name as sadis2_fully_specified_name, sda2.eye_id as sadis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':padis0' => 1,
                ':padis1' => 2,
                ':padis2' => 3,
                ':sadis0' => 1,
                ':sadis1' => 2,
                ':sadis2' => 3,
            ), $whereParams);

        $this->assertEquals(array(
                '(padis0.id is not null or sadis0.id is not null)',
                '(padis1.id is not null or sadis1.id is not null)',
                '(padis2.id is not null or sadis2.id is not null)',
            ), $or_conditions);
    }

    /**
     * @covers ReportDiagnoses
     */
    public function testJoinDisorders_All_And()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 4))
                ->method('leftJoin')
                ->with('episode ea'.$i, 'ea'.$i.'.patient_id = p.id and ea'.$i.'.disorder_id = :padis'.$i);

            $query->expects($this->at(($i * 4) + 1))
                ->method('leftJoin')
                ->with('disorder padis'.$i, 'padis'.$i.'.id = ea'.$i.'.disorder_id');

            $query->expects($this->at(($i * 4) + 2))
                ->method('leftJoin')
                ->with('secondary_diagnosis sda'.$i, 'sda'.$i.'.patient_id = p.id and sda'.$i.'.disorder_id = :sadis'.$i);

            $query->expects($this->at(($i * 4) + 3))
                ->method('leftJoin')
                ->with('disorder sadis'.$i, 'sadis'.$i.'.id = sda'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'and';

        $select = 'p.hos_num, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('All', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.hos_num, c.first_name, c.last_name, p.dob, ea0.created_date as padis0_date, padis0.fully_specified_name as padis0_fully_specified_name, '.
            'ea0.eye_id as padis0_eye, sda0.date as sadis0_date, sadis0.fully_specified_name as sadis0_fully_specified_name, sda0.eye_id as sadis0_eye, '.
            'ea1.created_date as padis1_date, padis1.fully_specified_name as padis1_fully_specified_name, ea1.eye_id as padis1_eye, sda1.date as sadis1_date,'.
            ' sadis1.fully_specified_name as sadis1_fully_specified_name, sda1.eye_id as sadis1_eye, ea2.created_date as padis2_date, '.
            'padis2.fully_specified_name as padis2_fully_specified_name, ea2.eye_id as padis2_eye, sda2.date as sadis2_date, '.
            'sadis2.fully_specified_name as sadis2_fully_specified_name, sda2.eye_id as sadis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':padis0' => 1,
                ':padis1' => 2,
                ':padis2' => 3,
                ':sadis0' => 1,
                ':sadis1' => 2,
                ':sadis2' => 3,
            ), $whereParams);

        $this->assertEquals(array(
                '(padis0.id is not null or sadis0.id is not null)',
                '(padis1.id is not null or sadis1.id is not null)',
                '(padis2.id is not null or sadis2.id is not null)',
            ), $or_conditions);
    }

    public function testJoinDisorders_StartDate()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 2))
                ->method('leftJoin')
                ->with('secondary_diagnosis sd'.$i, 'sd'.$i.'.patient_id = p.id and sd'.$i.'.disorder_id = :sdis'.$i.' and sd'.$i.'.date >= :start_date');

            $query->expects($this->at(($i * 2) + 1))
                ->method('leftJoin')
                ->with('disorder sdis'.$i, 'sdis'.$i.'.id = sd'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'and';
        $r->start_date = '10 May 2002';

        $select = 'p.id, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('Secondary', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.id, c.first_name, c.last_name, p.dob, sd0.date as sdis0_date, sdis0.fully_specified_name as sdis0_fully_specified_name, '.
            'sd0.eye_id as sdis0_eye, sd1.date as sdis1_date, sdis1.fully_specified_name as sdis1_fully_specified_name, sd1.eye_id as sdis1_eye, '.
            'sd2.date as sdis2_date, sdis2.fully_specified_name as sdis2_fully_specified_name, sd2.eye_id as sdis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':sdis0' => 1,
                ':sdis1' => 2,
                ':sdis2' => 3,
                ':start_date' => '2002-05-10',
            ), $whereParams);

        $this->assertEquals(array(
                'sdis0.id is not null',
                'sdis1.id is not null',
                'sdis2.id is not null',
            ), $or_conditions);
    }

    public function testJoinDisorders_EndDate()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 2))
                ->method('leftJoin')
                ->with('secondary_diagnosis sd'.$i, 'sd'.$i.'.patient_id = p.id and sd'.$i.'.disorder_id = :sdis'.$i.' and sd'.$i.'.date <= :end_date');

            $query->expects($this->at(($i * 2) + 1))
                ->method('leftJoin')
                ->with('disorder sdis'.$i, 'sdis'.$i.'.id = sd'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'and';
        $r->end_date = '19 May 2002';

        $select = 'p.id, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('Secondary', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.id, c.first_name, c.last_name, p.dob, sd0.date as sdis0_date, sdis0.fully_specified_name as sdis0_fully_specified_name, '.
            'sd0.eye_id as sdis0_eye, sd1.date as sdis1_date, sdis1.fully_specified_name as sdis1_fully_specified_name, sd1.eye_id as sdis1_eye, '.
            'sd2.date as sdis2_date, sdis2.fully_specified_name as sdis2_fully_specified_name, sd2.eye_id as sdis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':sdis0' => 1,
                ':sdis1' => 2,
                ':sdis2' => 3,
                ':end_date' => '2002-05-19 23:59:59',
            ), $whereParams);

        $this->assertEquals(array(
                'sdis0.id is not null',
                'sdis1.id is not null',
                'sdis2.id is not null',
            ), $or_conditions);
    }

    public function testJoinDisorders_StartDateAndEndDate()
    {
        $query = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'where', 'leftJoin', 'queryAll'))
            ->getMock();

        for ($i = 0;$i < 3;++$i) {
            $query->expects($this->at($i * 2))
                ->method('leftJoin')
                ->with('secondary_diagnosis sd'.$i, 'sd'.$i.'.patient_id = p.id and sd'.$i.'.disorder_id = :sdis'.$i.' and sd'.$i.'.date >= :start_date and sd'.$i.'.date <= :end_date');

            $query->expects($this->at(($i * 2) + 1))
                ->method('leftJoin')
                ->with('disorder sdis'.$i, 'sdis'.$i.'.id = sd'.$i.'.disorder_id');
        }

        $r = new ReportDiagnoses();

        $r->condition_type = 'and';
        $r->start_date = '10 May 2002';
        $r->end_date = '19 May 2002';

        $select = 'p.id, c.first_name, c.last_name, p.dob';
        $whereParams = array();
        $or_conditions = array();

        $r->joinDisorders('Secondary', array(1, 2, 3), $select, $whereParams, $or_conditions, $query);

        $this->assertEquals(
            'p.id, c.first_name, c.last_name, p.dob, sd0.date as sdis0_date, sdis0.fully_specified_name as sdis0_fully_specified_name, '.
            'sd0.eye_id as sdis0_eye, sd1.date as sdis1_date, sdis1.fully_specified_name as sdis1_fully_specified_name, sd1.eye_id as sdis1_eye, '.
            'sd2.date as sdis2_date, sdis2.fully_specified_name as sdis2_fully_specified_name, sd2.eye_id as sdis2_eye',
            $select
        );

        $this->assertEquals(array(
                ':sdis0' => 1,
                ':sdis1' => 2,
                ':sdis2' => 3,
                ':start_date' => '2002-05-10',
                ':end_date' => '2002-05-19 23:59:59',
            ), $whereParams);

        $this->assertEquals(array(
                'sdis0.id is not null',
                'sdis1.id is not null',
                'sdis2.id is not null',
            ), $or_conditions);
    }

    public function testAddDiagnosisItem_Principal()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDiagnosesForRow'))
            ->getMock();

        $r->user_institution_id = 1;
        $r->user_selected_site_id = 1;

        $item = array(
            'id' => 1,
            'dob' => 'test2',
            'first_name' => 'test3',
            'last_name' => 'test4',
            'diagnoses' => 'test5',
        );

        $r->expects($this->once())
            ->method('getDiagnosesForRow')
            ->with('Principal', $item, array(1, 2, 3), array())
            ->will($this->returnValue(array(12345 => 'blah')));

        $r->principal = array(1, 2, 3);
        $r->diagnoses = array();

        $r->addDiagnosesResultItem($item);

        $this->assertEquals(array(
                12345 => array(
                    'dob' => 'test2',
                    'first_name' => 'test3',
                    'last_name' => 'test4',
                    'diagnoses' => array(
                        12345 => 'blah',
                    ),
                    'identifier' => '12345',
                    'all_ids' => 'ID (The Monachs Trust): 12345, National Health Service (The Monachs Trust): 54321, ',
                ),
            ), $r->diagnoses);
    }

    public function testAddDiagnosisItem_Secondary()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDiagnosesForRow'))
            ->getMock();

        $r->user_institution_id = 1;
        $r->user_selected_site_id = 1;

        $item = array(
            'id' => 1,
            'dob' => 'test2',
            'first_name' => 'test3',
            'last_name' => 'test4',
            'diagnoses' => 'test5',
        );

        $r->expects($this->once())
            ->method('getDiagnosesForRow')
            ->with('Secondary', $item, array(1, 2, 3), array())
            ->will($this->returnValue(array(12345 => 'blah')));

        $r->secondary = array(1, 2, 3);
        $r->diagnoses = array();

        $r->addDiagnosesResultItem($item);

        $this->assertEquals(array(
                12345 => array(
                    'dob' => 'test2',
                    'first_name' => 'test3',
                    'last_name' => 'test4',
                    'diagnoses' => array(
                        12345 => 'blah',
                    ),
                    'identifier' => '12345',
                    'all_ids' => 'ID (The Monachs Trust): 12345, National Health Service (The Monachs Trust): 54321, ',
                ),
            ), $r->diagnoses);
    }

    public function testAddDiagnosisItem_UniqueTS()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getDiagnosesForRow'))
            ->getMock();

        $r->user_institution_id = 1;
        $r->user_selected_site_id = 1;

        $item = array(
            'id' => 1,
            'dob' => 'test2',
            'first_name' => 'test3',
            'last_name' => 'test4',
            'diagnoses' => 'test5',
        );

        $r->expects($this->once())
            ->method('getDiagnosesForRow')
            ->with('Secondary', $item, array(1, 2, 3), array())
            ->will($this->returnValue(array(12345 => 'blah')));

        $r->secondary = array(1, 2, 3);
        $r->diagnoses = array(12345 => 'blah', 12346 => 'blah2');

        $r->addDiagnosesResultItem($item);

        $this->assertEquals(array(
                12347 => array(
                    'dob' => 'test2',
                    'first_name' => 'test3',
                    'last_name' => 'test4',
                    'diagnoses' => array(
                        12345 => 'blah',
                    ),
                    'identifier' => '12345',
                    'all_ids' => 'ID (The Monachs Trust): 12345, National Health Service (The Monachs Trust): 54321, ',
                ),
                12345 => 'blah',
                12346 => 'blah2',
            ), $r->diagnoses);
    }

    public function testGetDiagnosisForRow_Principal()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getFreeTimestampIndex'))
            ->getMock();

        $item = array(
            'pdis0_date' => '10 May 2002',
            'pdis0_fully_specified_name' => 'bob',
            'pdis0_eye' => 1,
        );

        $diagnoses = array(1, 2, 3);
        $list = array(1);

        $r->expects($this->once())
            ->method('getFreeTimestampIndex')
            ->with('10 May 2002', $diagnoses)
            ->will($this->returnValue(32431241));

        $diagnoses = $r->getDiagnosesForRow('Principal', $item, $list, $diagnoses);

        $this->assertEquals(array(
                0 => 1,
                1 => 2,
                2 => 3,
                32431241 => array(
                    'type' => 'Principal',
                    'disorder' => 'bob',
                    'date' => '10 May 2002',
                    'eye' => 'Left',
                ),
            ), $diagnoses);
    }

    public function testGetDiagnosisForRow_Secondary()
    {
        $r = $this->getMockBuilder('ReportDiagnoses')
            ->disableOriginalConstructor()
            ->setMethods(array('getFreeTimestampIndex'))
            ->getMock();

        $item = array(
            'sdis0_date' => '10 May 2002',
            'sdis0_fully_specified_name' => 'bob',
            'sdis0_eye' => 1,
        );

        $diagnoses = array(1, 2, 3);
        $list = array(1);

        $r->expects($this->once())
            ->method('getFreeTimestampIndex')
            ->with('10 May 2002', $diagnoses)
            ->will($this->returnValue(32431241));

        $diagnoses = $r->getDiagnosesForRow('Secondary', $item, $list, $diagnoses);

        $this->assertEquals(array(
                0 => 1,
                1 => 2,
                2 => 3,
                32431241 => array(
                    'type' => 'Secondary',
                    'disorder' => 'bob',
                    'date' => '10 May 2002',
                    'eye' => 'Left',
                ),
            ), $diagnoses);
    }

    //This test needs fixing to account for time zones
//    public function testGetFreeTimestampIndex()
//    {
//        $r = new ReportDiagnoses();
//
//        $this->assertEquals(1356998400, $r->getFreeTimestampIndex('2013-01-01', array()));
//        $this->assertEquals(1356998401, $r->getFreeTimestampIndex('2013-01-01', array(1356998400 => 'foo')));
//        $this->assertEquals(1356998402, $r->getFreeTimestampIndex('2013-01-01', array(1356998400 => 'foo', '1356998401' => 'bar')));
//        $this->assertEquals(1356998403, $r->getFreeTimestampIndex('2013-01-01', array(1356998400 => 'foo', '1356998401' => 'bar', '1356998402' => 'blah')));
//    }

    public function testDescription_or()
    {
        $r = new ReportDiagnoses();
        $r->condition_type = 'or';

        $this->assertRegExp('/Patients with any /', $r->description());
    }

    public function testDescription_and()
    {
        $r = new ReportDiagnoses();
        $r->condition_type = 'and';

        $this->assertRegExp('/Patients with all /', $r->description());
    }

    public function testDescription_Principal()
    {
        $r = new ReportDiagnoses();
        $r->condition_type = 'and';
        $r->principal = array(1, 2);

        $this->assertRegExp('/Myopia \(Principal\)/', $r->description());
        $this->assertRegExp('/Retinal lattice degeneration \(Principal\)/', $r->description());
    }

    public function testDescription_Secondary()
    {
        $r = new ReportDiagnoses();
        $r->condition_type = 'and';
        $r->secondary = array(1, 2);

        $this->assertRegExp('/Myopia \(Secondary\)/', $r->description());
        $this->assertRegExp('/Retinal lattice degeneration \(Secondary\)/', $r->description());
    }

    public function testDescription_Dates()
    {
        $r = new ReportDiagnoses();
        $r->condition_type = 'and';
        $r->secondary = array(1, 2);
        $r->start_date = '10 May 2002';
        $r->end_date = '19 May 2002';

        $this->assertRegExp('/Between 10 May 2002 and 19 May 2002/', $r->description());
    }

    public function testToCSV()
    {
        $r = new ReportDiagnoses();

        $r->user_institution_id = 1;
        $r->user_selected_site_id = 1;

        $r->principal = array(1, 2, 3);
        $r->secondary = array(4, 5, 6);
        $r->start_date = '10 May 2002';
        $r->end_date = '19 May 2002';

        $r->diagnoses = array(
            array(
                'identifier' => '12345',
                'dob' => '1 Jan 1980',
                'first_name' => 'Jim',
                'last_name' => 'Jones',
                'diagnoses' => array(
                    array(
                        'eye' => 'Left',
                        'disorder' => 'one',
                        'type' => 'Principal',
                    ),
                    array(
                        'eye' => 'Right',
                        'disorder' => 'two',
                        'type' => 'Secondary',
                    ),
                    array(
                        'eye' => 'Both',
                        'disorder' => 'bloo',
                        'type' => 'Principal',
                    ),
                ),
                'all_ids' => '12345, ',
            ),
        );

        $csv = $r->toCSV();

        $this->assertEquals('Patients with all of these diagnoses:
Myopia (Principal)
Retinal lattice degeneration (Principal)
Posterior vitreous detachment (Principal)
Vitreous haemorrhage (Secondary)
Essential hypertension (Secondary)
Diabetes mellitus type 1 (Secondary)
Between 10 May 2002 and 19 May 2002

ID,Date of Birth,First Name,Last Name,Date,Diagnoses,Patient IDs
"12345","1 Jan 1980","Jim","Jones","1 Jan 1970","Left one (Principal)","12345, "
"12345","1 Jan 1980","Jim","Jones","1 Jan 1970","Right two (Secondary)","12345, "
"12345","1 Jan 1980","Jim","Jones","1 Jan 1970","Both bloo (Principal)","12345, "
', $csv);
    }

    public function testToCSV_All()
    {
        $r = new ReportDiagnoses();
        $r->principal = array(1, 2);
        $r->secondary = array(4, 5);
        $r->all = array(3, 6);
        $r->start_date = '10 May 2002';
        $r->end_date = '19 May 2002';

        $r->diagnoses = array(
            array(
                'hos_num' => 12345,
                'dob' => '1 Jan 1980',
                'first_name' => 'Jim',
                'last_name' => 'Jones',
                'diagnoses' => array(
                    array(
                        'eye' => 'Left',
                        'disorder' => 'one',
                        'type' => 'Principal',
                    ),
                    array(
                        'eye' => 'Right',
                        'disorder' => 'two',
                        'type' => 'Secondary',
                    ),
                    array(
                        'eye' => 'Both',
                        'disorder' => 'bloo',
                        'type' => 'Principal',
                    ),
                ),
            ),
        );

        $csv = $r->toCSV();

        $this->assertEquals('Patients with all of these diagnoses:
Myopia (Principal)
Retinal lattice degeneration (Principal)
Vitreous haemorrhage (Secondary)
Essential hypertension (Secondary)
Posterior vitreous detachment (Principal or Secondary)
Diabetes mellitus type 1 (Principal or Secondary)
Between 10 May 2002 and 19 May 2002

'.Patient::model()->getAttributeLabel('hos_num').',Date of Birth,First Name,Last Name,Date,Diagnoses
"12345","1 Jan 1980","Jim","Jones","1 Jan 1970","Left one (Principal)"
"12345","1 Jan 1980","Jim","Jones","1 Jan 1970","Right two (Secondary)"
"12345","1 Jan 1980","Jim","Jones","1 Jan 1970","Both bloo (Principal)"
', $csv);
    }

    public function testRun_Principal_And()
    {
        $r = new ReportDiagnoses();
        $r->principal = array(1, 2, 3);
        $r->start_date = date('j M Y', strtotime('-35 days'));
        $r->end_date = date('j M Y');
        $r->condition_type = 'and';

        $r->run();

        $this->assertEmpty($r->diagnoses);

        $r->principal = array(1, 2);

        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertEquals('12345', $row['identifier']);
        $this->assertEquals('1970-01-01', $row['dob']);
        $this->assertEquals('Jim', $row['first_name']);
        $this->assertEquals('Aylward', $row['last_name']);
        $this->assertCount(2, $row['diagnoses']);

        $first = array_shift($row['diagnoses']);

        $this->assertEquals('Principal', $first['type']);
        $this->assertEquals('Myopia (disorder)', $first['disorder']);
        $this->assertRegExp('/^'.date('Y-m-d').'/', $first['date']);
        $this->assertEquals('Left', $first['eye']);

        $second = array_shift($row['diagnoses']);

        $this->assertEquals('Principal', $second['type']);
        $this->assertEquals('Retinal lattice degeneration (disorder)', $second['disorder']);
        $this->assertRegExp('/^'.date('Y-m-d').'/', $second['date']);
        $this->assertEquals('Both', $second['eye']);
    }

    public function testRun_Secondary_Or()
    {
        $r = new ReportDiagnoses();
        $r->secondary = array(1, 2, 3);
        $r->start_date = date('j M Y', strtotime('-35 days'));
        $r->end_date = date('j M Y');
        $r->condition_type = 'or';

        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertEquals('12345', $row['identifier']);
        $this->assertEquals('1970-01-01', $row['dob']);
        $this->assertEquals('Jim', $row['first_name']);
        $this->assertEquals('Aylward', $row['last_name']);
        $this->assertCount(3, $row['diagnoses']);

        $first = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $first['type']);
        $this->assertEquals('Posterior vitreous detachment (disorder)', $first['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-22 days')), $first['date']);
        $this->assertEquals('Both', $first['eye']);

        $second = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $second['type']);
        $this->assertEquals('Retinal lattice degeneration (disorder)', $second['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-12 days')), $second['date']);
        $this->assertEquals('Right', $second['eye']);

        $third = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $third['type']);
        $this->assertEquals('Myopia (disorder)', $third['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-10 days')), $third['date']);
        $this->assertEquals('Left', $third['eye']);
    }

    public function testRun_Secondary_And()
    {
        $r = new ReportDiagnoses();
        $r->secondary = array(1, 2, 3, 4);
        $r->start_date = date('j M Y', strtotime('-35 days'));
        $r->end_date = date('j M Y');
        $r->condition_type = 'and';

        $r->run();

        $this->assertEmpty($r->diagnoses);

        $r->secondary = array(1, 2, 3);

        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertEquals('12345', $row['identifier']);
        $this->assertEquals('1970-01-01', $row['dob']);
        $this->assertEquals('Jim', $row['first_name']);
        $this->assertEquals('Aylward', $row['last_name']);
        $this->assertCount(3, $row['diagnoses']);

        $first = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $first['type']);
        $this->assertEquals('Posterior vitreous detachment (disorder)', $first['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-22 days')), $first['date']);
        $this->assertEquals('Both', $first['eye']);

        $second = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $second['type']);
        $this->assertEquals('Retinal lattice degeneration (disorder)', $second['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-12 days')), $second['date']);
        $this->assertEquals('Right', $second['eye']);

        $third = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $third['type']);
        $this->assertEquals('Myopia (disorder)', $third['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-10 days')), $third['date']);
        $this->assertEquals('Left', $third['eye']);
    }

    public function testRun_All_Or()
    {
        $r = new ReportDiagnoses();
        $r->all = array(1, 2, 3);
        $r->start_date = date('j M Y', strtotime('-35 days'));
        $r->end_date = date('j M Y');
        $r->condition_type = 'or';

        $r->run();

        $this->assertCount(5, $r->diagnoses);

        $row = array_shift($r->diagnoses);

        $this->assertEquals('12345', $row['hos_num']);
        $this->assertEquals('1970-01-01', $row['dob']);
        $this->assertEquals('Jim', $row['first_name']);
        $this->assertEquals('Aylward', $row['last_name']);
        $this->assertCount(5, $row['diagnoses']);

        $first = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $first['type']);
        $this->assertEquals('Posterior vitreous detachment (disorder)', $first['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-22 days')), $first['date']);
        $this->assertEquals('Both', $first['eye']);

        $second = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $second['type']);
        $this->assertEquals('Retinal lattice degeneration (disorder)', $second['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-12 days')), $second['date']);
        $this->assertEquals('Right', $second['eye']);

        $third = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $third['type']);
        $this->assertEquals('Myopia (disorder)', $third['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-10 days')), $third['date']);
        $this->assertEquals('Left', $third['eye']);

        $fourth = array_shift($row['diagnoses']);

        $this->assertEquals('Principal', $fourth['type']);
        $this->assertEquals('Myopia (disorder)', $fourth['disorder']);
        $this->assertRegExp('/^'.date('Y-m-d').'/', $fourth['date']);
        $this->assertEquals('Left', $fourth['eye']);

        $fifth = array_shift($row['diagnoses']);

        $this->assertEquals('Principal', $fifth['type']);
        $this->assertEquals('Retinal lattice degeneration (disorder)', $fifth['disorder']);
        $this->assertRegExp('/^'.date('Y-m-d').'/', $fifth['date']);
        $this->assertEquals('Both', $fifth['eye']);
    }

    public function testRun_All_And()
    {
        $r = new ReportDiagnoses();
        $r->secondary = array(1, 2);
        $r->start_date = date('j M Y', strtotime('-35 days'));
        $r->end_date = date('j M Y');
        $r->condition_type = 'and';

        $r->secondary = array(1, 2);

        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertEquals('12345', $row['hos_num']);
        $this->assertEquals('1970-01-01', $row['dob']);
        $this->assertEquals('Jim', $row['first_name']);
        $this->assertEquals('Aylward', $row['last_name']);
        $this->assertCount(2, $row['diagnoses']);

        $first = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $first['type']);
        $this->assertEquals('Retinal lattice degeneration (disorder)', $first['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-12 days')), $first['date']);
        $this->assertEquals('Right', $first['eye']);

        $second = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $second['type']);
        $this->assertEquals('Myopia (disorder)', $second['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-10 days')), $second['date']);
        $this->assertEquals('Left', $second['eye']);
    }

    public function testRun_Both_And()
    {
        $r = new ReportDiagnoses();
        $r->principal = array(1, 2);
        $r->secondary = array(3, 4);
        $r->start_date = date('j M Y', strtotime('-35 days'));
        $r->end_date = date('j M Y');
        $r->condition_type = 'and';

        $r->run();

        $this->assertEmpty($r->diagnoses);

        $r->secondary = array(3);

        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertEquals('12345', $row['identifier']);
        $this->assertEquals('1970-01-01', $row['dob']);
        $this->assertEquals('Jim', $row['first_name']);
        $this->assertEquals('Aylward', $row['last_name']);
        $this->assertCount(3, $row['diagnoses']);

        $first = array_shift($row['diagnoses']);

        $this->assertEquals('Secondary', $first['type']);
        $this->assertEquals('Posterior vitreous detachment (disorder)', $first['disorder']);
        $this->assertEquals(date('Y-m-d', strtotime('-22 days')), $first['date']);
        $this->assertEquals('Both', $first['eye']);

        $second = array_shift($row['diagnoses']);

        $this->assertEquals('Principal', $second['type']);
        $this->assertEquals('Myopia (disorder)', $second['disorder']);
        $this->assertRegExp('/^'.date('Y-m-d').'/', $second['date']);
        $this->assertEquals('Left', $second['eye']);

        $third = array_shift($row['diagnoses']);

        $this->assertEquals('Principal', $third['type']);
        $this->assertEquals('Retinal lattice degeneration (disorder)', $third['disorder']);
        $this->assertRegExp('/^'.date('Y-m-d').'/', $second['date']);
        $this->assertEquals('Both', $third['eye']);
    }

    public function testRun_StartDate()
    {
        $r = new ReportDiagnoses();
        $r->secondary = array(1, 2, 3);
        $r->start_date = date('j M Y', strtotime('-15 days'));
        $r->end_date = date('j M Y');
        $r->condition_type = 'or';

        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertCount(2, $row['diagnoses']);

        $r->start_date = date('j M Y', strtotime('-11 days'));
        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertCount(1, $row['diagnoses']);

        $r->start_date = date('j M Y', strtotime('-1 day'));
        $r->run();

        $this->assertCount(0, $r->diagnoses);
    }

    public function testRun_EndDate()
    {
        $r = new ReportDiagnoses();
        $r->secondary = array(1, 2, 3);
        $r->start_date = date('j M Y', strtotime('-30 days'));
        $r->end_date = date('j M Y');
        $r->condition_type = 'or';

        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertCount(3, $row['diagnoses']);

        $r->end_date = date('j M Y', strtotime('-22 days'));
        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertCount(1, $row['diagnoses']);

        $r->end_date = date('j M Y', strtotime('-11 days'));
        $r->run();

        $this->assertCount(1, $r->diagnoses);

        $row = array_pop($r->diagnoses);

        $this->assertCount(2, $row['diagnoses']);

        $r->end_date = date('j M Y', strtotime('-29 days'));
        $r->run();

        $this->assertCount(0, $r->diagnoses);
    }
}
