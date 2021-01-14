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

use OEModule\OphCiExamination\models\Retinoscopy;
use OEModule\OphCiExamination\models\Retinoscopy_WorkingDistance;
use OEModule\OphCiExamination\models\traits\HasSidedData;
use OEModule\OphCiExamination\tests\traits\InteractsWithRetinoscopy;

/**
 * Class RetinoscopyTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\Retinoscopy
 * @group sample-data
 * @group strabismus
 * @group retinoscopy
 */
class RetinoscopyTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use InteractsWithRetinoscopy;
    use \HasRelationOptionsToTest;

    protected $element_cls = Retinoscopy::class;

    protected static $required_sided_attributes = [
        'working_distance_id', 'angle', 'power1', 'power2', 'dilated', 'refraction'
    ];

    public function belongs_to_relations()
    {
        return [
            ['entry_type', Retinoscopy_WorkingDistance::class]
        ];
    }

    /** @test */
    public function uses_sided_data_trait()
    {
        $uses = static::classUsesRecursive($this->getElementInstance());
        $this->assertContains(HasSidedData::class, $uses);
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

    public function side_provider()
    {
        return [
            ['right'],
            ['left']
        ];
    }

    /**
     * @test
     * @dataProvider required_attribute_provider
     */
    public function sided_attribute_required($side, $attribute)
    {
        $instance = $this->getElementInstance();

        $instance->{"setHas" . ucfirst($side)}();
        $instance->$attribute = null;
        $this->assertAttributeInvalid($instance, $attribute, 'cannot be blank');

        $instance->$attribute = $this->generateRetinoscopyData()[$attribute];

        $this->assertAttributeValid($instance, $attribute);
    }

    /**
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
    public function working_distance_relation_defined($side)
    {
        $attr = "{$side}_working_distance_id";
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, $attr, Retinoscopy_WorkingDistance::class);
        $this->assertContains($attr, $instance->getSafeAttributeNames());
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function angle_range_validation($side)
    {
        $instance = $this->getElementInstanceWithSide($side);
        $this->assertMaxValidation($instance, "{$side}_angle", 180);
        $this->assertMinValidation($instance, "{$side}_angle", 0);
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function power1_range_validation($side)
    {
        $instance = $this->getElementInstanceWithSide($side);
        $this->assertMaxValidation($instance, "{$side}_power1", 30);
        $this->assertMinValidation($instance, "{$side}_power1", -30);
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function power2_range_validation($side)
    {
        $instance = $this->getElementInstanceWithSide($side);
        $this->assertMaxValidation($instance, "{$side}_power2", 30);
        $this->assertMinValidation($instance, "{$side}_power2", -30);
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function dilated_display($side)
    {
        $instance = $this->getElementInstanceWithSide($side);
        $dilated = $this->faker->randomElement([true, false]);
        $display_attribute = "display_{$side}_dilated";

        $this->assertEmpty($instance->$display_attribute);
        $instance->{"{$side}_dilated"} = $dilated;

        $this->assertEquals(
            $dilated ? "Dilated" : "Not dilated",
            $instance->$display_attribute
        );
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function power1_display($side)
    {
        $this->doPowerDisplayChecks($this->getElementInstanceWithSide($side), "{$side}_power1");
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function power2_display($side)
    {
        $this->doPowerDisplayChecks($this->getElementInstanceWithSide($side), "{$side}_power2");
    }

    public function letter_string_provider()
    {
        return [
            [['refraction' => 'foo'], [], 'Retinoscopy: R: foo, L: NR'],
            [[], ['refraction' => 'foo'], 'Retinoscopy: R: NR, L: foo'],
            [['refraction' => 'foo'], ['refraction' => 'bar'], 'Retinoscopy: R: foo, L: bar'],
            [['refraction' => 'foo', 'dilated' => true], ['refraction' => 'bar'], 'Retinoscopy: R: foo dilated, L: bar'],
            [['refraction' => 'foo'], ['refraction' => 'bar', 'dilated' => true], 'Retinoscopy: R: foo, L: bar dilated'],
            [
                ['refraction' => 'foo', 'comments' => 'foobar baz'],
                ['refraction' => 'bar', 'dilated' => true, 'comments' => 'baz'],
                'Retinoscopy: R: foo - foobar baz, L: bar dilated - baz'
            ],
        ];
    }

    /**
     * @param $right_attrs
     * @param $left_attrs
     * @param $expected
     * @test
     * @dataProvider letter_string_provider
     * @todo dilated tests!
     */
    public function letter_string($right_attrs, $left_attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $this->setElementSideAttrs($instance, 'right', $right_attrs);
        $this->setElementSideAttrs($instance, 'left', $left_attrs);

        $this->assertEquals($expected, $instance->letter_string);
    }

    protected function doPowerDisplayChecks($instance, $attribute)
    {
        $data = $this->generateRetinoscopyData();
        $instance->$attribute = $data[$attribute];

        $this->assertEquals(sprintf("%+.2f", $data[$attribute]), $instance->{"display_{$attribute}"});
    }

    protected function getElementInstanceWithSide($side)
    {
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($side)}();
        return $instance;
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
}
