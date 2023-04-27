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


use OEModule\OphCiExamination\models\NinePositions_HorizontalEDeviation;
use OEModule\OphCiExamination\models\NinePositions_HorizontalXDeviation;
use OEModule\OphCiExamination\models\NinePositions_AlignmentForGaze;
use OEModule\OphCiExamination\models\NinePositions_VerticalDeviation;
use OEModule\OphCiExamination\tests\traits\InteractsWithNinePositions;

/**
 * Class NinePositions_AlignmentForGazeTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\NinePositions_AlignmentForGaze
 * @group sample-data
 * @group strabismus
 * @group nine-positions
 */
class NinePositions_AlignmentForGazeTest extends \ModelTestCase
{
    use \HasRelationOptionsToTest;
    use \WithFaker;
    use \HasModelAssertions;
    use InteractsWithNinePositions;

    protected $element_cls = NinePositions_AlignmentForGaze::class;

    public function auto_belongs_to_relations_provider()
    {
        return [
            ['vertical_deviation', 'vertical_deviation_id', NinePositions_VerticalDeviation::class],
            ['horizontal_e_deviation', 'horizontal_e_deviation_id', NinePositions_HorizontalEDeviation::class],
            ['horizontal_x_deviation', 'horizontal_x_deviation_id', NinePositions_HorizontalXDeviation::class]
        ];
    }

    /**
     * @test
     * @dataProvider auto_belongs_to_relations_provider
     * @param $relation
     * @param $attribute
     * @param $cls
     */
    public function auto_belongs_to_relations_defined($relation, $attribute, $cls)
    {
        $this->assertBelongsToCompletelyDefined($relation, $attribute, $cls);
    }

