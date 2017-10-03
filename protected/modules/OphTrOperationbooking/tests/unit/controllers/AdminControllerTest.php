<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class AdminControllerTest.
 *
 * @group controllers
 */
class AdminControllerTest extends CTestCase
{
    public static function setupBeforeClass()
    {
        //Yii::import('application.modules.OphTrOperationbooking.components.*');
    }

    private $controller;
    private $audit;

    public function setUp()
    {
        /*
        $this->controller = $this->getMockBuilder('AdminController')
            ->disableOriginalConstructor()
            ->setMethods(array('render'))
            ->getMock();

        $this->audit = $this->getMock('Audit');
        */
        //Yii::app()->setComponent('audit',$this->audit);
    }

    public function tearDown()
    {
    }

    public function testActionViewERODRules()
    {
        //$this->audit->expects($this->once())->method('log')->with('admin','list','null',false,array('module'=>'OphTrOperationbooking','model'=>'OphTrOperationbooking_Operation_EROD_Rule'));

        //$this->controller->expects($this->once())->method('render')->with('erodrules');

        //$this->controller->actionViewERODRules();
    }

    public function testActionEditERODRule()
    {
    }
}
