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
class OEDateCompareValidatorTest extends BasePHPUnit
{
    /**
     * @param bool $expect_message
     * @throws ReflectionException
     */
    public function doValidateAttribute($expect_message = false)
    {
        $attr = new DateTime('2015-05-06');
        $compare_attr = new DateTime('2015-05-06');

        $validator = $this->getMockBuilder('OEDateCompareValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('parseDateValue', 'doComparison', 'addError'))
            ->getMock();

        $validator->expects($this->at(0))
            ->method('parseDateValue')
            ->with($attr)
            ->will($this->returnValue($attr));

        $validator->expects($this->at(1))
            ->method('parseDateValue')
            ->with($compare_attr)
            ->will($this->returnValue($compare_attr));

        $validator->compareAttribute = 'compareAttr';

        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'compareAttr' => $compare_attr,
            'attr' => $attr,
        ));

        if ($expect_message) {
            $validator->expects($this->at(2))
                ->method('doComparison')
                ->with($attr, $compare_attr)
                ->will($this->returnValue('test message'));

            $validator->expects($this->at(3))
                ->method('addError')
                ->with($obj, 'attr', 'test message');
        } else {
            $validator->expects($this->at(2))
                ->method('doComparison')
                ->with($attr, $compare_attr)
                ->will($this->returnValue(null));
            $validator->expects($this->never())
                ->method('addError');
        }

        $m = static::getProtectedMethod($validator, 'validateAttribute');
        $m->invokeArgs($validator, array($obj, 'attr'));
    }

    /**
     * @covers OEDateCompareValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeSuccess()
    {
        $this->doValidateAttribute();
    }

    /**
     * @covers OEDateCompareValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeFailure()
    {
        $this->doValidateAttribute(true);
    }

    /**
     * @covers OEDateCompareValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeEmptyValue()
    {
        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'compareAttr' => 'misc',
            'attr' => '',
        ));

        $validator = $this->getMockBuilder('OEDateCompareValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();

        $validator->message = 'test message';

        $validator->expects($this->at(0))
            ->method('addError');

        $m = static::getProtectedMethod($validator, 'validateAttribute');

        $m->invokeArgs($validator, array($obj, 'attr'));
    }

    /**
     * @covers OEDateCompareValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeEmptyValue_allowed()
    {
        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'compareAttr' => 'misc',
            'attr' => '',
        ));

        $validator = $this->getMockBuilder('OEDateCompareValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();

        $validator->allowEmpty = true;
        $validator->message = 'test message';

        $validator->expects($this->never())
            ->method('addError');

        $m = static::getProtectedMethod($validator, 'validateAttribute');

        $m->invokeArgs($validator, array($obj, 'attr'));
    }

    /**
     * @covers OEDateCompareValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeCompareEmptyValue()
    {
        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'compareAttr' => '',
            'attr' => 'misc',
        ));

        $validator = $this->getMockBuilder('OEDateCompareValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();

        $validator->message = 'test message';

        $validator->expects($this->at(0))
            ->method('addError');

        $m = static::getProtectedMethod($validator, 'validateAttribute');

        $m->invokeArgs($validator, array($obj, 'attr'));
    }

    /**
     * @covers OEDateCompareValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeCompareEmptyValue_allowed()
    {
        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'compareAttr' => '',
            'attr' => 'misc',
        ));

        $validator = $this->getMockBuilder('OEDateCompareValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();

        $validator->allowCompareEmpty = true;
        $validator->message = 'test message';

        $validator->expects($this->never())
            ->method('addError');

        $m = static::getProtectedMethod($validator, 'validateAttribute');

        $m->invokeArgs($validator, array($obj, 'attr'));
    }

    /**
     * @covers OEDateCompareValidator
     * @throws ReflectionException
     */
    public function test_validateAttributeInvalidValues()
    {
        $obj = ComponentStubGenerator::generate('CActiveRecord', array(
            'compareAttr' => 'misc',
            'attr' => 'misc',
        ));

        $validator = $this->getMockBuilder('OEDateCompareValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();

        $validator->message = 'test message';

        $validator->expects($this->at(0))
            ->method('addError');

        $m = static::getProtectedMethod($validator, 'validateAttribute');

        $m->invokeArgs($validator, array($obj, 'attr'));
    }

    public function compareProvider()
    {
        return array(
            array(new DateTime('2015-05-06'), new DateTime('2015-05-06'), '=', true),
            array(new DateTime('2015-05-06'), new DateTime('2015-05-06'), '<=', true),
            array(new DateTime('2015-05-06'), new DateTime('2015-05-06'), '!=', false),
            array(new DateTime('2015-05-02'), new DateTime('2015-05-06'), '!=', true),
            array(new DateTime('2015-05-02'), new DateTime('2015-05-06'), '<', true),
            array(new DateTime('2015-05-02'), new DateTime('2015-05-06'), '>', false),
            array(new DateTime('2016-05-02'), new DateTime('2015-05-06'), '>', true),
        );
    }

    /**
     * @covers       OEDateCompareValidator
     * @dataProvider compareProvider
     * @throws CException
     */
    public function test_doComparison($value, $compare, $op, $pass)
    {
        $validator = new OEDateCompareValidator();
        $validator->operator = $op;

        if ($pass) {
            $this->assertNull($validator->doComparison($value, $compare));
        } else {
            $this->assertNotNull($validator->doComparison($value, $compare));
        }
    }
}
