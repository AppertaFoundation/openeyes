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
 * Class OERequiredIfOtherAttributesEmptyValidatorTest
 * @covers OERequiredIfOtherAttributesEmptyValidator
 * @group sample-data
 * @group strabismus
 */
class OERequiredIfOtherAttributesEmptyValidatorTest extends TestCase
{
    /** @test */
    public function error_for_null_dependency()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'dependent_attr' => null
        ]);
        $validator = $this->getValidatorExpectingErrorForAttribute($obj, 'attr', ['dependent_attr']);

        $validator->validate($obj);
    }

    /** @test */
    public function error_for_empty_string_dependency()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'dependent_attr' => ''
        ]);
        $validator = $this->getValidatorExpectingErrorForAttribute($obj, 'attr', ['dependent_attr']);

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_for_zero_value_dependency()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'dependent_attr' => 0
        ]);
        $validator = $this->getValidatorNotExpectingErrorForAttribute($obj, 'attr', ['dependent_attr']);

        $validator->validate($obj);
    }

    /** @test */
    public function error_for_empty_array_dependency()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'dependent_attr' => []
        ]);
        $validator = $this->getValidatorExpectingErrorForAttribute($obj, 'attr', ['dependent_attr']);

        $validator->validate($obj);
    }

    /** @test */
    public function error_for_multiple_empty_dependencies()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'dependent_attr1' => [],
            'dependent_attr2' => '',
            'ignored_attr' => 'foobar'
        ]);

        $validator = $this->getValidatorExpectingErrorForAttribute($obj, 'attr', ['dependent_attr1', 'dependent_attr2']);

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_with_one_filled_dependency()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => '',
            'dependent_attr1' => [],
            'dependent_attr2' => 'foobar',
            'ignored_attr' => 'baz'
        ]);

        $validator = $this->getValidatorNotExpectingErrorForAttribute($obj, 'attr', ['dependent_attr1', 'dependent_attr2']);

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_when_attribute_filled()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => 'foobar',
            'dependent_attr' => null
        ]);
        $validator = $this->getValidatorNotExpectingErrorForAttribute($obj, 'attr', ['dependent_attr']);

        $validator->validate($obj);
    }

    /** @test */
    public function error_when_attribute_filled_with_empty_array()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => [],
            'dependent_attr' => null
        ]);
        $validator = $this->getValidatorExpectingErrorForAttribute($obj, 'attr', ['dependent_attr']);

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_when_attribute_filled_with_populated_array()
    {
        $obj = $this->getStubbedActiveRecord([
            'attr' => ['foo'],
            'dependent_attr' => null
        ]);
        $validator = $this->getValidatorNotExpectingErrorForAttribute($obj, 'attr', ['dependent_attr']);

        $validator->validate($obj);
    }

    protected function getStubbedActiveRecord($attributes = [])
    {
        return ComponentStubGenerator::generate('CActiveRecord', $attributes);
    }

    protected function getValidatorExpectingErrorForAttribute($obj, $attribute, $dependent_attrs = [])
    {
        $validator = $this->getMockBuilder(OERequiredIfOtherAttributesEmptyValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();
        $validator->attributes = [$attribute];
        $validator->other_attributes = $dependent_attrs;
        $validator->expects($this->at(0))
            ->method('addError')
            ->with($obj, $attribute);

        return $validator;
    }

    protected function getValidatorNotExpectingErrorForAttribute($obj, $attribute, $dependent_attrs = [])
    {
        $validator = $this->getMockBuilder(OERequiredIfOtherAttributesEmptyValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();
        $validator->attributes = [$attribute];
        $validator->other_attributes = $dependent_attrs;
        $validator->expects($this->never())
            ->method('addError');

        return $validator;
    }
}
