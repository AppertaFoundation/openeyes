<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Class OERequiredTogetherValidatorTest
 * @covers OERequiredTogetherValidator
 */
class OERequiredTogetherValidatorTest extends TestCase
{
    /** @test */
    public function missing_attribute_marked_invalid()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'set_attr' => 'foo'
        ]);

        $validator = $this->getValidatorExpectingErrorForAttribute($obj, ['attr', 'set_attr'], ['attr']);
        $validator->validate($obj);
    }

    /** @test */
    public function multiple_missing_attribute_marked_invalid()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'attr2' => null,
            'attr3' => [],
            'set_attr' => 'foo',
            'set_attr2' => 'bar'
        ]);

        $validator = $this->getValidatorExpectingErrorForAttribute(
            $obj,
            ['attr', 'attr2', 'attr3', 'set_attr', 'set_attr2'],
            ['attr', 'attr2', 'attr3']
        );

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_when_all_empty()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'attr2' => null,
            'attr3' => [],
        ]);

        $validator = $this->getValidatorNotExpectingErrors(
            $obj,
            ['attr', 'attr2', 'attr3']
        );

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_when_all_set()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => 'foo',
            'attr2' => 0,
            'attr3' => ['baz'],
        ]);

        $validator = $this->getValidatorNotExpectingErrors(
            $obj,
            ['attr', 'attr2', 'attr3']
        );

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_when_validating_irrelevant_attribute()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'set_attr' => 'foo'
        ]);

        $validator = $this->getValidatorNotExpectingErrors($obj, ['attr', 'set_attr']);
        $validator->validate($obj, ['foo']);
    }

    /** @test */
    public function only_relevant_missing_attributes_marked_invalid()
    {
        $obj = $this->getStubbedActiveRecord([
            'misc_attr' => '',
            'required_together_unset' => null,
            'required_together_unset_2' => [],
            'required_together_set' => 'foo',
            'required_together_set_2' => 'bar'
        ]);

        $validator = $this->getValidatorExpectingErrorForAttribute(
            $obj,
            ['required_together_unset', 'required_together_unset_2', 'required_together_set', 'required_together_set_2'],
            ['required_together_unset', 'required_together_unset_2'] // misc attr not flagged
        );

        $validator->validate($obj, ['misc_attr', 'required_together_unset', 'required_together_unset_2']);
    }

    /**
     * Simple wrapper for creating an object that can be validated with the given attribute values
     *
     * @param array $attributes
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    protected function getStubbedActiveRecord($attributes = [])
    {
        return ComponentStubGenerator::generate('CActiveRecord', $attributes);
    }

    /**
     * Builds a partial mock of the validator.
     * The $attributes are the attributes that are required together
     * The $error_attributes are those attributes which are not set and are expected to be marked with
     * an error. The test will fail if they are not.
     *
     * @param $obj
     * @param $attributes
     * @param $error_attributes
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getValidatorExpectingErrorForAttribute($obj, $attributes, $error_attributes)
    {
        $validator = $this->getMockBuilder(OERequiredTogetherValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();
        $validator->attributes = $attributes;

        foreach ($error_attributes as $i => $attr) {
            $validator->expects($this->at($i))
                ->method('addError')
                ->with($obj, $attr);
        }

        return $validator;
    }

    /**
     * Builds a partial mock of the validator that will have an expectation failure
     * if any of the attributes trigger an error
     *
     * @param $obj
     * @param $attributes
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getValidatorNotExpectingErrors($obj, $attributes)
    {
        $validator = $this->getMockBuilder(OERequiredTogetherValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();
        $validator->attributes = $attributes;

        $validator->expects($this->never())
            ->method('addError');

        return $validator;
    }
}
