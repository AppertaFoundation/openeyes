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
class Element_OphCiExamination_CurrentManagementPlan_Test extends ActiveRecordTestCase
{
    /**
     * @var Element_OphCiExamination_CurrentManagementPlan
     */
    protected $model;
    public $fixtures = array(
        'patient' => 'Patient',
    );

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan();
    }

    /**
     * @covers OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan::model
     */
    public function testModel()
    {
        $this->assertEquals('OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan', get_class($this->model), 'Class name should match model.');
        $this->assertTrue(is_subclass_of($this->model, 'SplitEventTypeElement'));
    }

    /**
     * @covers OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('et_ophciexamination_currentmanagementplan', $this->model->tableName());
    }

    /**
     * @covers OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan::getLatestIOP
     */
    public function testGetLatestIOP()
    {
        $patient = $this->patient('patient1');
        $api = $this->getMockBuilder('\OEModule\OphCiExamination\components\OphCiExamination_API')
            ->disableOriginalConstructor()
            //->setMethods(array('getIOPReadingLeft', 'getIOPReadingRight'))
            ->getMock();
        //$api = $this->getMock('\OEModule\OphCiExamination\components\OphCiExamination_API');

        $api->expects($this->any())->method('getIOPReadingLeft')
            ->with($this->equalTo($patient))
            ->will($this->returnValue('10'));
        $api->expects($this->any())->method('getIOPReadingRight')
            ->with($this->equalTo($patient))
            ->will($this->returnValue('20'));

        $result = $this->model->getLatestIOP($patient, $api);
        $expected = array('leftIOP' => '10', 'rightIOP' => '20');

        $this->assertIsArray($result);
        $this->assertEquals($expected, $result);
    }
}
