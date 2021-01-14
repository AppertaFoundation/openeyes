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

use OEModule\OphCiExamination\models\StrabismusManagement_Treatment;
use OEModule\OphCiExamination\models\StrabismusManagement_TreatmentOption;

/**
 * Class StrabismusManagement_TreatmentTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\StrabismusManagement_Treatment
 * @group sample-data
 * @group strabismus
 * @group strabismus-management
 */
class StrabismusManagement_TreatmentTest extends \ModelTestCase
{
    use \HasModelAssertions;
    use \WithTransactions;

    protected $element_cls = StrabismusManagement_Treatment::class;

    /** @test */
    public function has_options_relation()
    {
        $this->assertHasManyDefined(
            'options',
            StrabismusManagement_TreatmentOption::class,
            'treatment_id'
        );
    }

    /** @test */
    public function column_mapping_no_options_is_empty_array()
    {
        $treatment = $this->getElementInstance();
        $this->assertEquals([], $treatment->getOptionsByColumn());
    }

    public function optionsProvider()
    {
        return [
            [
                [['name' => 'foo', 'display_order' => 1, 'column_number' => 1]], [[['value' => 'foo']]]
            ],
            [
                [
                    ['name' => 'foo', 'display_order' => 1, 'column_number' => 1],
                    ['name' => 'bar', 'display_order' => 1, 'column_number' => 2],
                    ['name' => 'baz', 'display_order' => 2, 'column_number' => 2]
                ],
                [
                    [['value' => 'foo']],
                    [['value' => 'bar'], ['value' => 'baz']]
                ]
            ],
            [
                [
                    ['name' => 'foo', 'display_order' => 2, 'column_number' => 1],
                    ['name' => 'bar', 'display_order' => 1, 'column_number' => 2],
                    ['name' => 'baz', 'display_order' => 2, 'column_number' => 2],
                    ['name' => 'qux', 'display_order' => 1, 'column_number' => 1]
                ],
                [
                    [['value' => 'qux'], ['value' => 'foo']],
                    [['value' => 'bar'], ['value' => 'baz']]
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider optionsProvider
     */
    public function column_mapping_with_options_contains_options($options, $expected)
    {
        $treatment = new StrabismusManagement_Treatment();
        $treatment->setAttributes(['name' => 'test treatment']);
        $treatment->save();

        foreach ($options as $opt_values) {
            $opt = new StrabismusManagement_TreatmentOption();
            $opt->setAttributes($opt_values);
            $opt->treatment_id = $treatment->id;
            $opt->save();
        }

        $result = $treatment->getOptionsByColumn();
        foreach ($expected as $column => $column_expected) {
            foreach ($column_expected as $row => $row_expected) {
                foreach ($row_expected as $attr => $value) {
                    $this->assertEquals(
                        $value,
                        $result[$column][$row][$attr],
                        "no match at {$column}.{$row}.{$attr} for {$value} in " . print_r($result, true)
                    );
                }
            }
        }
    }
}
