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

use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type;
use OEModule\OphCiExamination\tests\traits\InteractsWithRefraction;

/**
 * Class OphCiExamination_Refraction_ReadingTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading
 * @group sample-data
 * @group strabismus
 * @group refraction
 */
class OphCiExamination_Refraction_ReadingTest extends \ModelTestCase
{
    use InteractsWithRefraction;

    protected $element_cls = OphCiExamination_Refraction_Reading::class;

    public function required_attrs_provider()
    {
        return [
            ['sphere'],
            ['cylinder'],
            ['axis']
        ];
    }

    /**
     * @test
     * @dataProvider required_attrs_provider
     * @param $attr
     */
    public function required_attrs($attr)
    {
        $instance = $this->getElementInstance();
        $this->assertAttributeInvalid($instance, $attr, 'cannot be blank');
    }

    /** @test */
    public function sphere_maximum()
    {
        $this->assertMaxValidation($this->element_cls, 'sphere', 45);
    }

    /** @test */
    public function sphere_minimum()
    {
        $this->assertMinValidation($this->element_cls, 'sphere', -45);
    }

    /** @test */
    public function cylinder_maximum()
    {
        $this->assertMaxValidation($this->element_cls, 'cylinder', 25);
    }

    /** @test */
    public function cylinder_minimum()
    {
        $this->assertMinValidation($this->element_cls, 'cylinder', -25);
    }

    /** @test */
    public function axis_maximum()
    {
        $this->assertMaxValidation($this->element_cls, 'axis', 180);
    }

    /** @test */
    public function axis_minimum()
    {
        $this->assertMinValidation($this->element_cls, 'axis', -180);
    }

    /** @test */
    public function type_or_other_required()
    {
        $instance = $this->getElementInstance();
        $this->assertAttributeInvalid($instance, 'type_id', 'cannot be blank');
        $instance->type_other = 'foo';
        $this->assertAttributeValid($instance, 'type_id');
    }

    /** @test */
    public function type_validation()
    {
        $instance = $this->getElementInstance();
        $instance->type_id = 'foo';
        $this->assertAttributeInvalid($instance, 'type_id', 'invalid');
    }

    public function two_dp_format_provider()
    {
        return [
            [0, "+0.00"],
            [0.1, "+0.10"],
            [-20, "-20.00"],
            ['', ""]
        ];
    }

    /**
     * @test
     * @dataProvider two_dp_format_provider
     * @param $value
     * @param $expected
     */
    public function sphere_display($value, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->sphere = $value;
        $this->assertEquals($expected, $instance->sphere_display);
    }

    /**
     * @test
     * @dataProvider two_dp_format_provider
     * @param $value
     * @param $expected
     */
    public function cylinder_display($value, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->cylinder = $value;
        $this->assertEquals($expected, $instance->cylinder_display);
    }

    /** @test */
    public function spherical_equivalent_is_null_when_values_not_set()
    {
        $instance = $this->getElementInstance();

        $this->assertNull($instance->getSphericalEquivalent());
    }

    public function spherical_equivalent_provider()
    {
        return [
            ["0", "0", "0.00"],
            [-30, 15, "-22.50"],
            [-30, -15, "-37.50"],
            [12, 8, "+16.00"],
            [12, -8, "+8.00"],
            [2.25, -3.25, "+0.63"]
        ];
    }

    /**
     * @test
     * @dataProvider spherical_equivalent_provider
     */
    public function spherical_equivalent_calculation($sphere, $cylinder, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->sphere = $sphere;
        $instance->cylinder = $cylinder;

        $this->assertEquals($expected, $instance->getSphericalEquivalent());
    }

    /** @test */
    public function stringification_positive_values()
    {
        $instance = $this->getElementInstance();
        $attrs = $this->generateRefractionReadingData();
        // force positive
        foreach (['sphere', 'cylinder'] as $attr) {
            if ($attrs[$attr] < 0) {
                $attrs[$attr] *= -1;
            }
        }
        $type = OphCiExamination_Refraction_Type::model()->findByPk($attrs['type_id']);

        $instance->setAttributes($attrs);

        $this->assertEquals(sprintf(
            "+%.2f/+%.2f X %s° SE:%s %s",
            $attrs['sphere'],
            $attrs['cylinder'],
            $attrs['axis'],
            $instance->getSphericalEquivalent(),
            $type->name,
        ), (string) $instance);
    }

    /** @test */
    public function stringification_negative_values()
    {
        $instance = $this->getElementInstance();
        $attrs = $this->generateRefractionReadingData();
        // force negative
        foreach (['sphere', 'cylinder'] as $attr) {
            if ($attrs[$attr] > 0) {
                $attrs[$attr] *= -1;
            }
            elseif ($attrs[$attr] === 0) {
                $attrs[$attr] = -1;
            }
        }
        $type = OphCiExamination_Refraction_Type::model()->findByPk($attrs['type_id']);

        $instance->setAttributes($attrs);

        $this->assertEquals(sprintf(
            "%.2f/%.2f X %s° SE:%s %s",
            $attrs['sphere'],
            $attrs['cylinder'],
            $attrs['axis'],
            $instance->getSphericalEquivalent(),
            $type->name,
        ), (string) $instance);
    }
}
