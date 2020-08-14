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
class OETimeValidatorTest extends BasePHPUnit
{
    public function validateValueProvider()
    {
        return array(
            array('11:15', true),
            array('3pm', false),
            array('1115', false),
            array('15:12', true),
            array('23:60', false),
            array('8:04', true),
        );
    }

    /**
     * @covers OETimeValidator
     * @dataProvider validateValueProvider
     *
     * @param $time
     * @param $valid
     */
    public function test_validateValue($time, $valid)
    {
        $validator = new OETimeValidator();

        $this->assertEquals($valid, $validator->validateValue($time));
    }

    public function validateAttribute_empty_provider()
    {
        return array(
            array(false, 1),
            array(true, 0),
        );
    }

    /**
     * @covers OETimeValidator
     * @dataProvider validateAttribute_empty_provider
     *
     * @param bool $allowEmpty
     * @param $addError_count
     * @throws ReflectionException
     */
    public function test_validateAttribute_empty($allowEmpty, $addError_count)
    {
        $validator = $this->getMockBuilder('OETimeValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateValue', 'addError'))
            ->getMock();

        $validator->allowEmpty = $allowEmpty;

        $validator->expects($this->exactly($addError_count))
            ->method('addError');

        $validator->expects($this->never())
            ->method('validateValue');

        $m = static::getProtectedMethod($validator, 'validateAttribute');

        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'attr' => '',
        ));

        $m->invokeArgs($validator, array($obj, 'attr'));
    }

    public function validateAttribute_value_provider()
    {
        return array(
            array(false, 1),
            array(true, 0),
        );
    }

    /**
     * @covers       OETimeValidator
     * @dataProvider validateAttribute_value_provider
     *
     * @param $valid
     * @param $addError_count
     * @throws ReflectionException
     */
    public function test_validateAttribute_value($valid, $addError_count)
    {
        $validator = $this->getMockBuilder('OETimeValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateValue', 'addError'))
            ->getMock();

        $validator->allowEmpty = false;

        $validator->expects($this->once())
            ->method('validateValue')
            ->will($this->returnValue($valid));

        $validator->expects($this->exactly($addError_count))
            ->method('addError');

        $m = static::getProtectedMethod($validator, 'validateAttribute');

        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'attr' => 'anything',
        ));

        $m->invokeArgs($validator, array($obj, 'attr'));
    }
}
