<?php
/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
    * @covers OphDrPGDPSD_PGDPSD
    * @covers OphDrPGDPSD_PGDPSDMeds
    *
    * @group sample-data
    * @group pgdpsd
 */
class OphDrPGDPSD_PDGPSDMedsTest extends \ActiveRecordTestCase
{
    public function getModel()
    {
        return OphDrPGDPSD_PGDPSDMeds::model();
    }

    public function meds_data_provider()
    {
        return [
            'valid psd attributes passes' => [
                [
                    'medication_id' => rand(1, 50),
                    'pgdpsd_id' => rand(1, 2),
                    'route_id' => rand(1, 50),
                    'dose' => rand(1, 50),
                    'dose_unit_term' => 'Foo'
                ],
                'psd',
                true,
                []
            ],
            'invalid psd attributes fails' => [
                [
                    'medication_id' => 'Foo bar',
                    'pgdpsd_id' => rand(1, 2) // required to create object
                ],
                'psd',
                false,
                [
                    'medication_id',
                    'route_id',
                    'dose',
                    'dose_unit_term'
                ]
            ],
            'missing psd attributes fails' => [
                [
                    'pgdpsd_id' => rand(1, 2) // required to create object
                ],
                'psd',
                false,
                [
                    'medication_id',
                    'route_id',
                    'dose',
                    'dose_unit_term'
                ]
            ],
            'valid pgd attributes passes' => [
                [
                    'medication_id' => rand(1, 50),
                    'pgdpsd_id' => rand(1, 2),
                    'route_id' => rand(1, 50),
                    'dose' => rand(1, 50),
                    'dose_unit_term' => 'Baz',
                    'frequency_id' => rand(1, 50),
                    'duration_id' => rand(1, 50),
                    'dispense_condition_id' => rand(1, 50),
                    'dispense_location_id' => rand(1, 50),
                ],
                'pgd',
                true,
                []
            ],
            'missing pgd attributes fails' => [
                [
                    'pgdpsd_id' => 1
                ],
                'pgd',
                false,
                [
                    'medication_id',
                    'route_id',
                    'dose',
                    'dose_unit_term',
                    'frequency_id',
                    'duration_id',
                    'dispense_condition_id',
                    'dispense_location_id'
                ]
            ],
        ];
    }

    /**
     * @dataProvider meds_data_provider
     *
     * @test
     * */
    public function validation_rules_are_applied_correctly($attributes, $type, $valid, $expected_errors)
    {
        $this_med = new OphDrPGDPSD_PGDPSDMeds();
        $this_med->attributes = $attributes;
        $this_med->pgdpsd->type = $type;

        $this->assertEquals($this_med->validate(), $valid);

        if (count($expected_errors)) {
            foreach ($expected_errors as $expected_error) {
                $this->assertArrayHasKey($expected_error, $this_med->getErrors());
            }
        }
    }
}