    public function at_least_one_attr_provider()
    {
        return [
            [['gaze_type'], false],
            [['gaze_type', 'horizontal_angle'], true],
            [['gaze_type', 'horizontal_prism_position'], true],
            [['gaze_type', 'horizontal_e_deviation_id'], true],
            [['gaze_type', 'horizontal_x_deviation_id'], true],
            [['gaze_type', 'vertical_angle'], true],
            [['gaze_type', 'vertical_prism_position'], true],
            [['gaze_type', 'vertical_deviation_id'], true],
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @test
     * @dataProvider at_least_one_attr_provider
     */
    public function validation_requires_at_least_one_value_to_be_recorded($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($this->getValidAttributesForAlignment($attrs));

        $this->assertEquals($expected, $instance->validate());
    }

    public function horizontal_prism_validation_provider()
    {
        return [
            [NinePositions_AlignmentForGaze::HORIZONTAL_PRISMS[array_rand(NinePositions_AlignmentForGaze::HORIZONTAL_PRISMS)], true],
            ['foo', false],
            [1, false, "must be string"]
        ];
    }

    /**
     * @test
     * @dataProvider horizontal_prism_validation_provider
     * @param $value
     * @param $expected
     * @param string $expected_msg
     */
    public function horizontal_prism_validation($value, $expected, $expected_msg = "invalid")
    {
        $instance = $this->getElementInstance();
        $instance->horizontal_prism_position = $value;
        if ($expected) {
            $this->assertAttributeValid($instance, 'horizontal_prism_position');
        } else {
            $this->assertAttributeInvalid($instance, 'horizontal_prism_position', $expected_msg);
        }
    }

    public function vertical_prism_validation_provider()
    {
        return [
            [NinePositions_AlignmentForGaze::VERTICAL_PRISMS[array_rand(NinePositions_AlignmentForGaze::VERTICAL_PRISMS)], true],
            ['foo', false],
            [1, false, 'must be string']
        ];
    }

    /**
     * @test
     * @dataProvider vertical_prism_validation_provider
     * @param $value
     * @param $expected
     * @param string $expected_msg
     */
    public function vertical_prism_validation($value, $expected, $expected_msg = 'invalid')
    {
        $instance = $this->getElementInstance();
        $instance->vertical_prism_position = $value;
        if ($expected) {
            $this->assertAttributeValid($instance, 'vertical_prism_position');
        } else {
            $this->assertAttributeInvalid($instance, 'vertical_prism_position', $expected_msg);
        }
    }

    public function horizontal_values_provider()
    {
        return [
            [['horizontal_prism_position'], true],
            [['horizontal_e_deviation_id'], true],
            [['horizontal_x_deviation_id'], true],
            [['horizontal_prism_position', 'horizontal_e_deviation_id'], false],
            [['horizontal_prism_position', 'horizontal_x_deviation_id'], false],
            [['horizontal_e_deviation_id', 'horizontal_x_deviation_id'], false],
            [['horizontal_prism_position', 'horizontal_e_deviation_id', 'horizontal_x_deviation_id'], false],

        ];
    }

    /**
     * @test
     * @dataProvider horizontal_values_provider
     * @param $attrs
     * @param $expected
     */
    public function only_one_horizontal_value_allowed($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes(
            $this->getValidAttributesForAlignment(array_merge($attrs, ['gaze_type']))
        );

        if ($expected) {
            $this->assertTrue($instance->validate());
        } else {
            $this->assertFalse($instance->validate());
        }
    }

    public function vertical_values_provider()
    {
        return [
            [['vertical_prism_position'], true],
            [['vertical_deviation_id'], true],
            [['vertical_prism_position', 'vertical_deviation_id'], false],
        ];
    }

    /**
     * @test
     * @dataProvider vertical_values_provider
     * @param $attrs
     * @param $expected
     */
    public function only_one_vertical_value_allowed($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes(
            $this->getValidAttributesForAlignment(array_merge($attrs, ['gaze_type']))
        );

        if ($expected) {
            $this->assertTrue($instance->validate(), print_r($instance->getErrors(), true));
        } else {
            $this->assertFalse($instance->validate());
        }
    }

    /** @test */
    public function gaze_type_is_validated()
    {
        $instance = $this->getElementInstance();
        $instance->gaze_type = $this->faker->word();
        $this->assertAttributeInvalid($instance, 'gaze_type', 'invalid');
    }

    /** @test */
    public function horizontal_angle_min_validation()
    {
        $this->assertMinValidation($this->element_cls, 'horizontal_angle', 0);
    }

    /** @test */
    public function horizontal_angle_max_validation()
    {
        $this->assertMaxValidation($this->element_cls, 'horizontal_angle', 90);
    }

    /** @test */
    public function vertical_angle_min_validation()
    {
        $this->assertMinValidation($this->element_cls, 'vertical_angle', 0);
    }

    /** @test */
    public function vertical_angle_max_validation()
    {
        $this->assertMaxValidation($this->element_cls, 'vertical_angle', 50);
    }

    /** @test */
    public function display_horizontal_no_values()
    {
        $instance = $this->getElementInstance();
        $this->assertEmpty($instance->display_horizontal);
    }

    /** @test */
    public function display_horizontal_with_prism_position()
    {
        $instance = $this->getElementInstance();
        $attrs = $this->getValidAttributesForAlignment(['horizontal_angle', 'horizontal_prism_position']);
        $instance->setAttributes($attrs);

        $this->assertEquals($attrs['horizontal_angle'] . $attrs['horizontal_prism_position'], $instance->display_horizontal);
    }

    /** @test */
    public function display_horizontal_with_e_deviation()
    {
        $instance = $this->getElementInstance();
        $attrs = $this->getValidAttributesForAlignment(['horizontal_angle']);
        $instance->setAttributes($attrs);
        $deviation = $this->getRandomLookup(NinePositions_HorizontalEDeviation::class);
        $instance->horizontal_e_deviation_id = $deviation->id;

        $this->assertEquals($attrs['horizontal_angle'] . $deviation->abbreviation, $instance->display_horizontal);
    }

    /** @test */
    public function display_horizontal_with_x_deviation()
    {
        $instance = $this->getElementInstance();
        $attrs = $this->getValidAttributesForAlignment(['horizontal_angle']);
        $instance->setAttributes($attrs);
        $deviation = $this->getRandomLookup(NinePositions_HorizontalXDeviation::class);
        $instance->horizontal_x_deviation_id = $deviation->id;

        $this->assertEquals($attrs['horizontal_angle'] . $deviation->abbreviation, $instance->display_horizontal);
    }

    /** @test */
    public function display_vertical_no_values()
    {
        $instance = $this->getElementInstance();
        $this->assertEmpty($instance->display_vertical);
    }

    /** @test */
    public function display_vertical_with_prism_position()
    {
        $instance = $this->getElementInstance();
        $attrs = $this->getValidAttributesForAlignment(['vertical_angle', 'vertical_prism_position']);
        $instance->setAttributes($attrs);

        $this->assertEquals($attrs['vertical_angle'] . $attrs['vertical_prism_position'], $instance->display_vertical);
    }

    /** @test */
    public function display_vertical_with_e_deviation()
    {
        $instance = $this->getElementInstance();
        $attrs = $this->getValidAttributesForAlignment(['vertical_angle']);
        $instance->setAttributes($attrs);
        $deviation = $this->getRandomLookup(NinePositions_VerticalDeviation::class);
        $instance->vertical_deviation_id = $deviation->id;

        $this->assertEquals($attrs['vertical_angle'] . $deviation->abbreviation, $instance->display_vertical);
    }
}
