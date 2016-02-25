<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class Element_OphCiExamination_ColourVisionTest extends CDbTestCase
{
    public $fixtures = array(
            'methods' => 'OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Method',
    );

    public function testValidation_validatesReadings()
    {
        $lreading = $this->getMockBuilder('OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading')
                ->disableOriginalConstructor()
                ->setMethods(array('validate'))
                ->getMock();

        $lreading->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(false));

        $test = $this->getMockBuilder('OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision')
            ->disableOriginalConstructor()
            ->setMethods(array('hasLeft', 'hasRight'))
            ->getMock();
        $test->expects($this->any())
            ->method('hasLeft')
            ->will($this->returnValue(true));
        $test->expects($this->any())
                ->method('hasRight')
                ->will($this->returnValue(false));

        $test->left_readings = array($lreading);
        $this->assertFalse($test->validate());
    }

    public function testGetUnusedReadingMethods()
    {
        $test = new OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision();
        $test->left_readings = array(ComponentStubGenerator::generate('OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading', array('method' => $this->methods('method1'))));

        $this->assertEquals(array($this->methods('method2')), $test->getUnusedReadingMethods('left'), 'Left methods should be restricted');
        $this->assertEquals(array($this->methods('method1'), $this->methods('method2')), $test->getUnusedReadingMethods('right'), 'Right should return both methods');
    }
}
