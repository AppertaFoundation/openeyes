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

use OEModule\OphCiExamination\models\PrismFusionRange;
use OEModule\OphCiExamination\models\PrismFusionRange_Entry;
use OEModule\OphCiExamination\models\traits\HasCorrectionType;
use OEModule\OphCiExamination\tests\traits\InteractsWithPrismFusionRange;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasCorrectionTypeAttributeToTest;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

/**
 * Class PrismFusionRange_EntryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\PrismFusionRange_Entry
 * @group sample-data
 * @group strabismus
 * @group prism-fusion-range
 * @group pfr
 */
class PrismFusionRange_EntryTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use InteractsWithPrismFusionRange;
    use HasCorrectionTypeAttributeToTest;
    use HasWithHeadPostureAttributesToTest;

    protected $element_cls = PrismFusionRange_Entry::class;

    /** @test */
    public function uses_has_correction_type_trait()
    {
        $uses = static::classUsesRecursive($this->getElementInstance());
        $this->assertContains(HasCorrectionType::class, $uses);
    }

    public function prism_over_validation_provider()
    {
        return [
            [\Eye::RIGHT, true],
            [\Eye::LEFT, true],
            ['foo', false, 'invalid'],
            ['', false, 'cannot be blank']
        ];
    }

    /**
     * @param $value
     * @param $expected_valid
     * @param string $expected_error_partial
     * @test
     * @dataProvider prism_over_validation_provider
     */
    public function prism_over_validation($value, $expected_valid, $expected_error_partial = '')
    {
        $instance = $this->getElementInstance();

        $instance->prism_over_eye_id = $value;
        if ($expected_valid) {
            $this->assertAttributeValid($instance, 'prism_over_eye_id');
        } else {
            $this->assertAttributeInvalid($instance, 'prism_over_eye_id', $expected_error_partial);
        }
    }

    public function horizontal_attributes_provider()
    {
        return [
            ['near_bi'],
            ['near_bo'],
            ['distance_bi'],
            ['distance_bo']
        ];
    }

    /**
     * @param $attr
     * @test
     * @dataProvider horizontal_attributes_provider
     */
    public function max_horizontal_value_validation($attr)
    {
        $this->assertMaxValidation($this->getElementInstance(), $attr, 45);
    }

    /**
     * @param $attr
     * @test
     * @dataProvider horizontal_attributes_provider
     */
    public function min_horizontal_value_validation($attr)
    {
        $this->assertMinValidation($this->getElementInstance(), $attr, 1);
    }

    public function vertical_attributes_provider()
    {
        return [
            ['near_bu'],
            ['near_bd'],
            ['distance_bu'],
            ['distance_bd']
        ];
    }

    /**
     * @param $attr
     * @test
     * @dataProvider vertical_attributes_provider
     */
    public function max_vertical_value_validation($attr)
    {
        $this->assertMaxValidation($this->getElementInstance(), $attr, 25);
    }

    /**
     * @param $attr
     * @test
     * @dataProvider vertical_attributes_provider
     */
    public function min_vertical_value_validation($attr)
    {
        $this->assertMinValidation($this->getElementInstance(), $attr, 1);
    }

    public function at_least_one_attr_provider()
    {
        return [
            [['prism_over_eye_id'], false],
            [['prism_over_eye_id', 'near_bi'], true],
            [['prism_over_eye_id', 'near_bo'], true],
            [['prism_over_eye_id', 'near_bu'], true],
            [['prism_over_eye_id', 'near_bd'], true],
            [['prism_over_eye_id', 'distance_bi'], true],
            [['prism_over_eye_id', 'distance_bo'], true],
            [['prism_over_eye_id', 'distance_bu'], true],
            [['prism_over_eye_id', 'distance_bd'], true]
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
        $data = $this->generatePrismFusionRangeEntryData();
        foreach ($attrs as $attr) {
            $instance->$attr = $data[$attr];
        }

        $this->assertEquals($expected, $instance->validate());
    }
}
