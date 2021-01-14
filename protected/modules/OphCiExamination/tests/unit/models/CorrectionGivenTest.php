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

namespace OEModule\OphCiExamination\tests\unit\models;

use ComponentStubGenerator;
use OEModule\OphCiExamination\models\CorrectionGiven;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading;
use OEModule\OphCiExamination\tests\traits\InteractsWithCorrectionGiven;

/**
 * Class CorrectionGivenTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\CorrectionGiven
 * @group sample-data
 * @group strabismus
 * @group correction-given
 */
class CorrectionGivenTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use InteractsWithCorrectionGiven;

    protected $element_cls = CorrectionGiven::class;
    protected static $required_sided_attributes = ['as_found', 'refraction'];

    public function side_provider()
    {
        return [
            ['right'],
            ['left']
        ];
    }

    public function required_attribute_provider()
    {
        $attributes = [];
        foreach (['right', 'left'] as $side) {
            foreach (static::$required_sided_attributes as $attribute) {
                $attributes[] = [$side, "{$side}_{$attribute}"];
            }
        }
        return $attributes;
    }

    /**
     * @param $side
     * @param $attribute
     * @test
     * @dataProvider required_attribute_provider
     */
    public function sided_attribute_required($side, $attribute)
    {
        $instance = $this->getElementInstanceWithSide($side);
        $instance->$attribute = null;
        $this->assertAttributeInvalid($instance, $attribute, 'cannot be blank');

        $instance->$attribute = $this->generateCorrectionGivenDataForSide($side)[$attribute];
        $this->assertAttributeValid($instance, $attribute);
    }

    /**
     * @param $side
     * @param $attribute
     * @test
     * @dataProvider required_attribute_provider
     */
    public function other_side_not_required($side, $attribute)
    {
        $other_side = $side === 'right' ? 'left' : 'right';
        $instance = $this->getElementInstanceWithSide($other_side);
        $instance->{"setDoesNotHave" . ucfirst($side)}();

        $instance->$attribute = null;
        $this->assertAttributeValid($instance, $attribute);
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function as_found_element_type_invalid($side)
    {
        $instance = $this->getElementInstanceWithSide($side);
        $instance->{"{$side}_as_found"} = true;
        $attribute = "{$side}_as_found_element_type_id";

        $instance->$attribute = $this->getInvalidAsFoundElementType()->id;
        $this->assertAttributeInvalid($instance, $attribute, 'invalid');
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function as_found_element_type_valid($side)
    {
        $instance = $this->getElementInstanceWithSide($side);
        $instance->{"{$side}_as_found"} = true;
        $attribute = "{$side}_as_found_element_type_id";

        $instance->$attribute = $this->getValidAsFoundElementType()->id;
        $this->assertAttributeValid($instance, $attribute);
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function as_found_element_type_required($side)
    {
        $instance = $this->getElementInstance();
        $attribute = "{$side}_as_found_element_type_id";

        $instance->{"{$side}_as_found"} = true;
        $instance->$attribute = null;

        $this->assertAttributeInvalid($instance, $attribute, "cannot be blank");

        $instance->$attribute = $this->getValidAsFoundElementType()->id;
        $this->assertAttributeValid($instance, $attribute);
    }

    public function letter_string_provider()
    {
        return [
            [['refraction' => 'foo', 'as_found' => false], [], 'Correction Given: R: foo (adjusted), L: NR'],
            [[], ['refraction' => 'foo', 'as_found' => false], 'Correction Given: R: NR, L: foo (adjusted)'],
            [
                [
                    'refraction' => 'foo',
                    'as_found' => true,
                    'as_found_element_type' => $this->getStubbedNamedElementType('Bar')
                ],
                [],
                'Correction Given: R: foo (bar), L: NR'
            ],
            [
                ['refraction' => 'foo', 'as_found' => false],
                [
                    'refraction' => 'foobar',
                    'as_found' => true,
                    'as_found_element_type' => $this->getStubbedNamedElementType('Baz')
                ],
                'Correction Given: R: foo (adjusted), L: foobar (baz)'
            ],
        ];
    }

    /**
     * @param $right_attrs
     * @param $left_attrs
     * @param $expected
     * @test
     * @dataProvider letter_string_provider
     */
    public function letter_string($right_attrs, $left_attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $this->setElementSideAttrs($instance, 'right', $right_attrs);
        $this->setElementSideAttrs($instance, 'left', $left_attrs);

        $this->assertEquals($expected, $instance->letter_string);
    }

    protected function setElementSideAttrs($element, $side, $attrs)
    {
        if (count($attrs)) {
            $element->{"setHas" . ucfirst($side)}();
            foreach ($attrs as $attr => $val) {
                $element->{"{$side}_{$attr}"} = $val;
            }
        } else {
            $element->{"setDoesNotHave" . ucfirst($side)}();
        }
    }

    protected function getElementInstanceWithSide($side)
    {
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($side)}();
        return $instance;
    }

    protected function getStubbedNamedElementType($name)
    {
        return ComponentStubGenerator::generate(\ElementType::class, ['name' => $name]);
    }
}
