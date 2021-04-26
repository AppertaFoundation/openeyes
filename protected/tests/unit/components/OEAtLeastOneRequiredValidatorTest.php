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
 * Class OEAtLeastOneRequiredValidatorTest
 * @covers OEAtLeastOneRequiredValidator
 */
class OEAtLeastOneRequiredValidatorTest extends TestCase
{
    /** @test */
    public function errors_when_attributes_not_set()
    {
        $obj = $this->getStubbedActiveRecord([
            'foo' => '',
            'bar' => null
        ]);

        $validator = $this->getValidatorExpectingErrorsForAttributes($obj, ['foo', 'bar']);

        $validator->validate($obj);
    }

    /** @test */
    public function error_for_empty_array_value()
    {
        $obj = $this->getStubbedActiveRecord([
            'foo' => '',
            'bar' => null,
            'baz' => []
        ]);

        $validator = $this->getValidatorExpectingErrorsForAttributes($obj, ['foo', 'bar', 'baz']);

        $validator->validate($obj);
    }

    /** @test */
    public function does_not_error_when_attribute_zero()
    {
        $obj = $this->getStubbedActiveRecord([
            'foo' => 0,
            'bar' => null
        ]);

        $validator = $this->getValidatorNotExpectingErrors(['foo', 'bar']);

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_with_populated_array()
    {
        $obj = $this->getStubbedActiveRecord([
            'foo' => [0],
            'bar' => null
        ]);

        $validator = $this->getValidatorNotExpectingErrors(['foo', 'bar']);

        $validator->validate($obj);
    }

    /** @test */
    public function no_error_when_validating_irrelevant_attribute()
    {
        $obj = $this->getStubbedActiveRecord([
            'foo' => '',
            'bar' => null
        ]);

        $validator = $this->getValidatorNotExpectingErrors(['foo', 'bar']);

        $validator->validate($obj, ['baz']);
    }

    /** @test */
    public function get_error_when_validating_relevant_attribute()
    {
        $obj = $this->getStubbedActiveRecord([
            'foo' => '',
            'bar' => null
        ]);

        $validator = $this->getValidatorExpectingErrorsForAttributes($obj, ['foo', 'bar'], ['bar']);

        $validator->validate($obj, ['bar']);
    }

    protected function getStubbedActiveRecord($attributes = [])
    {
        return ComponentStubGenerator::generate('CActiveRecord', $attributes);
    }

    protected function getValidatorExpectingErrorsForAttributes($obj, $attributes, $error_attributes = null)
    {
        if ($error_attributes === null) {
            $error_attributes = $attributes;
        }
        $validator = $this->getMockBuilder(OEAtLeastOneRequiredValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['addError'])
            ->getMock();
        $validator->attributes = $attributes;
        foreach ($error_attributes as $i => $attr) {
            $validator->expects($this->at($i))
                ->method('addError')
                ->with($obj, $attr);
        }

        return $validator;
    }

    protected function getValidatorNotExpectingErrors($attributes)
    {
        $validator = $this->getMockBuilder(OEAtLeastOneRequiredValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['addError'])
            ->getMock();
        $validator->attributes = $attributes;

        $validator->expects($this->never())
            ->method('addError');

        return $validator;
    }
}
