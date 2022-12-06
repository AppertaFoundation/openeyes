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

/**
 * @group sample-data
 */
class PatientIdTest extends \PHPUnit_Framework_TestCase
{
    public function getModelProvider()
    {
        return array(
            array('Id', false),
            array('PasId', false),
            array('NHSNumber', false),
            array('HospitalNumber', false),
            array('Any', true),
        );
    }

    /**
     * @dataProvider getModelProvider
     *
     * @param $id_tag
     * @param $expect_exception
     */
    public function test_getModel($id_tag, $expect_exception)
    {
        $resolver_method = 'resolveModel'.$id_tag;

        $resource = $this->getMockBuilder('\\OEModule\\PASAPI\\resources\\PatientId')
            ->disableOriginalConstructor()
            ->setMethods(array($resolver_method))
            ->getMock();

        $resource->$id_tag = 'Test Value';

        if (!$expect_exception) {
            $test_result = new \Patient();
            $resource->expects($this->once())
                ->method($resolver_method)
                ->will($this->returnValue($test_result));

            $this->assertEquals($test_result, $resource->getModel());
        } else {
            $this->expectException('Exception');

            $resource->getModel();
        }
    }

    public function resolveModelId($pass)
    {
        $rc = new \ReflectionClass('\\OEModule\\PASAPI\\resources\\PatientId');
        $m = $rc->getMethod('resolveModelId');
        $m->setAccessible(true);

        $resource = $this->getMockBuilder('\\OEModule\\PASAPI\\resources\\PatientId')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelForClass', 'patientNotFound'))
            ->getMock();

        $resource->Id = 'testId';

        $patient = $this->getMockBuilder('Patient')
            ->disableOriginalConstructor()
            ->setMethods(array('findByPk'))
            ->getMock();

        $resource->expects($this->once())
            ->method('getModelForClass')
            ->with('Patient')
            ->will($this->returnValue($patient));

        if ($pass) {
            $resource->expects($this->never())
                ->method('patientNotFound');

            $result = \ComponentStubGenerator::generate('Patient', array('id' => 'testId'));
            $patient->expects($this->once())
                ->method('findByPk')
                ->will($this->returnValue($result));
            $this->assertEquals($result, $m->invoke($resource));
        } else {
            $resource->expects($this->once())
                ->method('patientNotFound');

            $patient->expects($this->once())
                ->method('findByPk')
                ->will($this->returnValue(null));
            $m->invoke($resource);
        }
    }

    public function test_resolveModelId_success()
    {
        $this->resolveModelId(true);
    }

    public function test_resolveModelId_fail()
    {
        $this->resolveModelId(false);
    }
}
