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

use OEModule\OphCiExamination\models\HeadPosture;

/**
 * Class HeadPostureTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\HeadPosture
 * @group sample-data
 * @group strabismus
 * @group head-posture
 */
class HeadPostureTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;

    protected $element_cls = HeadPosture::class;

    /** @test */
    public function check_tilt_options_available_as_attribute()
    {
        $options = $this->getElementInstance()->tilt_options;
        $this->assertCount(2, $options);
        $this->assertDropdownOptionsHasCorrectKeys($options);
    }

    /** @test */
    public function check_turn_options_available_as_attribute()
    {
        $options = $this->getElementInstance()->turn_options;
        $this->assertCount(2, $options);
        $this->assertDropdownOptionsHasCorrectKeys($options);
    }

    /** @test */
    public function check_chin_options_available_as_attribute()
    {
        $options = $this->getElementInstance()->chin_options;
        $this->assertCount(2, $options);
        $this->assertDropdownOptionsHasCorrectKeys($options);
    }

    /** @test */
    public function make_sure_expected_attributes_are_safe()
    {
        $safe = $this->getElementInstance()->getSafeAttributeNames();

        foreach (['chin', 'tilt', 'turn', 'comments'] as $attr) {
            $this->assertContains($attr, $safe);
        }
    }

    /** @test */
    public function validation_error_is_set_when_no_attributes_set()
    {
        $instance = $this->getElementInstance();
        foreach ($instance->getSafeAttributeNames() as $attr) {
            $instance->$attr = '';
        }

        $result = $instance->validate();

        $this->assertFalse($result);
    }

    public function attribute_provider()
    {
        return [
            ['tilt'],
            ['turn'],
            ['chin']
        ];
    }

    /**
     * @test
     * @dataProvider attribute_provider
     * @param $attribute
     */
    public function errors_on_invalid_option_for_attribute($attribute)
    {
        $instance = $this->getElementInstance();
        $instance->$attribute = 'foo';

        $this->assertAttributeInvalid($instance, $attribute, 'is invalid');
    }

    public function right_and_left_display_provider()
    {
        return [
            [HeadPosture::RIGHT, HeadPosture::RIGHT_DISPLAY],
            [HeadPosture::LEFT, HeadPosture::LEFT_DISPLAY],
            ['', '-'],
            [rand(1, 5), '-']
        ];
    }

    /**
     * @test
     * @dataProvider right_and_left_display_provider
     * @param $value
     * @param $expected
     */
    public function turn_display_value_is_correct($value, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->turn = $value;

        $this->assertEquals($expected, $instance->display_turn);
    }

    /**
     * @test
     * @dataProvider right_and_left_display_provider
     * @param $value
     * @param $expected
     */
    public function tilt_display_value_is_correct($value, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->tilt = $value;

        $this->assertEquals($expected, $instance->display_tilt);
    }

    public function chin_display_provider()
    {
        return [
            [HeadPosture::ELEVATED, HeadPosture::ELEVATED_DISPLAY],
            [HeadPosture::DEPRESSED, HeadPosture::DEPRESSED_DISPLAY],
            ['', '-'],
            [rand(1, 5), '-']
        ];
    }

    /**
     * @test
     * @dataProvider chin_display_provider
     * @param $value
     * @param $expected
     */
    public function chin_display_value_is_correct($value, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->chin = $value;

        $this->assertEquals($expected, $instance->display_chin);
    }

    public function letterStringProvider()
    {
        return [
            [['tilt' => HeadPosture::RIGHT], 'Tilt: Right'],
            [['tilt' => HeadPosture::LEFT], 'Tilt: Left'],
            [['turn' => HeadPosture::LEFT], 'Turn: Left'],
            [['turn' => HeadPosture::LEFT, 'tilt' => HeadPosture::RIGHT], 'Tilt: Right, Turn: Left'],
            [['chin' => HeadPosture::ELEVATED, 'tilt' => HeadPosture::RIGHT], 'Tilt: Right, Chin: Elevated'],
            [['comments' => 'foo bar baz'], 'foo bar baz'],
            [['chin' => HeadPosture::ELEVATED, 'comments' => 'foobar'], 'Chin: Elevated - foobar'],
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @test
     * @dataProvider letterStringProvider
     */
    public function check_letter_string($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->attributes = $attrs;

        $this->assertEquals($expected, $instance->letter_string);
    }
}
