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
class OEDatetimeValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers OEDatetimeValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeEmptyValue()
    {
        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'attr' => '',
        ));

        $validator = $this->getMockBuilder('OEDatetimeValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();

        $validator->message = 'test message';

        $validator->expects($this->at(0))
            ->method('addError');

        $validator->validateAttribute($obj, 'attr');
    }

    /**
     * @covers OEDatetimeValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeEmptyValue_allowed()
    {
        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'attr' => '',
        ));

        $validator = $this->getMockBuilder('OEDatetimeValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();

        $validator->allowEmpty = true;
        $validator->message = 'test message';

        $validator->expects($this->never())
            ->method('addError');

        $validator->validateAttribute($obj, 'attr');
    }

    /**
     * @covers OEDatetimeValidator
     * @throws ReflectionException
     */
    public function test_validateAttribute_valid()
    {
        $validator = $this->getMockBuilder('OEDatetimeValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('parseDateValue', 'addError'))
            ->getMock();

        $validator->expects($this->at(0))
            ->method('parseDateValue')
            ->with('test')
            ->will($this->returnValue(new DateTime()));

        $validator->expects($this->never())
            ->method('addError');

        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'attr' => 'test',
        ));

        $validator->validateAttribute($obj, 'attr');
    }

    /**
     * @covers OEDatetimeValidator
     * @throws ReflectionException
     */
    public function test_validateAttribute_invalid()
    {
        $validator = $this->getMockBuilder('OEDatetimeValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('parseDateValue', 'addError'))
            ->getMock();

        $validator->expects($this->at(0))
            ->method('parseDateValue')
            ->with('test')
            ->will($this->returnValue(false));

        $validator->expects($this->at(1))
            ->method('addError');

        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'attr' => 'test',
        ));

        $validator->validateAttribute($obj, 'attr');
    }
}
