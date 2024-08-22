<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphGeneric\tests\unit\models;

use OEModule\OphGeneric\models\HFA;
use OEModule\OphGeneric\models\HFAEntry;

/**
 * Class BirthHistoryTest
 *
 * @package OEModule\OphGeneric\tests\unit\models
 * @covers \OEModule\OphGeneric\models\HFAEntry
 * @group sample-data
 * @group ophgeneric
 * @group visual-fields
 * @group hfa
 * @group hfa-entry
 */
class HFAEntryTest extends \ModelTestCase
{
    use \HasRelationOptionsToTest;
    use \WithFaker;
    use \WithTransactions;

    protected $element_cls = HFAEntry::class;

    /** @test */
    public function validation_fails_when_no_attributes_set()
    {
        $instance = new HFAEntry();
        foreach ($instance->getSafeAttributeNames() as $attr) {
            $instance->$attr = '';
        }

        $result = $instance->validate();

        $this->assertFalse($result);
    }

    public function getAttributeData()
    {
        return [
            'valid input passes validation' => [
                function ($testCase) {
                    return [
                        [
                            'element_id' => HFA::factory()->create()->id,
                            'mean_deviation' => $testCase->getValidMeanDeviation(),
                            'visual_field_index' => $testCase->getValidVisualFieldIndex()
                        ],
                        true
                    ];
                }
            ],
            'missing element (hfa) fails validation' => [
                function ($testCase) {
                    return [
                        [
                            'mean_deviation' => $testCase->getValidMeanDeviation(),
                            'visual_field_index' => $testCase->getValidVisualFieldIndex()
                        ],
                        false
                    ];
                }
            ],
            'missing mean_deviation fails validation' => [
                function ($testCase) {
                    return [
                        [
                            'element_id' => HFA::factory()->create()->id,
                            'visual_field_index' => $testCase->getValidVisualFieldIndex()
                        ],
                        false
                    ];
                }
            ],
            'missing visual_field_index fails validation' => [
                function ($testCase) {
                    return [
                        [
                            'element_id' => HFA::factory()->create()->id,
                            'mean_deviation' => $testCase->getValidMeanDeviation(),
                        ],
                        false
                    ];
                }
            ],
            'out of range mean_deviation fails validation (max)' => [
                function ($testCase) {
                    return [
                        [
                            'element_id' => HFA::factory()->create()->id,
                            'mean_deviation' => HFAEntry::MEAN_DEVIATION_MAX + 1,
                            'visual_field_index' => $testCase->getValidVisualFieldIndex()
                        ],
                        false
                    ];
                }
            ],
            'out of range mean_deviation fails validation (min)' => [
                function ($testCase) {
                    return [
                        [
                            'element_id' => HFA::factory()->create()->id,
                            'mean_deviation' => HFAEntry::MEAN_DEVIATION_MIN - 1,
                            'visual_field_index' => $testCase->getValidVisualFieldIndex()
                        ],
                        false
                    ];
                }
            ],
            'out of range visual_field_index fails validation (max)' => [
                function ($testCase) {
                    return [
                        [
                            'element_id' => HFA::factory()->create()->id,
                            'mean_deviation' => $testCase->getValidMeanDeviation(),
                            'visual_field_index' => HFAEntry::VISUAL_FIELD_INDEX_MAX + 1
                        ],
                        false
                    ];
                }
            ],
            'out of range visual_field_index fails validation (min)' => [
                function ($testCase) {
                    return [
                        [
                            'element_id' => HFA::factory()->create()->id,
                            'mean_deviation' => $testCase->getValidMeanDeviation(),
                            'visual_field_index' => HFAEntry::VISUAL_FIELD_INDEX_MIN - 1
                        ],
                        false
                    ];
                }
            ]
        ];
    }

    /**
     * @test
     * @dataProvider getAttributeData
     * */
    public function hfa_entry_fields_validation(\Closure $providerCallback)
    {
        list($data, $expected) = $providerCallback($this);

        $instance = new HFAEntry();
        foreach ($data as $key => $value) {
            $instance->$key = $value;
        }

        $result = $instance->validate();

        $this->assertEquals($result, $expected);
    }

    /**
     * @return number
     */
    protected function getValidMeanDeviation()
    {
        return $this->faker->numberBetween(HFAEntry::MEAN_DEVIATION_MIN, HFAEntry::MEAN_DEVIATION_MAX);
    }

    /**
     * @return number
     */
    protected function getValidVisualFieldIndex()
    {
        return $this->faker->numberBetween(HFAEntry::VISUAL_FIELD_INDEX_MIN, HFAEntry::VISUAL_FIELD_INDEX_MAX);
    }
}
